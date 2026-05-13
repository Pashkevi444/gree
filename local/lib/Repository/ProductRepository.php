<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\Enum\IblockCode;
use Gree\Enum\ProductType;
use Gree\Enum\SortField;

final class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    private const SELECT = [
        'ID', 'NAME', 'CODE', 'PREVIEW_PICTURE',
        'TYPE_ITEM_XML_ID' => 'TYPE.ITEM.XML_ID',
        'PRICE_VALUE' => 'PRICE.VALUE',
        'AREA_VALUE' => 'AREA.VALUE',
        'BESTSELLER_VALUE' => 'BESTSELLER.VALUE',
        'INVERTER_MOTOR_VALUE' => 'INVERTER_MOTOR.VALUE',
    ];

    public function getList(FilterDto $filter): ProductCollection
    {
        return $this->fetchList($filter);
    }

    public function count(FilterDto $filter): int
    {
        return $this->fetchCount($filter);
    }

    public function getByCode(string $code): ?ProductDto
    {
        return $this->fetchByCode($code);
    }

    // ---- D7 fetch methods ---------------------------------------------------

    private function fetchList(FilterDto $filter): ProductCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Products);
        if (!$iblockId) {
            return new ProductCollection();
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(self::SELECT)
            ->setOrder($this->resolveSortOrder($filter->sortField))
            ->setOffset(($filter->page - 1) * $filter->perPage)
            ->setLimit($filter->perPage);

        $this->applyFilters($query, $filter);

        $items = [];
        foreach ($query->exec() as $row) {
            $items[] = $this->hydrate($row);
        }

        return new ProductCollection(...$items);
    }

    private function fetchCount(FilterDto $filter): int
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Products);
        if (!$iblockId) {
            return 0;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $query = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID']);

        $this->applyFilters($query, $filter);

        $count = 0;
        foreach ($query->exec() as $_) {
            $count++;
        }

        return $count;
    }

    private function fetchByCode(string $code): ?ProductDto
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Products);
        if (!$iblockId) {
            return null;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->where('CODE', $code)
            ->setSelect(self::SELECT)
            ->exec()
            ->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    // ---- helpers ------------------------------------------------------------

    private function applyFilters(object $query, FilterDto $filter): void
    {
        if ($filter->types) {
            $query->whereIn('TYPE.ITEM.XML_ID', array_map(fn(ProductType $t) => $t->value, $filter->types));
        }

        if ($filter->priceMin > 0) {
            $query->where('PRICE.VALUE', '>=', $filter->priceMin);
        }

        if ($filter->priceMax < PHP_INT_MAX) {
            $query->where('PRICE.VALUE', '<=', $filter->priceMax);
        }

        if ($filter->areas) {
            $query->whereIn('AREA.VALUE', $filter->areas);
        }

        if ($filter->bestseller !== null) {
            $filter->bestseller
                ? $query->where('BESTSELLER.VALUE', 'Y')
                : $query->whereNot('BESTSELLER.VALUE', 'Y');
        }

        if ($filter->inverterMotor !== null) {
            $filter->inverterMotor
                ? $query->where('INVERTER_MOTOR.VALUE', 'Y')
                : $query->whereNot('INVERTER_MOTOR.VALUE', 'Y');
        }
    }

    private function hydrate(array $row): ProductDto
    {
        return new ProductDto(
            id: (int) $row['ID'],
            name: (string) $row['NAME'],
            code: (string) $row['CODE'],
            type: ProductType::tryFrom((string) ($row['TYPE_ITEM_XML_ID'] ?? '')) ?? ProductType::Wall,
            price: (int) ($row['PRICE_VALUE'] ?? 0),
            area: (int) ($row['AREA_VALUE'] ?? 0),
            isBestseller: ($row['BESTSELLER_VALUE'] ?? '') === 'Y',
            image: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
        );
    }

    private function resolveSortOrder(SortField $sort): array
    {
        return match ($sort) {
            SortField::Popular => ['SORT' => 'ASC'],
            SortField::PriceAsc => ['PRICE.VALUE' => 'ASC'],
            SortField::PriceDesc => ['PRICE.VALUE' => 'DESC'],
        };
    }
}
