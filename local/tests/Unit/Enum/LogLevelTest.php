<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\LogLevel;
use PHPUnit\Framework\TestCase;

final class LogLevelTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame('debug', LogLevel::Debug->value);
        $this->assertSame('info', LogLevel::Info->value);
        $this->assertSame('warning', LogLevel::Warning->value);
        $this->assertSame('error', LogLevel::Error->value);
        $this->assertSame('critical', LogLevel::Critical->value);
    }

    public function testPriorityOrders(): void
    {
        $this->assertGreaterThan(LogLevel::Debug->priority(), LogLevel::Info->priority());
        $this->assertGreaterThan(LogLevel::Info->priority(), LogLevel::Warning->priority());
        $this->assertGreaterThan(LogLevel::Warning->priority(), LogLevel::Error->priority());
        $this->assertGreaterThan(LogLevel::Error->priority(), LogLevel::Critical->priority());
    }

    public function testCasesCount(): void
    {
        $this->assertCount(5, LogLevel::cases());
    }
}
