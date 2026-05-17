<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\OrderItemDto;

/** @extends BaseCollection<OrderItemDto> */
final class OrderItemCollection extends BaseCollection
{
    public function __construct(OrderItemDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return OrderItemDto::class;
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
