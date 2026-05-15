<?php

declare(strict_types=1);

namespace Gree\Traits;

use Gree\Enum\LogLevel;

/**
 * Boilerplate for Gree\Contract\Logging\LoggerInterface implementations: each
 * per-level method (debug/info/warning/error/critical) is just a thin delegate
 * to log(). A class that uses this trait only needs to implement
 * log(LogLevel, string, array).
 */
trait LoggerLevelMethodsTrait
{
    /**
     * @param array<string, mixed> $context
     */
    abstract public function log(LogLevel $level, string $message, array $context = []): void;

    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::Debug, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::Info, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::Warning, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::Error, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::Critical, $message, $context);
    }
}
