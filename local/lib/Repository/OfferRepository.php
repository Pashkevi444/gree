<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\OfferCollection;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\OfferDto;
use Gree\Enum\Color;
use Gree\Enum\IblockCode;

final class OfferRepository extends BaseRepository implements OfferRepositoryInterface
{
    public function getByProductIds(array $productIds): array
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if (!$productIds) {
            return [];
        }

        $iblockId = $this->resolveIblockId(IblockCode::ProductsOffers);
        if (!$iblockId) {
            return [];
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->whereIn('CML2_LINK.VALUE', $productIds)
            ->setSelect(array_merge(
                [
                    'ID',
                    'CML2_LINK_VALUE' => 'CML2_LINK.VALUE',
                    'PRICE_VALUE' => 'PRICE.VALUE',
                    'AREA_VALUE' => 'AREA.VALUE',
                    'COLOR_XML_ID' => 'COLOR.ITEM.XML_ID',
                    'IN_STOCK_XML_ID' => 'IN_STOCK.ITEM.XML_ID',
                ],
                $this->localizedSelect('COOLING_POWER'),
                $this->localizedSelect('HEATING_POWER'),
                $this->localizedSelect('NOISE'),
                $this->localizedSelect('INDOOR_DIMENSIONS'),
                $this->localizedSelect('OUTDOOR_DIMENSIONS'),
                $this->localizedSelect('INDOOR_WEIGHT'),
                $this->localizedSelect('OUTDOOR_WEIGHT'),
            ))
            ->setOrder(self::SORT)
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $rows = [];
        $offerIds = [];
        while ($row = $result->fetch()) {
            $rows[] = $row;
            $offerIds[] = (int) $row['ID'];
        }
        $galleryByOffer = $this->fetchGalleryByOfferIds($entity, $offerIds);

        /** @var array<int, OfferDto[]> $offersByProduct */
        $offersByProduct = [];
        foreach ($rows as $row) {
            $productId = (int) ($row['CML2_LINK_VALUE'] ?? 0);
            if ($productId <= 0) {
                continue;
            }
            $offerId = (int) $row['ID'];
            $offersByProduct[$productId] ??= [];
            $offersByProduct[$productId][] = $this->hydrate($row, $productId, $galleryByOffer[$offerId] ?? []);
        }

        $out = [];
        foreach ($offersByProduct as $pid => $offers) {
            $out[$pid] = new OfferCollection(...$offers);
        }
        return $out;
    }

    public function findProductIds(FilterDto $filter): array
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::ProductsOffers);
        if (!$iblockId) {
            return [];
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['PRODUCT_ID' => 'CML2_LINK.VALUE'])
            ->setCacheTtl(self::TTL);

        if ($filter->priceMin > 0) {
            $query->where('PRICE.VALUE', '>=', $filter->priceMin);
        }
        if ($filter->priceMax < PHP_INT_MAX) {
            $query->where('PRICE.VALUE', '<=', $filter->priceMax);
        }
        if ($filter->areas) {
            $query->whereIn('AREA.VALUE', $filter->areas);
        }
        if ($filter->colors) {
            $query->whereIn(
                'COLOR.ITEM.XML_ID',
                array_map(fn(Color $c) => $c->value, $filter->colors),
            );
        }

        $ids = [];
        foreach ($query->exec() as $row) {
            $pid = (int) ($row['PRODUCT_ID'] ?? 0);
            if ($pid > 0) {
                $ids[$pid] = $pid;
            }
        }
        return array_values($ids);
    }

    public function existsActive(int $offerId): bool
    {
        if ($offerId <= 0) {
            return false;
        }
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::ProductsOffers);
        if (!$iblockId) {
            return false;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->where('ID', $offerId)
            ->setSelect(['ID'])
            ->setLimit(1)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();

        return (bool) $row;
    }

    public function getByIds(array $offerIds): OfferCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if (!$offerIds) {
            return new OfferCollection();
        }

        $iblockId = $this->resolveIblockId(IblockCode::ProductsOffers);
        if (!$iblockId) {
            return new OfferCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->whereIn('ID', $offerIds)
            ->setSelect(array_merge(
                [
                    'ID',
                    'CML2_LINK_VALUE' => 'CML2_LINK.VALUE',
                    'PRICE_VALUE' => 'PRICE.VALUE',
                    'AREA_VALUE' => 'AREA.VALUE',
                    'COLOR_XML_ID' => 'COLOR.ITEM.XML_ID',
                    'IN_STOCK_XML_ID' => 'IN_STOCK.ITEM.XML_ID',
                ],
                $this->localizedSelect('COOLING_POWER'),
                $this->localizedSelect('HEATING_POWER'),
                $this->localizedSelect('NOISE'),
                $this->localizedSelect('INDOOR_DIMENSIONS'),
                $this->localizedSelect('OUTDOOR_DIMENSIONS'),
                $this->localizedSelect('INDOOR_WEIGHT'),
                $this->localizedSelect('OUTDOOR_WEIGHT'),
            ))
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $rows = [];
        $offerIds = [];
        while ($row = $result->fetch()) {
            $rows[] = $row;
            $offerIds[] = (int) $row['ID'];
        }
        $galleryByOffer = $this->fetchGalleryByOfferIds($entity, $offerIds);

        $offers = [];
        foreach ($rows as $row) {
            $productId = (int) ($row['CML2_LINK_VALUE'] ?? 0);
            $offers[] = $this->hydrate($row, $productId, $galleryByOffer[(int) $row['ID']] ?? []);
        }

        return new OfferCollection(...$offers);
    }

    /**
     * @param int[] $offerIds
     * @return array<int, string[]>  offer ID → image URLs
     */
    private function fetchGalleryByOfferIds(string $entity, array $offerIds): array
    {
        if (!$offerIds) {
            return [];
        }

        $result = $entity::query()
            ->whereIn('ID', $offerIds)
            ->setSelect(['ID', 'GALLERY_VALUE' => 'GALLERY.VALUE'])
            ->exec();

        $byId = [];
        foreach ($result as $row) {
            $fileId = (int) ($row['GALLERY_VALUE'] ?? 0);
            if ($fileId <= 0) {
                continue;
            }
            $path = \CFile::GetPath($fileId);
            if (!$path) {
                continue;
            }
            $id = (int) $row['ID'];
            $byId[$id] ??= [];
            $byId[$id][] = $path;
        }
        return $byId;
    }

    /**
     * @param string[] $gallery
     */
    private function hydrate(array $row, int $productId, array $gallery = []): OfferDto
    {
        $colorXml = (string) ($row['COLOR_XML_ID'] ?? '');

        return new OfferDto(
            id: (int) $row['ID'],
            productId: $productId,
            price: (int) ($row['PRICE_VALUE'] ?? 0),
            area: (int) ($row['AREA_VALUE'] ?? 0),
            color: $colorXml !== '' ? Color::tryFrom($colorXml) : null,
            inStock: ($row['IN_STOCK_XML_ID'] ?? '') === 'Y',
            coolingPower: $this->localized($row, 'COOLING_POWER'),
            heatingPower: $this->localized($row, 'HEATING_POWER'),
            noise: $this->localized($row, 'NOISE'),
            indoorDimensions: $this->localized($row, 'INDOOR_DIMENSIONS'),
            outdoorDimensions: $this->localized($row, 'OUTDOOR_DIMENSIONS'),
            indoorWeight: $this->localized($row, 'INDOOR_WEIGHT'),
            outdoorWeight: $this->localized($row, 'OUTDOOR_WEIGHT'),
            gallery: $gallery,
        );
    }
}
