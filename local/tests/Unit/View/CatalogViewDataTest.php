<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\DTO\FilterDto;
use Gree\View\BaseViewData;
use Gree\View\CatalogViewData;
use PHPUnit\Framework\TestCase;

final class CatalogViewDataTest extends TestCase
{
    public function testExtendsBaseViewData(): void
    {
        $this->assertInstanceOf(BaseViewData::class, $this->build());
    }

    public function testHoldsAllFields(): void
    {
        $products = new ProductCollection();
        $filter = new FilterDto();
        $cards = new GreeCardCollection();
        $stats = new GreeStatCollection();
        $crumbs = new BreadcrumbCollection();

        $data = new CatalogViewData($products, $filter, 42, $cards, $stats, $crumbs);

        $this->assertSame($products, $data->products);
        $this->assertSame($filter, $data->filter);
        $this->assertSame(42, $data->total);
        $this->assertSame($cards, $data->greeCards);
        $this->assertSame($stats, $data->greeStats);
        $this->assertSame($crumbs, $data->breadcrumbs);
    }

    public function testToArrayContainsExpectedKeys(): void
    {
        $array = $this->build()->toArray();

        $this->assertArrayHasKey('products', $array);
        $this->assertArrayHasKey('filter', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertArrayHasKey('greeCards', $array);
        $this->assertArrayHasKey('greeStats', $array);
        $this->assertArrayHasKey('breadcrumbs', $array);
    }

    public function testIsReadonly(): void
    {
        $data = $this->build();

        $this->expectException(\Error::class);
        $data->total = 1; // @phpstan-ignore-line
    }

    private function build(): CatalogViewData
    {
        return new CatalogViewData(
            new ProductCollection(),
            new FilterDto(),
            0,
            new GreeCardCollection(),
            new GreeStatCollection(),
            new BreadcrumbCollection(),
        );
    }
}
