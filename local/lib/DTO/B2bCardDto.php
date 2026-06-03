<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class B2bCardDto extends BaseDto
{
    public function __construct(
        public int $id,
        public int $stepNumber,
        public string $name,
        public string $description = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            stepNumber: (int) ($data['step_number'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
        );
    }
}
