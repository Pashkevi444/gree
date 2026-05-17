<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Contract\Service\TranslationLoaderInterface;
use Gree\Enum\HlblockCode;

/**
 * Reads UI translations from the `Translations` Highloadblock (CODE / VALUE_RU / VALUE_EN).
 * Implements the loader contract so TranslatorService stays storage-agnostic.
 */
final class TranslationRepository extends BaseHlblockRepository implements TranslationLoaderInterface
{
    /** @var array<string, array{ru: string, en: string}>|null */
    private ?array $cache = null;

    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Translations;
    }

    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        try {
            $result = $this->query()
                ->setSelect(['UF_CODE', 'UF_VALUE_RU', 'UF_VALUE_EN'])
                ->setCacheTtl(3600)
                ->exec();
        } catch (\RuntimeException) {
            return $this->cache = [];
        }

        foreach ($result as $row) {
            $code = (string) ($row['UF_CODE'] ?? '');
            if ($code === '') {
                continue;
            }
            $entries[$code] = [
                'ru' => (string) ($row['UF_VALUE_RU'] ?? ''),
                'en' => (string) ($row['UF_VALUE_EN'] ?? ''),
            ];
        }

        return $this->cache = $entries;
    }
}
