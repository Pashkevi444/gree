<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Core;

use Gree\Core\Env;
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/gree-env-' . uniqid('', true) . '.env';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmp)) {
            unlink($this->tmp);
        }
    }

    public function testLoadParsesSimpleKeyValues(): void
    {
        file_put_contents($this->tmp, "FOO=bar\nBAZ=42\n");
        Env::load($this->tmp);

        $this->assertSame('bar', Env::get('FOO'));
        $this->assertSame('42', Env::get('BAZ'));
    }

    public function testLoadIgnoresCommentsAndEmptyLines(): void
    {
        file_put_contents($this->tmp, "# a comment\n\nFOO=ok\n");
        Env::load($this->tmp);

        $this->assertSame('ok', Env::get('FOO'));
    }

    public function testLoadStripsQuotes(): void
    {
        file_put_contents($this->tmp, "FOO=\"hello world\"\nBAR='single quoted'\n");
        Env::load($this->tmp);

        $this->assertSame('hello world', Env::get('FOO'));
        $this->assertSame('single quoted', Env::get('BAR'));
    }

    public function testBoolHelper(): void
    {
        file_put_contents($this->tmp, "FLAG_ON=true\nFLAG_OFF=false\nFLAG_ONE=1\nFLAG_ZERO=0\n");
        Env::load($this->tmp);

        $this->assertTrue(Env::bool('FLAG_ON'));
        $this->assertFalse(Env::bool('FLAG_OFF'));
        $this->assertTrue(Env::bool('FLAG_ONE'));
        $this->assertFalse(Env::bool('FLAG_ZERO'));
    }

    public function testBoolDefault(): void
    {
        $this->assertFalse(Env::bool('NONEXISTENT_KEY'));
        $this->assertTrue(Env::bool('NONEXISTENT_KEY', true));
    }

    public function testGetDefault(): void
    {
        $this->assertSame('fallback', Env::get('NONEXISTENT_KEY', 'fallback'));
        $this->assertNull(Env::get('NONEXISTENT_KEY'));
    }

    public function testLoadIgnoresMissingFile(): void
    {
        Env::load('/no/such/path/.env');
        $this->assertNull(Env::get('SOME_KEY_NOT_THERE'));
    }
}
