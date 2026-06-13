<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Карточка раздела «Как работает партнёрство». Внизу карточки опционально
 * рендерится ссылка (LINK_LABEL + LINK_URL) — например «Связаться в телеграм»
 * или «Скачать презентацию». Если linkUrl пустой — ссылка не показывается.
 */
final readonly class HowItWorksCardDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description = '',
        public string $linkLabel = '',
        public string $linkUrl = '',
    ) {}

    public function hasLink(): bool
    {
        return $this->linkUrl !== '' && $this->linkLabel !== '';
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            linkLabel: (string) ($data['link_label'] ?? ''),
            linkUrl: (string) ($data['link_url'] ?? ''),
        );
    }
}
