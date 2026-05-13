<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\SortField;
use PHPUnit\Framework\TestCase;

final class SortFieldTest extends TestCase
{
    public function testFromValidValues(): void
    {
        $this->assertSame(SortField::Popular,   SortField::from('popular'));
        $this->assertSame(SortField::PriceAsc,  SortField::from('price_asc'));
        $this->assertSame(SortField::PriceDesc, SortField::from('price_desc'));
    }

    public function testDefault(): void
    {
        $this->assertSame(SortField::Popular, SortField::default());
    }

    public function testFromInvalidValueThrows(): void
    {
        $this->expectException(\ValueError::class);
        SortField::from('invalid_sort');
    }
}
