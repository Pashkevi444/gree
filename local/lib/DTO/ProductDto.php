<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\ProductType;

final readonly class ProductDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $code,
        public ProductType $type,
        public int $price,
        public int $area,
        public bool $isBestseller = false,
        public string $image = '',
        public array $colors = [],
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
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            code: (string) $data['code'],
            type: ProductType::from((string) $data['type']),
            price: (int) $data['price'],
            area: (int) $data['area'],
            isBestseller: (bool) ($data['is_bestseller'] ?? false),
            image: (string) ($data['image'] ?? ''),
            colors: (array) ($data['colors'] ?? []),
        );
    }
}
