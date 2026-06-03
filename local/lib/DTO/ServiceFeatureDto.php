<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class ServiceFeatureDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $iconCode = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            iconCode: (string) ($data['icon_code'] ?? ''),
        );
    }
}
