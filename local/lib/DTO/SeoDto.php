<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Resolved SEO bundle for a single page render (RU or EN, already collapsed).
 * Built either from the Seo HL block (static pages) or from iblock IPROPERTY
 * values (detail pages).
 */
final readonly class SeoDto extends BaseDto
{
    public function __construct(
        public string $title,
        public string $description = '',
        public string $keywords = '',
        public string $ogTitle = '',
        public string $ogDescription = '',
        public string $ogImage = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            title:         (string) ($data['title'] ?? ''),
            description:   (string) ($data['description'] ?? ''),
            keywords:      (string) ($data['keywords'] ?? ''),
            ogTitle:       (string) ($data['og_title'] ?? ''),
            ogDescription: (string) ($data['og_description'] ?? ''),
            ogImage:       (string) ($data['og_image'] ?? ''),
        );
    }
}
