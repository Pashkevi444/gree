<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Gree\Contract\Service\TranslationLoaderInterface;
use Gree\Enum\HlblockCode;

/**
 * Reads UI translations from the `Translations` Highloadblock (CODE / VALUE_RU / VALUE_EN).
 * Implements the loader contract so TranslatorService stays storage-agnostic.
 */
final class TranslationRepository implements TranslationLoaderInterface
{
    private const TTL = 3600;

    /** @var array<string, array{ru: string, en: string}>|null */
    private ?array $cache = null;

    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        if (!Loader::includeModule('highloadblock')) {
            return $this->cache = [];
        }

        $hlblock = HighloadBlockTable::query()
            ->where('NAME', HlblockCode::Translations->value)
            ->setSelect(['ID', 'NAME', 'TABLE_NAME'])
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();

        if (!$hlblock) {
            return $this->cache = [];
        }

        $dataClass = HighloadBlockTable::compileEntity($hlblock)->getDataClass();

        $entries = [];
        $result = $dataClass::query()
            ->setSelect(['UF_CODE', 'UF_VALUE_RU', 'UF_VALUE_EN'])
            ->setCacheTtl(self::TTL)
            ->exec();

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
