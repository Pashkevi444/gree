<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\Enum\ProductType;
use Gree\Service\CatalogService;
use PHPUnit\Framework\TestCase;

final class CatalogServiceTest extends TestCase
{
    private function makeProduct(int $id = 1): ProductDto
    {
        return new ProductDto($id, "P$id", "p-$id", ProductType::Wall, 1_000_000, 30);
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

        $service = new CatalogService($repo);

        $this->assertSame($expected, $service->getList($filter));
    }

    public function testGetByCodeDelegatesToRepository(): void
    {
        $dto = $this->makeProduct(1);

        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getByCode')
            ->with('p-1')
            ->willReturn($dto);

        $service = new CatalogService($repo);

        $this->assertSame($dto, $service->getByCode('p-1'));
    }

    public function testGetByCodeReturnsNullWhenNotFound(): void
    {
        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->method('getByCode')->willReturn(null);

        $service = new CatalogService($repo);

        $this->assertNull($service->getByCode('nonexistent'));
    }

    public function testGetListReturnsEmptyCollectionWhenNoProducts(): void
    {
        $repo = $this->createMock(ProductRepositoryInterface::class);
        $repo->method('getList')->willReturn(new ProductCollection());

        $service = new CatalogService($repo);
        $result  = $service->getList(new FilterDto());

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

        $service = new CatalogService($repo);

        $this->assertSame(42, $service->count($filter));
    }
}
