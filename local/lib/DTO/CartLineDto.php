<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\Color;

/**
 * View-level enriched cart line. Combines a CartItem row with its parent
 * product/offer metadata so the cart template renders without further joins.
 */
final readonly class CartLineDto extends BaseDto
{
    public function __construct(
        public int $id,
        public int $offerId,
        public int $productId,
        public string $productName,
        public string $productCode,
        public string $image,
        public string $productUrl,
        public int $unitPrice,
        public int $quantity,
        public int $totalPrice,
        public int $area,
        public ?Color $color,
        public bool $inStock,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id:          (int) ($data['id'] ?? 0),
            offerId:     (int) ($data['offer_id'] ?? 0),
            productId:   (int) ($data['product_id'] ?? 0),
            productName: (string) ($data['product_name'] ?? ''),
            productCode: (string) ($data['product_code'] ?? ''),
            image:       (string) ($data['image'] ?? ''),
            productUrl:  (string) ($data['product_url'] ?? ''),
            unitPrice:   (int) ($data['unit_price'] ?? 0),
            quantity:    (int) ($data['quantity'] ?? 0),
            totalPrice:  (int) ($data['total_price'] ?? 0),
            area:        (int) ($data['area'] ?? 0),
            color:       isset($data['color']) ? (Color::tryFrom((string) $data['color']) ?? null) : null,
            inStock:     (bool) ($data['in_stock'] ?? false),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJson(): array
    {
        return [
            'id'           => $this->id,
            'offer_id'     => $this->offerId,
            'product_id'   => $this->productId,
            'product_name' => $this->productName,
            'product_code' => $this->productCode,
            'product_url'  => $this->productUrl,
            'image'        => $this->image,
            'unit_price'   => $this->unitPrice,
            'quantity'     => $this->quantity,
            'total_price'  => $this->totalPrice,
            'area'         => $this->area,
            'color'        => $this->color?->value,
            'color_hex'    => $this->color?->hex(),
            'in_stock'     => $this->inStock,
        ];
    }
}
