<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\DTO\SeoDto;
use Gree\Enum\Locale;

interface SeoRepositoryInterface
{
    /**
     * SEO bundle for a static-page code (e.g. "home", "catalog-nastennie"),
     * resolved into the target locale. Returns null when no row matches.
     */
    public function findByPageCode(string $code, Locale $locale): ?SeoDto;

    /**
     * SEO bundle for an iblock element (product or blog article) derived from
     * the inherited IPROPERTY values configured on the iblock. Returns null
     * when the iblock has no templates and the element has no overrides.
     */
    public function findByElement(int $iblockId, int $elementId, Locale $locale): ?SeoDto;
}
