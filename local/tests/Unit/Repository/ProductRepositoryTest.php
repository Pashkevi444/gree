<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Repository;

use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\DTO\FilterDto;
use Gree\Enum\Locale;
use Gree\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repo;

    protected function setUp(): void
    {
        $language = $this->createMock(LanguageServiceInterface::class);
        $language->method('get')->willReturn(Locale::Ru);
        $offers = $this->createMock(OfferRepositoryInterface::class);
        $offers->method('getByProductIds')->willReturn([]);
        $offers->method('findProductIds')->willReturn([]);
        $this->repo = new ProductRepository($language, $offers);
    }

    public function testGetListReturnsProductCollection(): void
    {
        $this->assertInstanceOf(ProductCollection::class, $this->repo->getList(FilterDto::fromArray([])));
    }

    public function testGetListWithFiltersReturnsProductCollection(): void
    {
        $result = $this->repo->getList(FilterDto::fromArray([
            'type'          => ['wall'],
            'price'         => [10000, 500000],
            'area'          => [20, 25],
            'bestseller'    => ['yes'],
            'inverter_motor' => ['yes'],
        ]));

        $this->assertInstanceOf(ProductCollection::class, $result);
    }

    public function testGetByCodeReturnsNullWhenIblockMissing(): void
    {
        $this->assertNull($this->repo->getByCode('any-code'));
    }
}
