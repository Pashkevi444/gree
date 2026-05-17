<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\OrderItemCollection;
use Gree\DTO\OrderItemDto;

interface OrderItemRepositoryInterface
{
    /**
     * Persists one line. Returns the freshly-inserted row ID.
     * `$item->orderId` must already be set to the persisted Orders.ID.
     */
    public function insert(OrderItemDto $item): int;

    public function listByOrder(int $orderId): OrderItemCollection;
}
