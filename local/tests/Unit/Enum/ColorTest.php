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
        $this->assertSame('black', Color::Black->value);
        $this->assertSame('gold', Color::Gold->value);
        $this->assertSame('blue', Color::Blue->value);
        $this->assertSame('silver', Color::Silver->value);
    }

    public function testHex(): void
    {
        $this->assertSame('#ffffff', Color::White->hex());
        $this->assertSame('#000000', Color::Black->hex());
        $this->assertSame('#d4af37', Color::Gold->hex());
        $this->assertSame('#2f40d5', Color::Blue->hex());
        $this->assertSame('#8c8c8c', Color::Silver->hex());
    }

    public function testLabels(): void
    {
        $this->assertSame('Белый', Color::White->label());
        $this->assertSame('Чёрный', Color::Black->label());
        $this->assertSame('Золотой', Color::Gold->label());
        $this->assertSame('Синий', Color::Blue->label());
        $this->assertSame('Серебряный', Color::Silver->label());
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(Color::tryFrom('champagne'));
        $this->assertNull(Color::tryFrom('gray'));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(5, Color::cases());
    }
}
