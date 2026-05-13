<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class TechnologyDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description = '',
        public string $image = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            image: (string) ($data['image'] ?? ''),
        );
    }
}
