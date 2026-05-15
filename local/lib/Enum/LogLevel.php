<?php

declare(strict_types=1);

namespace Gree\Enum;

enum LogLevel: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';

    public function priority(): int
    {
        return match ($this) {
            self::Debug => 10,
            self::Info => 20,
            self::Warning => 30,
            self::Error => 40,
            self::Critical => 50,
        };
    }
}
