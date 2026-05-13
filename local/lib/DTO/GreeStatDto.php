<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class GreeStatDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public int $numberValue,
        public string $numberPrefix = '',
        public string $numberSuffix = '',
        public string $description = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            numberValue: (int) ($data['number_value'] ?? 0),
            numberPrefix: (string) ($data['number_prefix'] ?? ''),
            numberSuffix: (string) ($data['number_suffix'] ?? ''),
            description: (string) ($data['description'] ?? ''),
        );
    }
}
