<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Storage-level row of cart_items HL-block. View-level enrichment lives in
 * {@see CartLineDto}.
 */
final readonly class CartItemDto extends BaseDto
{
    public function __construct(
        public int $id,
        public int $cartId,
        public int $offerId,
        public int $quantity,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id:       (int) ($data['ID'] ?? $data['id'] ?? 0),
            cartId:   (int) ($data['UF_CART_ID'] ?? $data['cart_id'] ?? 0),
            offerId:  (int) ($data['UF_OFFER_ID'] ?? $data['offer_id'] ?? 0),
            quantity: (int) ($data['UF_QUANTITY'] ?? $data['quantity'] ?? 0),
        );
    }
}
