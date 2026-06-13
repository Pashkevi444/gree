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
    protected const int TTL = self::TTL_STATIC;

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
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DETAIL_TEXT'),
            ))
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
            name: $this->localized($row, 'NAME'),
            text: $this->localized($row, 'DETAIL_TEXT'),
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
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                $this->localizedSelect('BUTTON_TEXT'),
                ['BUTTON_URL_VALUE' => 'BUTTON_URL.VALUE'],
            ))
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
            name: $this->localized($row, 'NAME'),
            description: $this->localized($row, 'PREVIEW_TEXT'),
            buttonText: $this->localized($row, 'BUTTON_TEXT'),
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
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('PREVIEW_TEXT'),
                ['ICON_CODE_VALUE' => 'ICON_CODE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
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
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('NUMBER_PREFIX'),
                $this->localizedSelect('NUMBER_SUFFIX'),
                ['NUMBER_VALUE_VALUE' => 'NUMBER_VALUE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeStatDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                numberValue: (int) ($row['NUMBER_VALUE_VALUE'] ?? 0),
                numberPrefix: $this->localized($row, 'NUMBER_PREFIX'),
                numberSuffix: $this->localized($row, 'NUMBER_SUFFIX'),
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
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DETAIL_TEXT'),
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new BrandAboutCardDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'DETAIL_TEXT'),
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
            ->setSelect(array_merge(
                ['ID', 'PREVIEW_PICTURE'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DETAIL_TEXT'),
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new TechnologyDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'DETAIL_TEXT'),
                image: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            );
        }

        return new TechnologyCollection(...$items);
    }
}
