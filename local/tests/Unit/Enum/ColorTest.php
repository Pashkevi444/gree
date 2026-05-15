<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\Color;
use PHPUnit\Framework\TestCase;

final class ColorTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame('white', Color::White->value);
        $this->assertSame('silver', Color::Silver->value);
        $this->assertSame('black', Color::Black->value);
        $this->assertSame('champagne', Color::Champagne->value);
    }

    public function testHex(): void
    {
        $this->assertSame('#ffffff', Color::White->hex());
        $this->assertSame('#8c8c8c', Color::Silver->hex());
        $this->assertSame('#000000', Color::Black->hex());
        $this->assertSame('#f5deb3', Color::Champagne->hex());
    }

    public function testLabels(): void
    {
        $this->assertSame('Белый', Color::White->label());
        $this->assertSame('Серебристый', Color::Silver->label());
        $this->assertSame('Чёрный', Color::Black->label());
        $this->assertSame('Шампань', Color::Champagne->label());
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(Color::tryFrom('gray'));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(4, Color::cases());
    }
}
