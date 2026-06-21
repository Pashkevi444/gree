<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\DTO\OrderDto;

interface OrderRepositoryInterface
{
    /** Шапка заказа; items вставляются отдельно через OrderItemRepository. */
    public function insert(OrderDto $order): int;

    /** Связка Orders.UF_ITEM_IDS ← массив OrderItems.ID после insert позиций. */
    public function setItemIds(int $orderId, array $itemIds): void;

    public function findByPublicId(string $publicId): ?OrderDto;

    public function publicIdExists(string $publicId): bool;
}
