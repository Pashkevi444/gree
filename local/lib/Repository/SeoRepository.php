<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Main\Loader;
use Gree\Contract\Repository\SeoRepositoryInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\HlblockCode;
use Gree\Enum\Locale;

/**
 * SEO storage adapter:
 *   - Static pages: Highloadblock «Seo» (UF_PAGE_CODE → SeoDto), paired RU/EN.
 *   - Detail pages: Bitrix native IPROPERTY values (b_iblock_element_iprop +
 *     iblock-level templates configured in Version20260517000006).
 *
 * Locale resolution: every RU/EN field has a fallback to the opposite language
 * if the requested one is empty — same rule we use across BaseRepository for
 * property pairs.
 */
final class SeoRepository extends BaseHlblockRepository implements SeoRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Seo;
    }

    public function findByPageCode(string $code, Locale $locale): ?SeoDto
    {
        try {
            $row = $this->query()
                ->where('UF_PAGE_CODE', $code)
                ->setSelect([
                    'UF_TITLE_RU', 'UF_TITLE_EN',
                    'UF_DESCRIPTION_RU', 'UF_DESCRIPTION_EN',
                    'UF_KEYWORDS_RU', 'UF_KEYWORDS_EN',
                    'UF_OG_TITLE_RU', 'UF_OG_TITLE_EN',
                    'UF_OG_DESCRIPTION_RU', 'UF_OG_DESCRIPTION_EN',
                    'UF_OG_IMAGE',
                ])
                ->setLimit(1)
                ->setCacheTtl(3600)
                ->exec()
                ->fetch();
        } catch (\RuntimeException) {
            return null;
        }

        if (!$row) {
            return null;
        }

        return new SeoDto(
            title:         $this->pick($row, 'UF_TITLE', $locale),
            description:   $this->pick($row, 'UF_DESCRIPTION', $locale),
            keywords:      $this->pick($row, 'UF_KEYWORDS', $locale),
            ogTitle:       $this->pick($row, 'UF_OG_TITLE', $locale),
            ogDescription: $this->pick($row, 'UF_OG_DESCRIPTION', $locale),
            ogImage:       (string) ($row['UF_OG_IMAGE'] ?? ''),
        );
    }

    public function findByElement(int $iblockId, int $elementId, Locale $locale): ?SeoDto
    {
        if ($iblockId <= 0 || $elementId <= 0) {
            return null;
        }
        Loader::includeModule('iblock');

        $ipropValues = new ElementValues($iblockId, $elementId);
        $values = $ipropValues->getValues();

        // Bitrix returns array<string, string> keyed by code (e.g. ELEMENT_META_TITLE).
        // Empty templates/overrides yield empty strings — treat as missing.
        $title = (string) ($values['ELEMENT_META_TITLE'] ?? '');
        if ($title === '') {
            return null;
        }

        return new SeoDto(
            title:         $title,
            description:   (string) ($values['ELEMENT_META_DESCRIPTION'] ?? ''),
            keywords:      (string) ($values['ELEMENT_META_KEYWORDS'] ?? ''),
            ogTitle:       (string) ($values['ELEMENT_PAGE_TITLE'] ?? $title),
            ogDescription: (string) ($values['ELEMENT_META_DESCRIPTION'] ?? ''),
            ogImage:       '',
        );
    }

    /**
     * Pick the locale-appropriate value from a `_RU` / `_EN` pair, with
     * fallback to the opposite locale when the preferred one is empty.
     *
     * @param array<string, mixed> $row
     */
    private function pick(array $row, string $base, Locale $locale): string
    {
        $ru = (string) ($row[$base . '_RU'] ?? '');
        $en = (string) ($row[$base . '_EN'] ?? '');

        if ($locale === Locale::En) {
            return $en !== '' ? $en : $ru;
        }
        return $ru !== '' ? $ru : $en;
    }
}
