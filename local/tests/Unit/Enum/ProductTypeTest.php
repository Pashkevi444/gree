<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\ProductType;
use PHPUnit\Framework\TestCase;

final class ProductTypeTest extends TestCase
{
    public function testFromValidValues(): void
    {
        $this->assertSame(ProductType::Wall,       ProductType::from('wall'));
        $this->assertSame(ProductType::Column,     ProductType::from('column'));
        $this->assertSame(ProductType::Industrial, ProductType::from('industrial'));
    }

    public function testValues(): void
    {
        $this->assertSame('wall',       ProductType::Wall->value);
        $this->assertSame('column',     ProductType::Column->value);
        $this->assertSame('industrial', ProductType::Industrial->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('Настенный',      ProductType::Wall->label());
        $this->assertSame('Колонный',       ProductType::Column->label());
        $this->assertSame('Промышленный',   ProductType::Industrial->label());
    }

    public function testFromInvalidValueThrows(): void
    {
        $this->expectException(\ValueError::class);
        ProductType::from('unknown');
    }

    public function testTryFromReturnsNullOnInvalid(): void
    {
        $this->assertNull(ProductType::tryFrom('unknown'));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(3, ProductType::cases());
    }

    public function testSlugs(): void
    {
        $this->assertSame('nastennie',     ProductType::Wall->slug());
        $this->assertSame('kolonnye',      ProductType::Column->slug());
        $this->assertSame('promyshlennye', ProductType::Industrial->slug());
    }

    public function testFromSlug(): void
    {
        $this->assertSame(ProductType::Wall,       ProductType::fromSlug('nastennie'));
        $this->assertSame(ProductType::Column,     ProductType::fromSlug('kolonnye'));
        $this->assertSame(ProductType::Industrial, ProductType::fromSlug('promyshlennye'));
        $this->assertNull(ProductType::fromSlug('unknown'));
    }
}
