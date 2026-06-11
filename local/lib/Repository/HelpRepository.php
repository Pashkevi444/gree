<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\DeliveryItemCollection;
use Gree\Collection\HelpStepCollection;
use Gree\Collection\PaymentMethodCollection;
use Gree\Collection\ServiceCardCollection;
use Gree\Collection\ServiceFeatureCollection;
use Gree\Contract\Repository\HelpRepositoryInterface;
use Gree\DTO\DeliveryItemDto;
use Gree\DTO\HelpStepDto;
use Gree\DTO\PaymentMethodDto;
use Gree\DTO\ServiceCardDto;
use Gree\DTO\ServiceFeatureDto;
use Gree\DTO\ServiceHeroDto;
use Gree\Enum\IblockCode;

final class HelpRepository extends BaseRepository implements HelpRepositoryInterface
{
    protected const int TTL = self::TTL_STATIC;

    public function getPaymentMethods(): PaymentMethodCollection
    {
        $iblockId = $this->iblockId(IblockCode::HelpPaymentMethods);
        if (!$iblockId) {
            return new PaymentMethodCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                ['IMAGE_VALUE' => 'IMAGE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new PaymentMethodDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                imageUrl: !empty($row['IMAGE_VALUE']) ? (string) \CFile::GetPath((int) $row['IMAGE_VALUE']) : '',
            );
        }
        return new PaymentMethodCollection(...$items);
    }

    public function getDelivery(): DeliveryItemCollection
    {
        $iblockId = $this->iblockId(IblockCode::HelpDelivery);
        if (!$iblockId) {
            return new DeliveryItemCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
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
        while ($row = $rows->fetch()) {
            $items[] = new DeliveryItemDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'PREVIEW_TEXT'),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
            );
        }
        return new DeliveryItemCollection(...$items);
    }

    public function getExchangeSteps(): HelpStepCollection
    {
        return $this->fetchSteps(IblockCode::HelpExchangeSteps, withTooltip: false);
    }

    public function getRefundSteps(): HelpStepCollection
    {
        return $this->fetchSteps(IblockCode::HelpRefundSteps, withTooltip: true);
    }

    public function getServiceFeatures(): ServiceFeatureCollection
    {
        $iblockId = $this->iblockId(IblockCode::HelpServiceFeatures);
        if (!$iblockId) {
            return new ServiceFeatureCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                ['ICON_CODE_VALUE' => 'ICON_CODE.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new ServiceFeatureDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
            );
        }
        return new ServiceFeatureCollection(...$items);
    }

    public function getServiceHero(): ?ServiceHeroDto
    {
        $iblockId = $this->iblockId(IblockCode::HelpServiceHero);
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
                ['BACKGROUND_VALUE' => 'BACKGROUND.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setLimit(1)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }
        return new ServiceHeroDto(
            id: (int) $row['ID'],
            name: $this->localized($row, 'NAME'),
            description: $this->localized($row, 'PREVIEW_TEXT'),
            backgroundUrl: !empty($row['BACKGROUND_VALUE']) ? (string) \CFile::GetPath((int) $row['BACKGROUND_VALUE']) : '',
        );
    }

    public function getServiceCards(): ServiceCardCollection
    {
        $iblockId = $this->iblockId(IblockCode::HelpServiceCards);
        if (!$iblockId) {
            return new ServiceCardCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
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
        while ($row = $rows->fetch()) {
            $items[] = new ServiceCardDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'PREVIEW_TEXT'),
                iconCode: (string) ($row['ICON_CODE_VALUE'] ?? ''),
            );
        }
        return new ServiceCardCollection(...$items);
    }

    private function fetchSteps(IblockCode $code, bool $withTooltip): HelpStepCollection
    {
        $iblockId = $this->iblockId($code);
        if (!$iblockId) {
            return new HelpStepCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $select = array_merge(
            ['ID'],
            $this->localizedSelect('NAME'),
            $this->localizedSelect('PREVIEW_TEXT'),
            ['STEP_NUMBER_VALUE' => 'STEP_NUMBER.VALUE'],
        );
        if ($withTooltip) {
            $select = array_merge($select, $this->localizedSelect('TOOLTIP'));
        }

        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect($select)
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new HelpStepDto(
                id: (int) $row['ID'],
                stepNumber: (int) ($row['STEP_NUMBER_VALUE'] ?? 0),
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'PREVIEW_TEXT'),
                tooltip: $withTooltip ? $this->localized($row, 'TOOLTIP') : '',
            );
        }
        return new HelpStepCollection(...$items);
    }

    private function iblockId(IblockCode $code): int
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        return $this->resolveIblockId($code);
    }
}
