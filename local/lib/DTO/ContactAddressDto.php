<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Физическая точка из секции «Адреса». Координаты обязательны — кнопка
 * «Показать на карте» открывает их в footer-iframe.
 *
 * @phpstan-type Phone string
 */
final readonly class ContactAddressDto extends BaseDto
{
    /**
     * @param string[] $phones
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $schedule,
        public array $phones,
        public string $imageUrl,
        public string $latitude,
        public string $longitude,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            schedule: (string) ($data['schedule'] ?? ''),
            phones: array_values((array) ($data['phones'] ?? [])),
            imageUrl: (string) ($data['image_url'] ?? ''),
            latitude: (string) ($data['latitude'] ?? ''),
            longitude: (string) ($data['longitude'] ?? ''),
        );
    }
}
