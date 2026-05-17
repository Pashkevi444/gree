<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\CartItemCollection;
use Gree\DTO\CartItemDto;

interface CartItemRepositoryInterface
{
    public function findOne(int $cartId, int $offerId): ?CartItemDto;

    public function findById(int $itemId): ?CartItemDto;

    public function insert(int $cartId, int $offerId, int $quantity): int;

    public function updateQuantity(int $itemId, int $quantity): void;

    public function delete(int $itemId): void;

    public function listByCart(int $cartId): CartItemCollection;
}
