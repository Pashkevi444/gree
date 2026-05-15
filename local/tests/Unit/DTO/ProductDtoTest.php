<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\ProductDto;
use Gree\Enum\Color;
use Gree\Enum\ProductType;
use PHPUnit\Framework\TestCase;

final class ProductDtoTest extends TestCase
{
    public function testConstructionWithRequiredProps(): void
    {
        $dto = new ProductDto(
            id:    1,
            name:  'Gree Pular',
            code:  'gree-pular',
            type:  ProductType::Wall,
            price: 3_500_000,
            area:  30,
        );

        $this->assertSame(1,                 $dto->id);
        $this->assertSame('Gree Pular',      $dto->name);
        $this->assertSame('gree-pular',      $dto->code);
        $this->assertSame(ProductType::Wall, $dto->type);
        $this->assertSame(3_500_000,         $dto->price);
        $this->assertSame(30,                $dto->area);
        $this->assertFalse($dto->isBestseller);
        $this->assertSame('',                $dto->image);
        $this->assertSame([],                $dto->colors);
    }

    public function testConstructionWithAllProps(): void
    {
        $dto = new ProductDto(
            id:           5,
            name:         'Gree Column',
            code:         'gree-column',
            type:         ProductType::Column,
            price:        12_000_000,
            area:         100,
            isBestseller: true,
            image:        '/images/col.png',
            colors:       [Color::White, Color::Black],
        );

        $this->assertTrue($dto->isBestseller);
        $this->assertSame('/images/col.png', $dto->image);
        $this->assertSame([Color::White, Color::Black],  $dto->colors);
    }

    public function testFromArray(): void
    {
        $dto = ProductDto::fromArray([
            'id'           => '7',
            'name'         => 'Gree Industrial',
            'code'         => 'gree-industrial',
            'type'         => 'industrial',
            'price'        => '50000000',
            'area'         => '500',
            'is_bestseller' => '1',
            'image'        => '/img/ind.png',
            'colors'       => ['white', 'silver'],
        ]);

        $this->assertSame(7,                        $dto->id);
        $this->assertSame(ProductType::Industrial,  $dto->type);
        $this->assertSame([Color::White, Color::Silver], $dto->colors);
        $this->assertSame(50_000_000,               $dto->price);
        $this->assertSame(500,                      $dto->area);
        $this->assertTrue($dto->isBestseller);
    }

    public function testFromArrayDefaults(): void
    {
        $dto = ProductDto::fromArray([
            'id'    => '1',
            'name'  => 'Min',
            'code'  => 'min',
            'type'  => 'wall',
            'price' => '100',
            'area'  => '10',
        ]);

        $this->assertFalse($dto->isBestseller);
        $this->assertSame('', $dto->image);
        $this->assertSame([], $dto->colors);
    }

    public function testToArray(): void
    {
        $dto = new ProductDto(
            id: 3,
            name: 'Gree Pular',
            code: 'gree-pular',
            type: ProductType::Wall,
            price: 3_500_000,
            area: 30,
            isBestseller: true,
            image: '/upload/img.png',
        );

        $result = $dto->toArray();

        $this->assertSame(3, $result['id']);
        $this->assertSame('Gree Pular', $result['name']);
        $this->assertSame('gree-pular', $result['code']);
        $this->assertSame('wall', $result['type']);
        $this->assertSame(3_500_000, $result['price']);
        $this->assertSame(30, $result['area']);
        $this->assertTrue($result['is_bestseller']);
        $this->assertSame('/upload/img.png', $result['image']);
    }

    public function testIsImmutable(): void
    {
        $dto = new ProductDto(1, 'Test', 'test', ProductType::Wall, 100, 10);

        $this->expectException(\Error::class);
        $dto->id = 2; // @phpstan-ignore-line
    }
}
