<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\DTO\AppFeatureDto;
use Gree\DTO\GreeCardDto;
use Gree\DTO\GreeStatDto;
use Gree\DTO\SliderItemDto;
use Gree\DTO\TechnologyDto;
use Gree\Enum\IblockCode;

final class HomeRepository extends BaseRepository implements HomeRepositoryInterface
{

    // -------------------------------------------------------------------------

    public function getSlider(): SliderItemCollection
    {
        return $this->fetchSlider();
    }

    public function getGreeCards(): GreeCardCollection
    {
        return $this->fetchGreeCards();
    }

    public function getGreeStats(): GreeStatCollection
    {
        return $this->fetchGreeStats();
    }

    public function getAppFeatures(): AppFeatureCollection
    {
        return $this->fetchAppFeatures();
    }

    public function getTechnologies(): TechnologyCollection
    {
        return $this->fetchTechnologies();
    }

    // ---- D7 fetch methods ---------------------------------------------------

    private function fetchSlider(): SliderItemCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::HomeSlider);
        if (!$iblockId) {
            return new SliderItemCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_PICTURE', 'SUBTITLE_VALUE' => 'SUBTITLE.VALUE', 'BUTTON_TEXT_VALUE' => 'BUTTON_TEXT.VALUE', 'BUTTON_URL_VALUE' => 'BUTTON_URL.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new SliderItemDto(
                id: (int)$row['ID'],
                name: (string)$row['NAME'],
                subtitle: (string)($row['SUBTITLE_VALUE'] ?? ''),
                buttonText: (string)($row['BUTTON_TEXT_VALUE'] ?? ''),
                buttonUrl: (string)($row['BUTTON_URL_VALUE'] ?? ''),
                backgroundImage: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            );
        }

        return new SliderItemCollection(...$items);
    }

    private function fetchGreeCards(): GreeCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::HomeGreeCards);
        if (!$iblockId) {
            return new GreeCardCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'ICON_CODE_VALUE' => 'ICON_CODE.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeCardDto(
                id: (int)$row['ID'],
                name: (string)$row['NAME'],
                description: (string)($row['PREVIEW_TEXT'] ?? ''),
                iconCode: (string)($row['ICON_CODE_VALUE'] ?? ''),
            );
        }

        return new GreeCardCollection(...$items);
    }

    private function fetchGreeStats(): GreeStatCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::HomeGreeStats);
        if (!$iblockId) {
            return new GreeStatCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'NUMBER_PREFIX_VALUE' => 'NUMBER_PREFIX.VALUE', 'NUMBER_VALUE_VALUE' => 'NUMBER_VALUE.VALUE', 'NUMBER_SUFFIX_VALUE' => 'NUMBER_SUFFIX.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new GreeStatDto(
                id: (int)$row['ID'],
                name: (string)$row['NAME'],
                numberValue: (int)($row['NUMBER_VALUE_VALUE'] ?? 0),
                numberPrefix: (string)($row['NUMBER_PREFIX_VALUE'] ?? ''),
                numberSuffix: (string)($row['NUMBER_SUFFIX_VALUE'] ?? ''),
                description: (string)($row['PREVIEW_TEXT'] ?? ''),
            );
        }

        return new GreeStatCollection(...$items);
    }

    private function fetchAppFeatures(): AppFeatureCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::HomeAppFeatures);
        if (!$iblockId) {
            return new AppFeatureCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'ICON_CODE_VALUE' => 'ICON_CODE.VALUE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new AppFeatureDto(
                id: (int)$row['ID'],
                name: (string)$row['NAME'],
                description: (string)($row['PREVIEW_TEXT'] ?? ''),
                iconCode: (string)($row['ICON_CODE_VALUE'] ?? ''),
            );
        }

        return new AppFeatureCollection(...$items);
    }

    private function fetchTechnologies(): TechnologyCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::HomeTechnologies);
        if (!$iblockId) {
            return new TechnologyCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE'])
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = new TechnologyDto(
                id: (int)$row['ID'],
                name: (string)$row['NAME'],
                description: (string)($row['PREVIEW_TEXT'] ?? ''),
                image: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            );
        }

        return new TechnologyCollection(...$items);
    }

}
