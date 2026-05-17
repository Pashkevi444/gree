<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

interface CartRepositoryInterface
{
    public function findIdByToken(string $token): ?int;

    public function createWithToken(string $token): int;

    public function touch(int $cartId): void;
}
