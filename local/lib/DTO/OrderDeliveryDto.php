<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\DeliveryCity;

final readonly class OrderDeliveryDto extends BaseDto
{
    public function __construct(
        public DeliveryCity $city,
        public string $street,
        public string $house,
        public string $apartment = '',
        public string $comment = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            city:      DeliveryCity::tryFrom((string) ($data['city'] ?? '')) ?? DeliveryCity::Tashkent,
            street:    trim((string) ($data['street'] ?? '')),
            house:     trim((string) ($data['house'] ?? '')),
            apartment: trim((string) ($data['apartment'] ?? '')),
            comment:   trim((string) ($data['comment'] ?? '')),
        );
    }
}
