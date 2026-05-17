<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Main\Type\DateTime;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Enum\HlblockCode;

final class CartRepository extends BaseHlblockRepository implements CartRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Carts;
    }

    public function findIdByToken(string $token): ?int
    {
        $row = $this->query()
            ->where('UF_TOKEN', $token)
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        return $row ? (int) $row['ID'] : null;
    }

    public function createWithToken(string $token): int
    {
        $now = new DateTime();

        $result = $this->addRow([
            'UF_TOKEN'      => $token,
            'UF_CREATED_AT' => $now,
            'UF_UPDATED_AT' => $now,
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to create cart: ' . implode('; ', $result->getErrorMessages()));
        }
        return (int) $result->getId();
    }

    public function touch(int $cartId): void
    {
        $this->updateRow($cartId, [
            'UF_UPDATED_AT' => new DateTime(),
        ]);
    }
}
