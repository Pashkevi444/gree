<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\Color;

/**
 * One trade offer (SKU) belonging to a parent ProductDto. Combinations of
 * color × area produce separate offers, each with its own price and stock
 * state plus capacity-dependent specs.
 */
final readonly class OfferDto extends BaseDto
{
    public function __construct(
        public int $id,
        public int $productId,
        public int $price,
        public int $area,
        public ?Color $color,
        public bool $inStock = false,
        public string $coolingPower = '',
        public string $heatingPower = '',
        public string $noise = '',
        public string $indoorDimensions = '',
        public string $outdoorDimensions = '',
        public string $indoorWeight = '',
        public string $outdoorWeight = '',
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'price' => $this->price,
            'area' => $this->area,
            'color' => $this->color?->value,
            'color_hex' => $this->color?->hex(),
            'in_stock' => $this->inStock,
            'cooling_power' => $this->coolingPower,
            'heating_power' => $this->heatingPower,
            'noise' => $this->noise,
            'indoor_dimensions' => $this->indoorDimensions,
            'outdoor_dimensions' => $this->outdoorDimensions,
            'indoor_weight' => $this->indoorWeight,
            'outdoor_weight' => $this->outdoorWeight,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            productId: (int) ($data['product_id'] ?? 0),
            price: (int) ($data['price'] ?? 0),
            area: (int) ($data['area'] ?? 0),
            color: isset($data['color']) ? Color::tryFrom((string) $data['color']) : null,
            inStock: (bool) ($data['in_stock'] ?? false),
            coolingPower: (string) ($data['cooling_power'] ?? ''),
            heatingPower: (string) ($data['heating_power'] ?? ''),
            noise: (string) ($data['noise'] ?? ''),
            indoorDimensions: (string) ($data['indoor_dimensions'] ?? ''),
            outdoorDimensions: (string) ($data['outdoor_dimensions'] ?? ''),
            indoorWeight: (string) ($data['indoor_weight'] ?? ''),
            outdoorWeight: (string) ($data['outdoor_weight'] ?? ''),
        );
    }
}
