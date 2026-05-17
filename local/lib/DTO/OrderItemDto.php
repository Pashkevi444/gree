<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\Color;

/**
 * Snapshot of one cart line at order-placement time. Once persisted, this row
 * is immutable from the catalog's point of view — renaming a product or
 * changing its price does NOT rewrite this DTO.
 */
final readonly class OrderItemDto extends BaseDto
{
    public function __construct(
        public int $offerId,
        public string $productName,
        public string $productCode,
        public int $area,
        public ?Color $color,
        public int $quantity,
        public int $unitPrice,
        public int $totalPrice,
        public ?int $id = null,
        public ?int $orderId = null,
    ) {}

    public static function fromArray(array $data): static
    {
        $color = isset($data['UF_OFFER_COLOR'])
            ? Color::tryFrom((string) $data['UF_OFFER_COLOR'])
            : (isset($data['color']) ? Color::tryFrom((string) $data['color']) : null);

        return new self(
            offerId:     (int) ($data['UF_OFFER_ID'] ?? $data['offer_id'] ?? 0),
            productName: (string) ($data['UF_PRODUCT_NAME'] ?? $data['product_name'] ?? ''),
            productCode: (string) ($data['UF_PRODUCT_CODE'] ?? $data['product_code'] ?? ''),
            area:        (int) ($data['UF_OFFER_AREA'] ?? $data['area'] ?? 0),
            color:       $color,
            quantity:    (int) ($data['UF_QUANTITY'] ?? $data['quantity'] ?? 0),
            unitPrice:   (int) ($data['UF_UNIT_PRICE'] ?? $data['unit_price'] ?? 0),
            totalPrice:  (int) ($data['UF_TOTAL'] ?? $data['total_price'] ?? 0),
            id:          isset($data['ID']) ? (int) $data['ID'] : (isset($data['id']) ? (int) $data['id'] : null),
            orderId:     isset($data['UF_ORDER_ID']) ? (int) $data['UF_ORDER_ID'] : (isset($data['order_id']) ? (int) $data['order_id'] : null),
        );
    }
}
