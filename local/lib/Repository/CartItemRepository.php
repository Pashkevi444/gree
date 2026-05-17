<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Main\Type\DateTime;
use Gree\Collection\CartItemCollection;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\DTO\CartItemDto;
use Gree\Enum\HlblockCode;

final class CartItemRepository extends BaseHlblockRepository implements CartItemRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::CartItems;
    }

    public function findOne(int $cartId, int $offerId): ?CartItemDto
    {
        $row = $this->query()
            ->where('UF_CART_ID', $cartId)
            ->where('UF_OFFER_ID', $offerId)
            ->setSelect(['ID', 'UF_CART_ID', 'UF_OFFER_ID', 'UF_QUANTITY'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        return $row ? CartItemDto::fromArray($row) : null;
    }

    public function findById(int $itemId): ?CartItemDto
    {
        $row = $this->query()
            ->where('ID', $itemId)
            ->setSelect(['ID', 'UF_CART_ID', 'UF_OFFER_ID', 'UF_QUANTITY'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        return $row ? CartItemDto::fromArray($row) : null;
    }

    public function insert(int $cartId, int $offerId, int $quantity): int
    {
        $now = new DateTime();

        $result = $this->addRow([
            'UF_CART_ID'    => $cartId,
            'UF_OFFER_ID'   => $offerId,
            'UF_QUANTITY'   => max(1, $quantity),
            'UF_CREATED_AT' => $now,
            'UF_UPDATED_AT' => $now,
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to insert cart item: ' . implode('; ', $result->getErrorMessages()));
        }
        return (int) $result->getId();
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $this->updateRow($itemId, [
            'UF_QUANTITY'   => max(1, $quantity),
            'UF_UPDATED_AT' => new DateTime(),
        ]);
    }

    public function delete(int $itemId): void
    {
        $this->deleteRow($itemId);
    }

    public function listByCart(int $cartId): CartItemCollection
    {
        $result = $this->query()
            ->where('UF_CART_ID', $cartId)
            ->setSelect(['ID', 'UF_CART_ID', 'UF_OFFER_ID', 'UF_QUANTITY'])
            ->setOrder(['ID' => 'ASC'])
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = CartItemDto::fromArray($row);
        }
        return new CartItemCollection(...$items);
    }
}
