<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\GreeCardsRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\GreeCardDto;
use Gree\DTO\GreeStatDto;
use Gree\DTO\ProductDto;
use Gree\Enum\IblockCode;
use Gree\Enum\ProductType;
use Gree\Service\CatalogService;
use PHPUnit\Framework\TestCase;

final class CatalogServiceTest extends TestCase
{
    private function makeProduct(int $id = 1): ProductDto
    {
        return new ProductDto($id, "P$id", "p-$id", ProductType::Wall, 1_000_000, 30);
    }

    private function makeService(
        ?ProductRepositoryInterface $products = null,
        ?GreeCardsRepositoryInterface $cards = null,
    ): CatalogService {
        return new CatalogService(
            $products ?? $this->createMock(ProductRepositoryInterface::class),
            $cards ?? $this->createMock(GreeCardsRepositoryInterface::class),
        );
    }

    public function testGetListDelegatesToRepository(): void
    {
        $filter   = new FilterDto();
        $expected = new ProductCollection($this->makeProduct(1));

        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getList')
            ->with($filter)
            ->willReturn($expected);

        $this->assertSame($expected, $this->makeService($repo)->getList($filter));
    }

    public function testGetByCodeDelegatesToRepository(): void
    {
        $dto = $this->makeProduct(1);

        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getByCode')
            ->with('p-1')
            ->willReturn($dto);

        $this->assertSame($dto, $this->makeService($repo)->getByCode('p-1'));
    }

    public function testGetByCodeReturnsNullWhenNotFound(): void
    {
        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->method('getByCode')->willReturn(null);

        $this->assertNull($this->makeService($repo)->getByCode('nonexistent'));
    }

    public function testGetListReturnsEmptyCollectionWhenNoProducts(): void
    {
        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->method('getList')->willReturn(new ProductCollection());

        $result = $this->makeService($repo)->getList(new FilterDto());

        $this->assertInstanceOf(ProductCollection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testCountDelegatesToRepository(): void
    {
        $filter = new FilterDto();

        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('count')
            ->with($filter)
            ->willReturn(42);

        $this->assertSame(42, $this->makeService($repo)->count($filter));
    }

    public function testGetGreeCardsReadsCatalogIblock(): void
    {
        $expected = new GreeCardCollection(new GreeCardDto(1, 'Warranty', 'desc', 'thumbs-up'));

        $cards = $this->createMock(GreeCardsRepositoryInterface::class);
        $cards->expects($this->once())
            ->method('getCards')
            ->with(IblockCode::CatalogGreeCards)
            ->willReturn($expected);

        $this->assertSame($expected, $this->makeService(cards: $cards)->getGreeCards());
    }

    public function testGetGreeStatsReadsCatalogIblock(): void
    {
        $expected = new GreeStatCollection(new GreeStatDto(1, '#1', 1, '#', 'worldwide', 'desc'));

        $cards = $this->createMock(GreeCardsRepositoryInterface::class);
        $cards->expects($this->once())
            ->method('getStats')
            ->with(IblockCode::CatalogGreeStats)
            ->willReturn($expected);

        $this->assertSame($expected, $this->makeService(cards: $cards)->getGreeStats());
    }
}
