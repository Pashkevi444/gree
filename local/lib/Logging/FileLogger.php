<?php

declare(strict_types=1);

namespace Gree\Logging;

use Gree\Contract\Logging\LoggerInterface;
use Gree\Core\Env;
use Gree\Enum\LogLevel;
use Gree\Traits\LoggerLevelMethodsTrait;

/**
 * Singleton file logger.
 *
 * Files:
 *   logs/{YYYY-MM-DD}.log         — combined timeline of every level
 *   logs/{YYYY-MM-DD}-errors.log  — duplicate stream of warning+ for fast scanning
 *
 * Configuration is read from the .env file:
 *   LOG_DIR=local/logs   (relative to project root, or absolute)
 *   LOG_DEBUG=false      (debug messages are dropped unless true)
 *
 * Info / Warning / Error / Critical are always written. Info-level logging
 * isn't used on this project; if you add it later the call will just write.
 */
final class FileLogger implements LoggerInterface
{
    use LoggerLevelMethodsTrait;

    private static ?self $instance = null;

    public function __construct(
        private readonly string $directory,
        private readonly bool $debugEnabled = false,
    ) {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = self::createFromEnv();
        }
        return self::$instance;
    }

    /**
     * Override the singleton — used by tests to inject a deterministic instance.
     * Pass null to force the next getInstance() to rebuild from env.
     */
    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    private static function createFromEnv(): self
    {
        $configured = Env::get('LOG_DIR');
        $directory = $configured !== null && $configured !== ''
            ? (str_starts_with($configured, '/') ? $configured : dirname(__DIR__, 3) . '/' . ltrim($configured, '/'))
            : dirname(__DIR__, 2) . '/logs';

        return new self(
            directory: $directory,
            debugEnabled: Env::bool('LOG_DEBUG', false),
        );
    }

    public function log(LogLevel $level, string $message, array $context = []): void
    {
        if ($level === LogLevel::Debug && !$this->debugEnabled) {
            return;
        }

        $line = $this->format($level, $message, $context);
        $today = date('Y-m-d');

        $this->write($this->directory . '/' . $today . '.log', $line);

        // Hot-path file for ops: warnings and above duplicated into a dedicated file.
        if ($level->priority() >= LogLevel::Warning->priority()) {
            $this->write($this->directory . '/' . $today . '-errors.log', $line);
        }
    }

    private function format(LogLevel $level, string $message, array $context): string
    {
        $timestamp = date('c');
        $upper = strtoupper($level->value);

        // \Throwable serialises as a structured payload — full string trace stays out
        // of context so JSON encoding doesn't blow up.
        $normalized = [];
        foreach ($context as $key => $value) {
            if ($value instanceof \Throwable) {
                $normalized[$key] = [
                    'class' => $value::class,
                    'message' => $value->getMessage(),
                    'code' => $value->getCode(),
                    'file' => $value->getFile() . ':' . $value->getLine(),
                ];
            } else {
                $normalized[$key] = $value;
            }
        }

        $payload = $normalized ? json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        return sprintf("%s | %-8s | %s%s\n", $timestamp, $upper, $message, $payload !== '' ? ' | ' . $payload : '');
    }

    private function write(string $path, string $line): void
    {
        if (!is_dir($this->directory)) {
            @mkdir($this->directory, 0775, true);
        }
        // LOCK_EX so concurrent fpm workers don't interleave half-written lines.
        @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }
}
