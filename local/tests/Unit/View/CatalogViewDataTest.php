<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\View;

use Gree\Collection\ProductCollection;
use Gree\DTO\FilterDto;
use Gree\View\BaseViewData;
use Gree\View\CatalogViewData;
use PHPUnit\Framework\TestCase;

final class CatalogViewDataTest extends TestCase
{
    public function testExtendsBaseViewData(): void
    {
        $data = new CatalogViewData(new ProductCollection(), new FilterDto(), 0);

        $this->assertInstanceOf(BaseViewData::class, $data);
    }

    public function testHoldsProductsFilterAndTotal(): void
    {
        $products = new ProductCollection();
        $filter = new FilterDto();

        $data = new CatalogViewData($products, $filter, 42);

        $this->assertSame($products, $data->products);
        $this->assertSame($filter, $data->filter);
        $this->assertSame(42, $data->total);
    }

    public function testToArrayContainsExpectedKeys(): void
    {
        $data = new CatalogViewData(new ProductCollection(), new FilterDto(), 10);

        $array = $data->toArray();

        $this->assertArrayHasKey('products', $array);
        $this->assertArrayHasKey('filter', $array);
        $this->assertArrayHasKey('total', $array);
    }

    public function testIsReadonly(): void
    {
        $data = new CatalogViewData(new ProductCollection(), new FilterDto(), 0);

        $this->expectException(\Error::class);
        $data->total = 1; // @phpstan-ignore-line
    }
}
