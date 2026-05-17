<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\DTO\OrderDto;

interface OrderRepositoryInterface
{
    /**
     * Persists the order header. Returns the freshly-inserted row ID.
     * The DTO's `items` collection is NOT inserted here — that's the cart-line
     * snapshot saved separately via OrderItemRepository.
     */
    public function insert(OrderDto $order): int;

    /**
     * Public-id-keyed lookup for the success page. Returns null if not found.
     */
    public function findByPublicId(string $publicId): ?OrderDto;

    public function publicIdExists(string $publicId): bool;
}
