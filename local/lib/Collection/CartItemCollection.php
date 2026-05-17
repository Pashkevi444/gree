<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\CartItemDto;

/** @extends BaseCollection<CartItemDto> */
final class CartItemCollection extends BaseCollection
{
    public function __construct(CartItemDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return CartItemDto::class;
    }
}
