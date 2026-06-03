<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Логотип брендa/сети-партнёра. Используется и в карусели «Наши партнёры»,
 * и в сетке «Сети магазинов-партнёров» — структура одинаковая, отличаются
 * только iblock'и и Collection-обёртки.
 */
final readonly class BrandLogoDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $imageUrl,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            imageUrl: (string) ($data['image_url'] ?? ''),
        );
    }
}
