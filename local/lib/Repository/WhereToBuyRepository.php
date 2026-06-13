<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\ChainLogoCollection;
use Gree\Collection\PartnerLogoCollection;
use Gree\Collection\WhereToBuyLocationCollection;
use Gree\Contract\Repository\WhereToBuyRepositoryInterface;
use Gree\DTO\BrandLogoDto;
use Gree\DTO\WhereToBuyLocationDto;
use Gree\Enum\IblockCode;

final class WhereToBuyRepository extends BaseRepository implements WhereToBuyRepositoryInterface
{
    protected const int TTL = self::TTL_STATIC;

    public function getLocations(): WhereToBuyLocationCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::WhereToBuyLocations);
        if (!$iblockId) {
            return new WhereToBuyLocationCollection();
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        // PHONES — multi-string, дублирует строки в результате; группируем по ID.
        $rows = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID'],
                $this->localizedSelect('NAME'),
                $this->localizedSelect('SCHEDULE'),
                [
                    'PHONES_VALUE'    => 'PHONES.VALUE',
                    'IMAGE_VALUE'     => 'IMAGE.VALUE',
                    'LATITUDE_VALUE'  => 'LATITUDE.VALUE',
                    'LONGITUDE_VALUE' => 'LONGITUDE.VALUE',
                ],
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)->cacheJoins(true)
            ->exec();

        $byId = [];
        while ($row = $rows->fetch()) {
            $id = (int) $row['ID'];
            if (!isset($byId[$id])) {
                $byId[$id] = [
                    'name'      => $this->localized($row, 'NAME'),
                    'schedule'  => $this->localized($row, 'SCHEDULE'),
                    'image_id'  => (int) ($row['IMAGE_VALUE'] ?? 0),
                    'latitude'  => (string) ($row['LATITUDE_VALUE'] ?? ''),
                    'longitude' => (string) ($row['LONGITUDE_VALUE'] ?? ''),
                    'phones'    => [],
                ];
            }
            $phone = (string) ($row['PHONES_VALUE'] ?? '');
            if ($phone !== '' && !in_array($phone, $byId[$id]['phones'], true)) {
                $byId[$id]['phones'][] = $phone;
            }
        }

        $items = [];
        foreach ($byId as $id => $data) {
            $items[] = new WhereToBuyLocationDto(
                id: $id,
                name: $data['name'],
                schedule: $data['schedule'],
                phones: $data['phones'],
                imageUrl: $data['image_id'] > 0 ? (string) \CFile::GetPath($data['image_id']) : '',
                latitude: $data['latitude'],
                longitude: $data['longitude'],
            );
        }
        return new WhereToBuyLocationCollection(...$items);
    }

    public function getPartners(): PartnerLogoCollection
    {
        $items = $this->fetchLogos(IblockCode::WhereToBuyPartners);
        return new PartnerLogoCollection(...$items);
    }

    public function getChains(): ChainLogoCollection
    {
        $items = $this->fetchLogos(IblockCode::WhereToBuyChains);
        return new ChainLogoCollection(...$items);
    }

    /**
     * @return BrandLogoDto[]
     */
    private function fetchLogos(IblockCode $code): array
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId($code);
        if (!$iblockId) {
            return [];
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
        return $items;
    }
}
