<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Карточка из секции «Как с нами связаться». Кнопка либо ведёт на внешний
 * адрес (telegram, mailto), либо открывает координату на карте в футере —
 * в этом случае buttonUrl=='' и заполнены latitude/longitude.
 *
 * `code` — стабильный CODE iblock-элемента (orders-telegram / office /
 * service-center / email). По нему шапка/футер/страницы дёргают конкретный
 * канал через ContactsService::findChannelByCode().
 */
final readonly class ContactChannelDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $code = '',
        public string $description = '',
        public string $buttonLabel = '',
        public string $buttonUrl = '',
        public string $iconCode = '',
        public string $phone = '',
        public string $latitude = '',
        public string $longitude = '',
    ) {}

    public function opensMap(): bool
    {
        return $this->buttonUrl === '' && $this->latitude !== '' && $this->longitude !== '';
    }

    /**
     * Email-адрес без префикса mailto: — для отображения текстом.
     * Возвращает null если канал не email или buttonUrl пустой.
     */
    public function emailAddress(): ?string
    {
        if ($this->buttonUrl === '' || !str_starts_with($this->buttonUrl, 'mailto:')) {
            return null;
        }
        return substr($this->buttonUrl, 7) ?: null;
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            buttonLabel: (string) ($data['button_label'] ?? ''),
            buttonUrl: (string) ($data['button_url'] ?? ''),
            iconCode: (string) ($data['icon_code'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            latitude: (string) ($data['latitude'] ?? ''),
            longitude: (string) ($data['longitude'] ?? ''),
        );
    }
}
