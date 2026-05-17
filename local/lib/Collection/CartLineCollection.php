<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\CartLineDto;

/** @extends BaseCollection<CartLineDto> */
final class CartLineCollection extends BaseCollection
{
    public function __construct(CartLineDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return CartLineDto::class;
    }

    public function total(): int
    {
        $sum = 0;
        foreach ($this as $line) {
            $sum += $line->totalPrice;
        }
        return $sum;
    }

    public function itemsCount(): int
    {
        $sum = 0;
        foreach ($this as $line) {
            $sum += $line->quantity;
        }
        return $sum;
    }
}
