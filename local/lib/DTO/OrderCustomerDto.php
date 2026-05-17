<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class OrderCustomerDto extends BaseDto
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $telegram = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name:     trim((string) ($data['name'] ?? '')),
            phone:    trim((string) ($data['phone'] ?? '')),
            telegram: trim((string) ($data['telegram'] ?? '')),
        );
    }
}
