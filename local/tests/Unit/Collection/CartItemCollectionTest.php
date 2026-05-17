<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Collection;

use Gree\Collection\CartItemCollection;
use Gree\DTO\CartItemDto;
use PHPUnit\Framework\TestCase;

final class CartItemCollectionTest extends TestCase
{
    public function testAcceptsCartItemDtos(): void
    {
        $a = new CartItemDto(id: 1, cartId: 1, offerId: 10, quantity: 1);
        $b = new CartItemDto(id: 2, cartId: 1, offerId: 11, quantity: 2);
        $coll = new CartItemCollection($a, $b);

        $this->assertSame(2, $coll->count());
        $this->assertSame($a, $coll->first());
    }

    public function testAddRejectsWrongType(): void
    {
        $coll = new CartItemCollection();
        $this->expectException(\InvalidArgumentException::class);
        /** @phpstan-ignore-next-line */
        $coll->add(new \stdClass());
    }
}
