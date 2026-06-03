<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Карточка из секции «Как с нами связаться». Кнопка либо ведёт на внешний
 * адрес (telegram, mailto), либо открывает координату на карте в футере —
 * в этом случае buttonUrl=='' и заполнены latitude/longitude.
 */
final readonly class ContactChannelDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description = '',
        public string $buttonLabel = '',
        public string $buttonUrl = '',
        public string $iconCode = '',
        public string $latitude = '',
        public string $longitude = '',
    ) {}

    public function opensMap(): bool
    {
        return $this->buttonUrl === '' && $this->latitude !== '' && $this->longitude !== '';
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            buttonLabel: (string) ($data['button_label'] ?? ''),
            buttonUrl: (string) ($data['button_url'] ?? ''),
            iconCode: (string) ($data['icon_code'] ?? ''),
            latitude: (string) ($data['latitude'] ?? ''),
            longitude: (string) ($data['longitude'] ?? ''),
        );
    }
}
