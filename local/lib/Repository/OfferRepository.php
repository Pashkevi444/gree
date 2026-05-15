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

        /** @var array<int, OfferDto[]> $offersByProduct */
        $offersByProduct = [];
        while ($row = $result->fetch()) {
            $productId = (int) ($row['CML2_LINK_VALUE'] ?? 0);
            if ($productId <= 0) {
                continue;
            }
            $offersByProduct[$productId] ??= [];
            $offersByProduct[$productId][] = $this->hydrate($row, $productId);
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

    private function hydrate(array $row, int $productId): OfferDto
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
        );
    }
}
