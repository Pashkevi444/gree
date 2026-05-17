<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Main\Type\DateTime;
use Gree\Collection\OrderItemCollection;
use Gree\Contract\Repository\OrderItemRepositoryInterface;
use Gree\DTO\OrderItemDto;
use Gree\Enum\HlblockCode;

final class OrderItemRepository extends BaseHlblockRepository implements OrderItemRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::OrderItems;
    }

    public function insert(OrderItemDto $item): int
    {
        if ($item->orderId === null || $item->orderId <= 0) {
            throw new \InvalidArgumentException('OrderItem.orderId is required before insert');
        }

        $result = $this->addRow([
            'UF_ORDER_ID'     => $item->orderId,
            'UF_OFFER_ID'     => $item->offerId,
            'UF_PRODUCT_NAME' => $item->productName,
            'UF_PRODUCT_CODE' => $item->productCode,
            'UF_OFFER_AREA'   => $item->area,
            'UF_OFFER_COLOR'  => $item->color?->value ?? '',
            'UF_QUANTITY'     => $item->quantity,
            'UF_UNIT_PRICE'   => $item->unitPrice,
            'UF_TOTAL'        => $item->totalPrice,
            'UF_CREATED_AT'   => new DateTime(),
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to insert order item: ' . implode('; ', $result->getErrorMessages()));
        }
        return (int) $result->getId();
    }

    public function listByOrder(int $orderId): OrderItemCollection
    {
        $result = $this->query()
            ->where('UF_ORDER_ID', $orderId)
            ->setSelect(['*'])
            ->setOrder(['ID' => 'ASC'])
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = OrderItemDto::fromArray($row);
        }
        return new OrderItemCollection(...$items);
    }
}
