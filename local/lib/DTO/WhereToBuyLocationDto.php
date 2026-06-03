<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Точка продаж на странице /where-to-buy/. Структура совпадает с
 * ContactAddressDto, но семантика разная — это публичная карточка для
 * пользователя, а не «куда писать жалобу». Координаты используются JS-
 * обработчиком футер-iframe карты (data-show-on-map="lat,lon").
 *
 * @phpstan-type Phone string
 */
final readonly class WhereToBuyLocationDto extends BaseDto
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
