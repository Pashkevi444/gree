<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class OrderDeliveryDto extends BaseDto
{
    public function __construct(
        public CityDto $city,
        public string $street,
        public string $house,
        public string $apartment = '',
        public string $comment = '',
    ) {}

    public static function fromArray(array $data): static
    {
        $rawCity = $data['city'] ?? null;
        $city = $rawCity instanceof CityDto
            ? $rawCity
            : CityDto::fromArray(is_array($rawCity) ? $rawCity : ['id' => (int) $rawCity]);

        return new self(
            city:      $city,
            street:    trim((string) ($data['street'] ?? '')),
            house:     trim((string) ($data['house'] ?? '')),
            apartment: trim((string) ($data['apartment'] ?? '')),
            comment:   trim((string) ($data['comment'] ?? '')),
        );
    }
}
