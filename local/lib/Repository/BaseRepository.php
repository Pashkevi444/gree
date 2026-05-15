<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Iblock\IblockTable;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\IblockCode;
use Gree\Enum\Locale;

abstract class BaseRepository
{
    protected const int TTL = 3600;
    protected const array SORT = ['SORT' => 'ASC', 'TIMESTAMP_X' => 'DESC', 'DATE_CREATE' => 'DESC'];

    public function __construct(
        protected readonly LanguageServiceInterface $language,
    ) {}

    protected function locale(): Locale
    {
        return $this->language->get();
    }

    /**
     * Build a [alias => path] pair of locale-aware SELECT entries for a property whose
     * RU/EN variants live in {$base}_RU / {$base}_EN. The alias MUST differ from the
     * underlying property code (Bitrix D7 forbids alias-name collisions with entity
     * fields), so the alias gets a `_VALUE` suffix.
     *
     *   ['SUBTITLE_RU_VALUE' => 'SUBTITLE_RU.VALUE', 'SUBTITLE_EN_VALUE' => 'SUBTITLE_EN.VALUE']
     *
     * @return array<string, string>
     */
    protected function localizedSelect(string $base): array
    {
        return [
            $base . '_RU_VALUE' => $base . '_RU.VALUE',
            $base . '_EN_VALUE' => $base . '_EN.VALUE',
        ];
    }

    /**
     * Pick the localized value out of an ORM row using the current session locale.
     * Falls back to the other language if the preferred one is empty.
     *
     * @param array<string, mixed> $row
     */
    protected function localized(array $row, string $base): string
    {
        $ru = (string) ($row[$base . '_RU_VALUE'] ?? '');
        $en = (string) ($row[$base . '_EN_VALUE'] ?? '');

        if ($this->locale() === Locale::En) {
            return $en !== '' ? $en : $ru;
        }
        return $ru !== '' ? $ru : $en;
    }

    protected function resolveIblockId(IblockCode $code): int
    {
        $row = IblockTable::query()
            ->where('API_CODE', $code->value)
            ->setSelect(['ID'])
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();

        return (int) ($row['ID'] ?? 0);
    }
}
