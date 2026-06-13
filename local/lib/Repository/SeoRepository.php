<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Main\Loader;
use Gree\Contract\Repository\SeoRepositoryInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\HlblockCode;
use Gree\Enum\Locale;

/** Статические страницы → HL «Seo» (UF_PAGE_CODE, RU/UZ). Детальные товаров → Bitrix IPROPERTY. Пустой RU → fallback на UZ и наоборот. */
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
                    'UF_TITLE_RU', 'UF_TITLE_UZ',
                    'UF_DESCRIPTION_RU', 'UF_DESCRIPTION_UZ',
                    'UF_KEYWORDS_RU', 'UF_KEYWORDS_UZ',
                    'UF_OG_TITLE_RU', 'UF_OG_TITLE_UZ',
                    'UF_OG_DESCRIPTION_RU', 'UF_OG_DESCRIPTION_UZ',
                    'UF_OG_IMAGE',
                ])
                ->setLimit(1)
                ->setCacheTtl(static::TTL_STATIC)
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

        // Пустой ELEMENT_META_TITLE = шаблон не настроен → null.
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

    /** @param array<string, mixed> $row  Берёт {base}_{LOC}, fallback на противоположную локаль если пусто. */
    private function pick(array $row, string $base, Locale $locale): string
    {
        $ru = (string) ($row[$base . '_RU'] ?? '');
        $uz = (string) ($row[$base . '_UZ'] ?? '');

        if ($locale === Locale::Uz) {
            return $uz !== '' ? $uz : $ru;
        }
        return $ru !== '' ? $ru : $uz;
    }
}
