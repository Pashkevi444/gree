<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Collection\OfferCollection;
use Gree\Enum\Color;
use Gree\Enum\ProductType;

/**
 * Detail-only fields (sku/model/specs/text-tabs/functions/gallery) default to
 * empty/zero so the same DTO can be returned from the catalog listing without
 * paying for the extra joins. Only fetchByCode() fills them.
 */
final readonly class ProductDto extends BaseDto
{
    /**
     * @param Color[]  $colors    typed color options (admin-selectable variants)
     * @param string[] $functions feature codes (wifi/130v/turbo/...), labels via HL
     * @param string[] $gallery   image URLs for the detail carousel
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $code,
        public ProductType $type,
        public int $price,
        public int $area,
        public bool $isBestseller = false,
        public bool $isInverter = false,
        public string $image = '',
        public array $colors = [],
        // ── detail page only ───────────────────────────────────────────────
        public string $description = '',
        public string $sku = '',
        public string $model = '',
        public string $energyClass = '',
        public string $refrigerant = '',
        public bool $inStock = false,
        public string $coolingPower = '',
        public string $heatingPower = '',
        public string $noise = '',
        public string $indoorDimensions = '',
        public string $outdoorDimensions = '',
        public string $indoorWeight = '',
        public string $outdoorWeight = '',
        public string $warrantyText = '',
        public string $kitText = '',
        public string $installationText = '',
        public array $functions = [],
        public array $gallery = [],
        public ?OfferCollection $offers = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type->value,
            'price' => $this->price,
            'area' => $this->area,
            'is_bestseller' => $this->isBestseller,
            'image' => $this->image,
            // colors serialised as hex strings for JSON consumers.
            'colors' => array_map(fn(Color $c) => $c->hex(), $this->colors),
        ];
    }

    public static function fromArray(array $data): static
    {
        $colors = [];
        foreach ((array) ($data['colors'] ?? []) as $raw) {
            // accept either Color, the enum's string value, or a hex code
            if ($raw instanceof Color) {
                $colors[] = $raw;
                continue;
            }
            $maybe = Color::tryFrom((string) $raw);
            if ($maybe !== null) {
                $colors[] = $maybe;
            }
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            code: (string) ($data['code'] ?? ''),
            type: ProductType::from((string) ($data['type'] ?? ProductType::Wall->value)),
            price: (int) ($data['price'] ?? 0),
            area: (int) ($data['area'] ?? 0),
            isBestseller: (bool) ($data['is_bestseller'] ?? false),
            image: (string) ($data['image'] ?? ''),
            colors: $colors,
        );
    }
}
