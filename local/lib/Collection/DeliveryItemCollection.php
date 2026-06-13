<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\DeliveryItemDto;

/** @extends BaseCollection<DeliveryItemDto> */
final class DeliveryItemCollection extends BaseCollection
{
    public function __construct(DeliveryItemDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return DeliveryItemDto::class;
    }
}
