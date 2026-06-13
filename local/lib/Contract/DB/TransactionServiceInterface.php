<?php

declare(strict_types=1);

namespace Gree\Contract\DB;

/** Транзакции с вложенностью через SAVEPOINT; устойчиво к implicit rollback MySQL (deadlock/timeout/constraint). Идиома — run() с замыканием. */
interface TransactionServiceInterface
{
    public function startTransaction(): void;
    public function commitTransaction(): void;
    public function rollbackTransaction(): void;

    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function run(callable $callback): mixed;
}
