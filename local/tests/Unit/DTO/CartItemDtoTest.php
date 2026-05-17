<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\CartItemDto;
use PHPUnit\Framework\TestCase;

final class CartItemDtoTest extends TestCase
{
    public function testConstruction(): void
    {
        $dto = new CartItemDto(id: 42, cartId: 7, offerId: 88, quantity: 3);

        $this->assertSame(42, $dto->id);
        $this->assertSame(7, $dto->cartId);
        $this->assertSame(88, $dto->offerId);
        $this->assertSame(3, $dto->quantity);
    }

    public function testFromArrayCastsValues(): void
    {
        $dto = CartItemDto::fromArray([
            'ID'          => '42',
            'UF_CART_ID'  => '7',
            'UF_OFFER_ID' => '88',
            'UF_QUANTITY' => '3',
        ]);

        $this->assertSame(42, $dto->id);
        $this->assertSame(7, $dto->cartId);
        $this->assertSame(88, $dto->offerId);
        $this->assertSame(3, $dto->quantity);
    }
}
