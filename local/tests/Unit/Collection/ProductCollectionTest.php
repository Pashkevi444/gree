<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\ProductCollection;
use Gree\DTO\ProductDto;
use Gree\Enum\ProductType;
use PHPUnit\Framework\TestCase;

final class ProductCollectionTest extends TestCase
{
    private function makeProduct(int $id = 1, ProductType $type = ProductType::Wall): ProductDto
    {
        return new ProductDto(
            id:    $id,
            name:  "Product $id",
            code:  "product-$id",
            type:  $type,
            price: 1_000_000 * $id,
            area:  30,
        );
    }

    public function testEmptyCollection(): void
    {
        $c = new ProductCollection();

        $this->assertCount(0, $c);
        $this->assertTrue($c->isEmpty());
    }

    public function testConstructWithVariadicItems(): void
    {
        $c = new ProductCollection(
            $this->makeProduct(1),
            $this->makeProduct(2),
            $this->makeProduct(3),
        );

        $this->assertCount(3, $c);
        $this->assertFalse($c->isEmpty());
    }

    public function testAddValidItem(): void
    {
        $c = new ProductCollection();
        $c->add($this->makeProduct(1));

        $this->assertCount(1, $c);
    }

    public function testAddInvalidItemThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new ProductCollection();
        $c->add('not a product');
    }

    public function testAddNonDtoObjectThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new ProductCollection();
        $c->add(new \stdClass());
    }

    public function testFilterByType(): void
    {
        $c = new ProductCollection(
            $this->makeProduct(1, ProductType::Wall),
            $this->makeProduct(2, ProductType::Column),
            $this->makeProduct(3, ProductType::Wall),
        );

        $walls = $c->filter(fn(ProductDto $p) => $p->type === ProductType::Wall);

        $this->assertCount(2, $walls);
    }

    public function testContains(): void
    {
        $product = $this->makeProduct(1);
        $c       = new ProductCollection($product);

        $this->assertTrue($c->contains($product));
        $this->assertFalse($c->contains($this->makeProduct(2)));
    }

    public function testToArray(): void
    {
        $p1 = $this->makeProduct(1);
        $p2 = $this->makeProduct(2);
        $c  = new ProductCollection($p1, $p2);

        $arr = $c->toArray();

        $this->assertIsArray($arr);
        $this->assertCount(2, $arr);
        $this->assertContains($p1, $arr);
        $this->assertContains($p2, $arr);
    }

    public function testIteratable(): void
    {
        $c = new ProductCollection(
            $this->makeProduct(1),
            $this->makeProduct(2),
        );

        $ids = [];
        foreach ($c as $product) {
            $ids[] = $product->id;
        }

        $this->assertSame([1, 2], $ids);
    }
}
