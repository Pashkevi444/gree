<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\HlblockCode;
use PHPUnit\Framework\TestCase;

final class HlblockCodeTest extends TestCase
{
    public function testCases(): void
    {
        $this->assertSame('Translations',         HlblockCode::Translations->value);
        $this->assertSame('Seo',                  HlblockCode::Seo->value);
        $this->assertSame('Carts',                HlblockCode::Carts->value);
        $this->assertSame('CartItems',            HlblockCode::CartItems->value);
        $this->assertSame('Orders',               HlblockCode::Orders->value);
        $this->assertSame('OrderItems',           HlblockCode::OrderItems->value);
        $this->assertSame('CatalogHelpFeedback',  HlblockCode::CatalogHelpFeedback->value);
    }

    public function testCasesCount(): void
    {
        $this->assertCount(8, HlblockCode::cases());
    }

    public function testTryFromUnknownReturnsNull(): void
    {
        $this->assertNull(HlblockCode::tryFrom('UnknownHl'));
    }
}
