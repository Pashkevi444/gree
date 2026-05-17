<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\OfferCollection;
use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\Enum\IblockCode;
use Gree\Enum\ProductType;
use Gree\Enum\SortField;

/**
 * Products live in the `products` iblock — model-level data only. Offer-level
 * attributes (price, area, color, in-stock, capacity-dependent specs) are read
 * via OfferRepository and attached to the product DTO. Aggregates exposed on
 * ProductDto (price, area, colors, inStock) are derived from the underlying
 * offers — min price / max area / unique colors / any-in-stock.
 *
 * Filter precedence:
 *   1. If the filter touches offer-level attrs (price/area/color), narrow the
 *      candidate product IDs via OfferRepository::findProductIds() first.
 *   2. Apply product-level filters (types/bestseller/inverter) with the ID
 *      constraint added.
 *   3. Popular sort uses DB-side LIMIT/OFFSET. Price sorts fetch all matches
 *      and sort in PHP — catalogue is small, this is fine.
 */
final class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(
        LanguageServiceInterface $language,
        private readonly OfferRepositoryInterface $offerRepository,
    ) {
        parent::__construct($language);
    }

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

    public function getByIds(array $ids): array
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }

        $iblockId = $this->resolveIblockId(IblockCode::Products);
        if (!$iblockId) {
            return [];
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->whereIn('ID', $ids)
            ->setSelect($this->selectFieldsBrief())
            ->setCacheTtl(self::TTL)
            ->cacheJoins(true)
            ->exec();

        $rows = [];
        while ($row = $result->fetch()) {
            $rows[(int) $row['ID']] = $row;
        }

        // Cart enrichment doesn't need offers/gallery/functions — hydrate with
        // empty collections to keep ProductDto contract happy.
        $out = [];
        foreach ($rows as $id => $row) {
            $out[$id] = $this->hydrate($row, new OfferCollection());
        }
        return $out;
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
            ->setSelect($this->selectFieldsBrief())
            ->setOrder(['SORT' => 'ASC', 'TIMESTAMP_X' => 'DESC']);

        $this->applyFilters($query, $filter);

        // For Popular sort we can paginate at the DB level. For price sorts we
        // need the min-offer-price per product, so we have to load everything
        // first, then sort and slice in PHP.
        $popularSort = $filter->sortField === SortField::Popular;
        if ($popularSort) {
            $query
                ->setOffset(($filter->page - 1) * $filter->perPage)
                ->setLimit($filter->perPage);
        }

        $rows = [];
        $ids = [];
        foreach ($query->exec() as $row) {
            $rows[] = $row;
            $ids[] = (int) $row['ID'];
        }

        $offersByProduct = $this->offerRepository->getByProductIds($ids);

        $products = [];
        foreach ($rows as $row) {
            $pid = (int) $row['ID'];
            $products[] = $this->hydrate($row, $offersByProduct[$pid] ?? new OfferCollection());
        }

        if (!$popularSort) {
            usort($products, function (ProductDto $a, ProductDto $b) use ($filter): int {
                $cmp = $a->price <=> $b->price;
                return $filter->sortField === SortField::PriceDesc ? -$cmp : $cmp;
            });
            $products = array_slice(
                $products,
                ($filter->page - 1) * $filter->perPage,
                $filter->perPage,
            );
        }

        return new ProductCollection(...$products);
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
            ->setSelect($this->selectFieldsFull())
            ->exec()
            ->fetch();

        if (!$row) {
            return null;
        }

        $id = (int) $row['ID'];
        $offers = $this->offerRepository->getByProductIds([$id])[$id] ?? new OfferCollection();
        $gallery = $this->fetchGalleryByElementIds($entity, [$id])[$id] ?? [];
        $functions = $this->fetchFunctionsByElementIds($entity, [$id])[$id] ?? [];

        return $this->hydrate($row, $offers, $functions, $gallery);
    }

    // ---- SELECT lists -------------------------------------------------------

    /**
     * Brief: just the model identity. Offer-driven values (price/area/colors)
     * come from the offers query.
     */
    private function selectFieldsBrief(): array
    {
        return array_merge(
            ['ID', 'CODE', 'PREVIEW_PICTURE'],
            $this->localizedSelect('NAME'),
            [
                'TYPE_ITEM_XML_ID'         => 'TYPE.ITEM.XML_ID',
                'BESTSELLER_XML_ID'        => 'BESTSELLER.ITEM.XML_ID',
                'INVERTER_MOTOR_XML_ID'    => 'INVERTER_MOTOR.ITEM.XML_ID',
            ],
        );
    }

    /**
     * Full: every model-level field needed by the detail page. Offer-level data
     * still lives in offers — fetched separately by fetchByCode().
     */
    private function selectFieldsFull(): array
    {
        return array_merge(
            $this->selectFieldsBrief(),
            $this->localizedSelect('PREVIEW_TEXT'),
            $this->localizedSelect('DETAIL_TEXT'),
            [
                'SKU_VALUE'           => 'SKU.VALUE',
                'MODEL_VALUE'         => 'MODEL.VALUE',
                'ENERGY_CLASS_VALUE'  => 'ENERGY_CLASS.VALUE',
                'REFRIGERANT_VALUE'   => 'REFRIGERANT.VALUE',
            ],
            $this->localizedSelect('WARRANTY_TEXT'),
            $this->localizedSelect('KIT_TEXT'),
            $this->localizedSelect('INSTALLATION_TEXT'),
        );
    }

    // ---- filters ------------------------------------------------------------

    private function applyFilters(object $query, FilterDto $filter): void
    {
        if ($filter->types) {
            $query->whereIn('TYPE.ITEM.XML_ID', array_map(fn(ProductType $t) => $t->value, $filter->types));
        }

        if ($filter->bestseller !== null) {
            $filter->bestseller
                ? $query->where('BESTSELLER.ITEM.XML_ID', 'Y')
                : $query->whereNot('BESTSELLER.ITEM.XML_ID', 'Y');
        }

        if ($filter->inverterMotor !== null) {
            $filter->inverterMotor
                ? $query->where('INVERTER_MOTOR.ITEM.XML_ID', 'Y')
                : $query->whereNot('INVERTER_MOTOR.ITEM.XML_ID', 'Y');
        }

        // Offer-level filters: pre-fetch the IDs of products with at least one
        // matching offer, then constrain the product query by those IDs.
        $offerFiltersUsed = $filter->priceMin > 0
            || $filter->priceMax < PHP_INT_MAX
            || $filter->areas
            || $filter->colors;

        if ($offerFiltersUsed) {
            $matchingIds = $this->offerRepository->findProductIds($filter);
            $query->whereIn('ID', $matchingIds ?: [-1]);
        }
    }

    // ---- multi-value fetches (model-level: gallery + functions) ------------

    /**
     * @param int[] $ids
     * @return array<int, string[]>  element ID → feature codes
     */
    private function fetchFunctionsByElementIds(string $entity, array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $result = $entity::query()
            ->whereIn('ID', $ids)
            ->setSelect(['ID', 'FUNCTIONS_VALUE' => 'FUNCTIONS.VALUE'])
            ->exec();

        $byId = [];
        foreach ($result as $row) {
            $code = (string) ($row['FUNCTIONS_VALUE'] ?? '');
            if ($code === '') {
                continue;
            }
            $id = (int) $row['ID'];
            $byId[$id] ??= [];
            if (!in_array($code, $byId[$id], true)) {
                $byId[$id][] = $code;
            }
        }

        return $byId;
    }

    /**
     * @param int[] $ids
     * @return array<int, string[]>  element ID → image URLs
     */
    private function fetchGalleryByElementIds(string $entity, array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $result = $entity::query()
            ->whereIn('ID', $ids)
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

    // ---- hydration ----------------------------------------------------------

    /**
     * @param array<string, mixed> $row
     * @param string[]             $functions
     * @param string[]             $gallery
     */
    private function hydrate(array $row, OfferCollection $offers, array $functions = [], array $gallery = []): ProductDto
    {
        // First in-stock offer (or first overall) — used as the "primary" for
        // the detail-page initial render; JS swaps spec values when the user
        // changes color/area.
        $primaryOffer = null;
        foreach ($offers as $offer) {
            if ($offer->inStock) {
                $primaryOffer = $offer;
                break;
            }
        }
        $primaryOffer ??= $offers->isEmpty() ? null : $offers->first();

        return new ProductDto(
            id: (int) $row['ID'],
            name: $this->localized($row, 'NAME'),
            code: (string) $row['CODE'],
            type: ProductType::tryFrom((string) ($row['TYPE_ITEM_XML_ID'] ?? '')) ?? ProductType::Wall,
            price: $offers->minPrice(),
            area: $offers->maxArea(),
            isBestseller: ($row['BESTSELLER_XML_ID'] ?? '') === 'Y',
            isInverter: ($row['INVERTER_MOTOR_XML_ID'] ?? '') === 'Y',
            image: !empty($row['PREVIEW_PICTURE']) ? \CFile::GetPath($row['PREVIEW_PICTURE']) : '',
            colors: $offers->uniqueColors(),
            description: $this->localized($row, 'DETAIL_TEXT'),
            sku: (string) ($row['SKU_VALUE'] ?? ''),
            model: (string) ($row['MODEL_VALUE'] ?? ''),
            energyClass: (string) ($row['ENERGY_CLASS_VALUE'] ?? ''),
            refrigerant: (string) ($row['REFRIGERANT_VALUE'] ?? ''),
            inStock: $primaryOffer?->inStock ?? false,
            coolingPower: $primaryOffer?->coolingPower ?? '',
            heatingPower: $primaryOffer?->heatingPower ?? '',
            noise: $primaryOffer?->noise ?? '',
            indoorDimensions: $primaryOffer?->indoorDimensions ?? '',
            outdoorDimensions: $primaryOffer?->outdoorDimensions ?? '',
            indoorWeight: $primaryOffer?->indoorWeight ?? '',
            outdoorWeight: $primaryOffer?->outdoorWeight ?? '',
            warrantyText: $this->localized($row, 'WARRANTY_TEXT'),
            kitText: $this->localized($row, 'KIT_TEXT'),
            installationText: $this->localized($row, 'INSTALLATION_TEXT'),
            functions: $functions,
            gallery: $gallery,
            offers: $offers,
        );
    }
}
