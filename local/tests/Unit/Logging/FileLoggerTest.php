<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Logging;

use Gree\Enum\LogLevel;
use Gree\Logging\FileLogger;
use PHPUnit\Framework\TestCase;

final class FileLoggerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/gree-logs-' . uniqid('', true);
        mkdir($this->dir, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($this->dir);
    }

    public function testWritesErrorAlways(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: false);
        $logger->error('boom', ['code' => 500]);

        $contents = $this->readToday();
        $this->assertStringContainsString('ERROR', $contents);
        $this->assertStringContainsString('boom', $contents);
        $this->assertStringContainsString('"code":500', $contents);
    }

    public function testSkipsDebugWhenDisabled(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: false);
        $logger->debug('verbose');

        $this->assertSame('', $this->readToday());
    }

    public function testWritesDebugWhenEnabled(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: true);
        $logger->debug('hello', ['x' => 1]);

        $this->assertStringContainsString('DEBUG', $this->readToday());
        $this->assertStringContainsString('hello', $this->readToday());
    }

    public function testCriticalIsAlwaysWritten(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: false);
        $logger->critical('meltdown');

        $this->assertStringContainsString('CRITICAL', $this->readToday());
    }

    public function testSeparateFileForWarningsAndAbove(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: true);
        $logger->error('bad');
        $logger->debug('ok');

        $today = date('Y-m-d');
        $this->assertFileExists($this->dir . '/' . $today . '.log');
        $this->assertFileExists($this->dir . '/' . $today . '-errors.log');

        $errors = file_get_contents($this->dir . '/' . $today . '-errors.log');
        $this->assertStringContainsString('bad', $errors);
        $this->assertStringNotContainsString('ok', $errors);
    }

    public function testLogWithLevelArg(): void
    {
        $logger = new FileLogger($this->dir, debugEnabled: true);
        $logger->log(LogLevel::Warning, 'careful', []);

        $this->assertStringContainsString('WARNING', $this->readToday());
    }

    private function readToday(): string
    {
        $path = $this->dir . '/' . date('Y-m-d') . '.log';
        return is_file($path) ? (string) file_get_contents($path) : '';
    }
}
