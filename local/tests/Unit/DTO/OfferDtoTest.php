<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\OfferDto;
use Gree\Enum\Color;
use PHPUnit\Framework\TestCase;

final class OfferDtoTest extends TestCase
{
    public function testConstructionWithGalleryDefaultsToEmptyArray(): void
    {
        $dto = new OfferDto(
            id:        88,
            productId: 17,
            price:     1000000,
            area:      30,
            color:     Color::White,
        );

        $this->assertSame([], $dto->gallery);
        $this->assertSame('', $dto->code);
    }

    public function testGalleryRoundtripsThroughToArrayFromArray(): void
    {
        $gallery = [
            '/dist/images/a.png',
            '/dist/images/b.png',
            '/dist/images/c.png',
        ];

        $dto = new OfferDto(
            id:        88,
            productId: 17,
            price:     1000000,
            area:      30,
            color:     Color::White,
            gallery:   $gallery,
            code:      'GWH12RPLA-K3NNA1B',
        );

        $arr = $dto->toArray();
        $this->assertSame($gallery, $arr['gallery']);
        $this->assertSame('GWH12RPLA-K3NNA1B', $arr['code']);

        $restored = OfferDto::fromArray($arr);
        $this->assertSame($gallery, $restored->gallery);
        $this->assertSame('GWH12RPLA-K3NNA1B', $restored->code);
    }

    public function testFromArrayCoercesMixedGalleryEntriesToStrings(): void
    {
        $restored = OfferDto::fromArray([
            'id' => 88,
            'product_id' => 17,
            'price' => 1000,
            'area' => 20,
            'color' => 'white',
            'gallery' => [123, '/x.png', null],
        ]);

        $this->assertSame(['123', '/x.png', ''], $restored->gallery);
    }

    public function testFromArrayMissingGalleryGivesEmpty(): void
    {
        $restored = OfferDto::fromArray([
            'id' => 1, 'product_id' => 1, 'price' => 1, 'area' => 1, 'color' => null,
        ]);
        $this->assertSame([], $restored->gallery);
        $this->assertSame('', $restored->code);
    }
}
