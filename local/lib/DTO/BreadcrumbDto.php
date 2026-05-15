<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * One crumb in a breadcrumb trail.
 *
 *   - $label is what gets rendered (already localized — service resolves labels
 *     via TranslatorService before constructing the DTO).
 *   - $url is the link target. Empty string means «current page» — the partial
 *     renders such items as <span> rather than <a>.
 */
final readonly class BreadcrumbDto extends BaseDto
{
    public function __construct(
        public string $label,
        public string $url = '',
    ) {}

    public function isCurrent(): bool
    {
        return $this->url === '';
    }

    public static function fromArray(array $data): static
    {
        return new self(
            label: (string) ($data['label'] ?? ''),
            url: (string) ($data['url'] ?? ''),
        );
    }
}
