<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class BrandWhyGreeDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $buttonText = '',
        public string $buttonUrl = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            buttonText: (string) ($data['button_text'] ?? ''),
            buttonUrl: (string) ($data['button_url'] ?? ''),
        );
    }
}
