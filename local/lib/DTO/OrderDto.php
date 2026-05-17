<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Collection\OrderItemCollection;
use Gree\Enum\Locale;
use Gree\Enum\OrderStatus;
use Gree\Enum\PaymentMethod;

/**
 * Aggregate root for an order — header fields + line items collection.
 *
 * Built three ways:
 *   1. From a fresh checkout submission (no id/publicId yet) before persist.
 *   2. From a persisted row + items query (display on success page).
 *   3. From admin/manager tooling (future).
 */
final readonly class OrderDto extends BaseDto
{
    public function __construct(
        public string $publicId,
        public OrderStatus $status,
        public OrderCustomerDto $customer,
        public OrderDeliveryDto $delivery,
        public PaymentMethod $payment,
        public OrderItemCollection $items,
        public int $total,
        public int $itemsCount,
        public Locale $locale,
        public ?int $id = null,
        public string $cartToken = '',
        public string $ip = '',
        public string $userAgent = '',
        public string $createdAt = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            publicId:   (string) ($data['publicId'] ?? $data['UF_PUBLIC_ID'] ?? ''),
            status:     OrderStatus::tryFrom((string) ($data['status'] ?? $data['UF_STATUS'] ?? 'new')) ?? OrderStatus::New,
            customer:   OrderCustomerDto::fromArray($data['customer'] ?? []),
            delivery:   OrderDeliveryDto::fromArray($data['delivery'] ?? []),
            payment:    PaymentMethod::tryFrom((string) ($data['payment'] ?? $data['UF_PAYMENT_METHOD'] ?? 'card')) ?? PaymentMethod::Card,
            items:      $data['items'] ?? new OrderItemCollection(),
            total:      (int) ($data['total'] ?? $data['UF_TOTAL'] ?? 0),
            itemsCount: (int) ($data['itemsCount'] ?? $data['UF_ITEMS_COUNT'] ?? 0),
            locale:     Locale::tryFrom((string) ($data['locale'] ?? $data['UF_LOCALE'] ?? 'ru')) ?? Locale::Ru,
            id:         isset($data['id']) ? (int) $data['id'] : (isset($data['ID']) ? (int) $data['ID'] : null),
            cartToken:  (string) ($data['cartToken'] ?? $data['UF_CART_TOKEN'] ?? ''),
            ip:         (string) ($data['ip'] ?? $data['UF_IP'] ?? ''),
            userAgent:  (string) ($data['userAgent'] ?? $data['UF_USER_AGENT'] ?? ''),
            createdAt:  (string) ($data['createdAt'] ?? ''),
        );
    }
}
