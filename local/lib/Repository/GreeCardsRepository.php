<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Contract\Repository\GreeCardsRepositoryInterface;
use Gree\DTO\GreeCardDto;
use Gree\DTO\GreeStatDto;
use Gree\Enum\IblockCode;

final class GreeCardsRepository extends BaseRepository implements GreeCardsRepositoryInterface
{
    public function getCards(IblockCode $code): GreeCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId($code);
        if (!$iblockId) {
            return new GreeCardCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                ['ICON_CODE_VALUE' => 'ICON_CODE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeCardDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'PREVIEW_TEXT'),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
            );
        }

        return new GreeCardCollection(...$items);
    }

    public function getStats(IblockCode $code): GreeStatCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId($code);
        if (!$iblockId) {
            return new GreeStatCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                $this->localizedSelect('NUMBER_PREFIX'),
                $this->localizedSelect('NUMBER_SUFFIX'),
                ['NUMBER_VALUE_VALUE' => 'NUMBER_VALUE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeStatDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                numberValue: (int) ($row['NUMBER_VALUE_VALUE'] ?? 0),
                numberPrefix: $this->localized($row, 'NUMBER_PREFIX'),
                numberSuffix: $this->localized($row, 'NUMBER_SUFFIX'),
                description: $this->localized($row, 'PREVIEW_TEXT'),
            );
        }

        return new GreeStatCollection(...$items);
    }
}
