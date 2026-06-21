<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Main\Type\DateTime;
use Gree\Contract\Repository\OrderRepositoryInterface;
use Gree\DTO\OrderDto;
use Gree\Enum\HlblockCode;

final class OrderRepository extends BaseHlblockRepository implements OrderRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Orders;
    }

    public function insert(OrderDto $order): int
    {
        $now = new DateTime();

        $result = $this->addRow([
            'UF_PUBLIC_ID'          => $order->publicId,
            'UF_CART_TOKEN'         => $order->cartToken,
            'UF_STATUS'             => $order->status->value,

            'UF_CUSTOMER_NAME'      => $order->customer->name,
            'UF_CUSTOMER_PHONE'     => $order->customer->phone,
            'UF_CUSTOMER_TELEGRAM'  => $order->customer->telegram,

            'UF_DELIVERY_CITY'      => $order->delivery->city->value,
            'UF_DELIVERY_STREET'    => $order->delivery->street,
            'UF_DELIVERY_HOUSE'     => $order->delivery->house,
            'UF_DELIVERY_APARTMENT' => $order->delivery->apartment,
            'UF_DELIVERY_COMMENT'   => $order->delivery->comment,

            'UF_PAYMENT_METHOD'     => $order->payment->value,

            'UF_TOTAL'              => $order->total,
            'UF_ITEMS_COUNT'        => $order->itemsCount,
            'UF_ITEMS_SUMMARY'      => $this->formatItemsSummary($order),

            'UF_LOCALE'             => $order->locale->value,
            'UF_IP'                 => $order->ip,
            'UF_USER_AGENT'         => $order->userAgent,

            'UF_CREATED_AT'         => $now,
            'UF_UPDATED_AT'         => $now,
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to insert order: ' . implode('; ', $result->getErrorMessages()));
        }
        return (int) $result->getId();
    }

    public function findByPublicId(string $publicId): ?OrderDto
    {
        if ($publicId === '') {
            return null;
        }

        $row = $this->query()
            ->where('UF_PUBLIC_ID', $publicId)
            ->setSelect(['*'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }

        return OrderDto::fromArray([
            'id'         => $row['ID'],
            'publicId'   => $row['UF_PUBLIC_ID'],
            'status'     => $row['UF_STATUS'],
            'cartToken'  => $row['UF_CART_TOKEN'],
            'customer'   => [
                'name'     => $row['UF_CUSTOMER_NAME'],
                'phone'    => $row['UF_CUSTOMER_PHONE'],
                'telegram' => $row['UF_CUSTOMER_TELEGRAM'],
            ],
            'delivery'   => [
                'city'      => $row['UF_DELIVERY_CITY'],
                'street'    => $row['UF_DELIVERY_STREET'],
                'house'     => $row['UF_DELIVERY_HOUSE'],
                'apartment' => $row['UF_DELIVERY_APARTMENT'],
                'comment'   => $row['UF_DELIVERY_COMMENT'],
            ],
            'payment'    => $row['UF_PAYMENT_METHOD'],
            'total'      => $row['UF_TOTAL'],
            'itemsCount' => $row['UF_ITEMS_COUNT'],
            'locale'     => $row['UF_LOCALE'],
            'ip'         => $row['UF_IP'],
            'userAgent'  => $row['UF_USER_AGENT'],
            'createdAt'  => $row['UF_CREATED_AT'] instanceof DateTime ? $row['UF_CREATED_AT']->format('Y-m-d H:i:s') : (string) $row['UF_CREATED_AT'],
        ]);
    }

    /** Текстовый summary для UF_ITEMS_SUMMARY: «Gree BORA X 07 (Белый, 30 м²) × 2 — 4 000 000 UZS» по строке на позицию. */
    private function formatItemsSummary(OrderDto $order): string
    {
        $lines = [];
        foreach ($order->items as $item) {
            $meta = [];
            if ($item->color !== null) {
                $meta[] = $item->color->label();
            }
            if ($item->area > 0) {
                $meta[] = $item->area . ' м²';
            }
            $metaPart = $meta ? ' (' . implode(', ', $meta) . ')' : '';
            $lines[] = sprintf(
                '%s%s × %d — %s UZS',
                $item->productName,
                $metaPart,
                $item->quantity,
                number_format($item->totalPrice, 0, '.', ' '),
            );
        }
        return implode("\n", $lines);
    }

    public function publicIdExists(string $publicId): bool
    {
        if ($publicId === '') {
            return false;
        }
        $row = $this->query()
            ->where('UF_PUBLIC_ID', $publicId)
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        return (bool) $row;
    }
}
