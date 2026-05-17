<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\DTO\SeoDto;
use Gree\Enum\IblockCode;

interface SeoServiceInterface
{
    /**
     * Resolve SEO for a static page (e.g. "home", "catalog-nastennie", "blog").
     * Falls back to an empty-title SeoDto if no row exists — never null —
     * so callers can safely pipe the value into setMeta() without nullchecks.
     */
    public function forPage(string $code): SeoDto;

    /**
     * Resolve SEO for an iblock element. Locale is implicit (taken from the
     * Language service). Returns null when the element has no IPROPERTY
     * templates or per-element overrides — caller decides on the fallback.
     */
    public function forElement(IblockCode $iblock, int $elementId): ?SeoDto;
}
