<?php

declare(strict_types=1);

namespace Gree\DB;

use Bitrix\Main\Application;
use Bitrix\Main\Db\SqlQueryException;
use Gree\Contract\DB\TransactionServiceInterface;
use Gree\Logging\FileLogger;

/** Вложенные транзакции через SAVEPOINT (MySQL нативно не умеет) + обработка неявного rollback при deadlock/timeout. */
final class TransactionService extends BaseDbService implements TransactionServiceInterface
{
    /** @var array<string, string> stack of savepoint names */
    private array $points = [];

    /** @var array<string, array<string, mixed>> debug trace per savepoint */
    private array $debugStartLog = [];

    private static ?self $instance = null;

    private function __construct() {}

    private function __clone() {}
    /** @phpstan-ignore-next-line — singleton must not be unserialised */
    public function __wakeup() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function __destruct()
    {
        // Незакрытые транзакции на конец запроса — это всегда баг. Логируем,
        // но не пытаемся откатить: коннекшен может уже не существовать.
        if (!empty($this->points)) {
            try {
                FileLogger::getInstance()->critical('TransactionService destructed with open savepoints', [
                    'openPoints' => array_values($this->points),
                    'debugLog'   => $this->debugStartLog,
                ]);
            } catch (\Throwable) {
                // Логирование на этапе shutdown может уже не работать.
            }
        }
    }

    public function startTransaction(): void
    {
        if (!$this->isStarted()) {
            Application::getConnection()->query('START TRANSACTION');
        }

        $point = $this->getNewPoint();
        Application::getConnection()->query("SAVEPOINT {$point}");

        $this->points[$point] = $point;
        $this->debugStartLog[$point] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? [];
    }

    public function commitTransaction(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $point = array_pop($this->points);

        try {
            Application::getConnection()->query("RELEASE SAVEPOINT {$point}");
        } catch (SqlQueryException $e) {
            $this->resetStateOnImplicitRollback($point, $e);
            $this->safeQuery('COMMIT');
            return;
        }

        if (empty($this->points)) {
            Application::getConnection()->query('COMMIT');
        }

        unset($this->debugStartLog[$point]);
    }

    public function rollbackTransaction(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $point = array_pop($this->points);

        try {
            Application::getConnection()->query("ROLLBACK TO SAVEPOINT {$point}");
        } catch (SqlQueryException $e) {
            $this->resetStateOnImplicitRollback($point, $e);
            $this->safeQuery('ROLLBACK');
            return;
        }

        if (empty($this->points)) {
            Application::getConnection()->query('ROLLBACK');
        }

        unset($this->debugStartLog[$point]);
    }

    public function run(callable $callback): mixed
    {
        $this->startTransaction();
        try {
            $result = $callback();
            $this->commitTransaction();
            return $result;
        } catch (\Throwable $e) {
            try {
                $this->rollbackTransaction();
            } catch (\Throwable $rollbackException) {
                FileLogger::getInstance()->critical('TransactionService rollback failed', [
                    'original'         => $e->getMessage(),
                    'rollbackError'    => $rollbackException->getMessage(),
                ]);
            }
            throw $e;
        }
    }

    private function isStarted(): bool
    {
        return !empty($this->points);
    }

    private function getNewPoint(): string
    {
        return 'sp' . (count($this->points) + 1);
    }

    /** Deadlock/timeout/constraint в MySQL уничтожают все savepoint'ы — синхронизируем своё состояние. */
    private function resetStateOnImplicitRollback(string $point, SqlQueryException $e): void
    {
        $lostPoints = array_merge([$point], array_values($this->points));

        try {
            FileLogger::getInstance()->critical(
                'TransactionService: savepoint missing — MySQL did an implicit rollback',
                [
                    'failedPoint' => $point,
                    'lostPoints'  => $lostPoints,
                    'debugLog'    => $this->debugStartLog,
                    'mysqlError'  => $e->getMessage(),
                ],
            );
        } catch (\Throwable) {
            // Логгер не должен мешать восстановлению состояния транзакции.
        }

        $this->points = [];
        $this->debugStartLog = [];
    }

    /** После implicit rollback коннекшен в autocommit — повторный ROLLBACK/COMMIT не пройдёт, и это нормально. */
    private function safeQuery(string $sql): void
    {
        try {
            Application::getConnection()->query($sql);
        } catch (SqlQueryException) {
            // Ожидаемо после неявного rollback от MySQL.
        }
    }
}
