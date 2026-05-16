<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Collection\MenuItemCollection;

final readonly class MenuItemDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $code,
        public string $label,
        public string $url,
        public MenuItemCollection $children,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            code: (string) ($data['code'] ?? ''),
            label: (string) ($data['label'] ?? ''),
            url: (string) ($data['url'] ?? ''),
            children: $data['children'] ?? new MenuItemCollection(),
        );
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function hasUrl(): bool
    {
        return $this->url !== '';
    }
}
