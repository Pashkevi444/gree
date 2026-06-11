<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\B2bCardCollection;
use Gree\Collection\CompanyLogoCollection;
use Gree\Collection\HowItWorksCardCollection;
use Gree\Contract\Repository\PartnersRepositoryInterface;
use Gree\DTO\B2bCardDto;
use Gree\DTO\BrandLogoDto;
use Gree\DTO\HowItWorksCardDto;
use Gree\Enum\IblockCode;

final class PartnersRepository extends BaseRepository implements PartnersRepositoryInterface
{
    protected const int TTL = self::TTL_STATIC;

    public function getB2b(): B2bCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::PartnersB2b);
        if (!$iblockId) {
            return new B2bCardCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DESCRIPTION'),
                ['STEP_NUMBER_VALUE' => 'STEP_NUMBER.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new B2bCardDto(
                id: (int) $row['ID'],
                stepNumber: (int) ($row['STEP_NUMBER_VALUE'] ?? 0),
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'DESCRIPTION'),
            );
        }
        return new B2bCardCollection(...$items);
    }

    public function getHowItWorks(): HowItWorksCardCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::PartnersHowItWorks);
        if (!$iblockId) {
            return new HowItWorksCardCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('DESCRIPTION'),
                $this->localizedSelect('LINK_LABEL'),
                ['LINK_URL_VALUE' => 'LINK_URL.VALUE'],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $items = [];
        while ($row = $rows->fetch()) {
            $items[] = new HowItWorksCardDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                description: $this->localized($row, 'DESCRIPTION'),
                linkLabel: $this->localized($row, 'LINK_LABEL'),
                linkUrl: (string) ($row['LINK_URL_VALUE'] ?? ''),
            );
        }
        return new HowItWorksCardCollection(...$items);
    }

    public function getCompanies(): CompanyLogoCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::PartnersCompaniesTrust);
        if (!$iblockId) {
            return new CompanyLogoCollection();
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
            $items[] = new BrandLogoDto(
                id: (int) $row['ID'],
                name: $this->localized($row, 'NAME'),
                imageUrl: !empty($row['IMAGE_VALUE']) ? (string) \CFile::GetPath((int) $row['IMAGE_VALUE']) : '',
            );
        }
        return new CompanyLogoCollection(...$items);
    }
}
