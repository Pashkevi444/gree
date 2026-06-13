<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class PaymentMethodDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $imageUrl = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            imageUrl: (string) ($data['image_url'] ?? ''),
        );
    }
}
