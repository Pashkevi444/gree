<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\DTO\BrandAboutCardDto;
use Gree\DTO\BrandHistoryDto;
use Gree\DTO\BrandWhyGreeDto;
use Gree\DTO\GreeCardDto;
use Gree\DTO\GreeStatDto;
use Gree\DTO\TechnologyDto;
use Gree\Enum\IblockCode;

final class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{

    public function getHistory(): ?BrandHistoryDto
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandHistory);
        if (!$iblockId) {
            return null;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'DETAIL_TEXT'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }

        return new BrandHistoryDto(
            id: (int) $row['ID'],
            name: (string) $row['NAME'],
            text: (string) ($row['DETAIL_TEXT'] ?? ''),
        );
    }

    public function getWhyGree(): ?BrandWhyGreeDto
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandWhyGree);
        if (!$iblockId) {
            return null;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'BUTTON_TEXT_VALUE' => 'BUTTON_TEXT.VALUE', 'BUTTON_URL_VALUE' => 'BUTTON_URL.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }

        return new BrandWhyGreeDto(
            id: (int) $row['ID'],
            name: (string) $row['NAME'],
            description: (string) ($row['PREVIEW_TEXT'] ?? ''),
            buttonText: (string) ($row['BUTTON_TEXT_VALUE'] ?? ''),
            buttonUrl: (string) ($row['BUTTON_URL_VALUE'] ?? ''),
        );
    }

    public function getGreeCards(): GreeCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandGreeCards);
        if (!$iblockId) {
            return new GreeCardCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'ICON_CODE_VALUE' => 'ICON_CODE.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeCardDto(
                id: (int) $row['ID'],
                name: (string) $row['NAME'],
                description: (string) ($row['PREVIEW_TEXT'] ?? ''),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
            );
        }

        return new GreeCardCollection(...$items);
    }

    public function getGreeStats(): GreeStatCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandGreeStats);
        if (!$iblockId) {
            return new GreeStatCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'NUMBER_PREFIX_VALUE' => 'NUMBER_PREFIX.VALUE', 'NUMBER_VALUE_VALUE' => 'NUMBER_VALUE.VALUE', 'NUMBER_SUFFIX_VALUE' => 'NUMBER_SUFFIX.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeStatDto(
                id: (int) $row['ID'],
                name: (string) $row['NAME'],
                numberValue: (int) ($row['NUMBER_VALUE_VALUE'] ?? 0),
                numberPrefix: (string) ($row['NUMBER_PREFIX_VALUE'] ?? ''),
                numberSuffix: (string) ($row['NUMBER_SUFFIX_VALUE'] ?? ''),
            );
        }

        return new GreeStatCollection(...$items);
    }

    public function getAboutCards(): BrandAboutCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandAboutCards);
        if (!$iblockId) {
            return new BrandAboutCardCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'DETAIL_TEXT'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new BrandAboutCardDto(
                id: (int) $row['ID'],
                name: (string) $row['NAME'],
                description: (string) ($row['DETAIL_TEXT'] ?? ''),
            );
        }

        return new BrandAboutCardCollection(...$items);
    }

    public function getTechnologies(): TechnologyCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::BrandTechnologies);
        if (!$iblockId) {
            return new TechnologyCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new TechnologyDto(
                id: (int) $row['ID'],
                name: (string) $row['NAME'],
                description: (string) ($row['PREVIEW_TEXT'] ?? ''),
                image: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            );
        }

        return new TechnologyCollection(...$items);
    }

}
