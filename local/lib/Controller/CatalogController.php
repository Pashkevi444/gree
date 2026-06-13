<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\Enum\Color;
use Gree\Enum\ProductType;
use Gree\View\CatalogViewData;

final class CatalogController extends BaseController
{
    public function __construct(
        private readonly CatalogServiceInterface $catalogService,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        return $this->render(lockedType: null);
    }

    /**
     * Section landing — /catalog/nastennie/ etc. URL slug pins the type filter
     * for the whole page; the "Type" filter group in the form is hidden because
     * it's already decided by the URL.
     *
     * Invalid slugs are rejected at the routing layer (regex guard), but defend
     * here too in case someone calls this directly.
     */
    public function section(string $slug): HttpResponse
    {
        $type = ProductType::fromSlug($slug);
        if ($type === null) {
            return $this->render(lockedType: null);
        }
        return $this->render(lockedType: $type);
    }

    public function filter(): HttpResponse
    {
        $filter = FilterDto::fromRequest($this->getRequest());
        $products = $this->catalogService->getList($filter);
        $total = $this->catalogService->count($filter);

        return $this->json([
            'items' => array_map(self::buildItemPayload(...), $products->toArray()),
            'pagination' => self::buildPagination($total, $filter->perPage, $filter->page),
        ]);
    }

    private function render(?ProductType $lockedType): HttpResponse
    {
        // Section pages have their own SEO record (e.g. "catalog-nastennie").
        // Hub /catalog/ uses the generic "catalog" code.
        $seoCode = $lockedType !== null ? 'catalog-' . $lockedType->slug() : 'catalog';
        $this->applySeo($this->seo->forPage($seoCode));
        $this->addPageAssets('catalog');

        $filter = FilterDto::fromRequest($this->getRequest());

        // Section page forces the type filter regardless of what's in the
        // session / query — the URL is the source of truth.
        if ($lockedType !== null) {
            $filter = new FilterDto(
                types: [$lockedType],
                priceMin: $filter->priceMin,
                priceMax: $filter->priceMax,
                areas: $filter->areas,
                bestseller: $filter->bestseller,
                inverterMotor: $filter->inverterMotor,
                colors: $filter->colors,
                sortField: $filter->sortField,
                page: $filter->page,
                perPage: $filter->perPage,
            );
        }

        $products = $this->catalogService->getList($filter);
        $total = $this->catalogService->count($filter);
        $greeCards = $this->catalogService->getGreeCards();
        $greeStats = $this->catalogService->getGreeStats();

        $crumbs = $lockedType !== null
            ? $this->breadcrumbs->catalogSection($lockedType)
            : $this->breadcrumbs->catalog();

        return $this->view('catalog/index', new CatalogViewData(
            products: $products,
            filter: $filter,
            total: $total,
            greeCards: $greeCards,
            greeStats: $greeStats,
            breadcrumbs: $crumbs,
            lockedType: $lockedType,
        ));
    }

    public static function buildItemPayload(ProductDto $product): array
    {
        $href = \Gree\Helpers\Route::to('catalog.product', [
            'section' => $product->type->slug(),
            'code'    => $product->code,
        ]);
        $payload = [
            'image' => $product->image,
            'name' => $product->name,
            'meta' => [
                'text' => $product->area > 0 ? "Площадь — {$product->area} м²" : '',
                'colors' => array_map(fn(Color $c) => $c->hex(), $product->colors),
            ],
            'price' => $product->price,
            'href' => $href,
        ];

        if ($product->isBestseller) {
            $payload['badge'] = ['type' => 'bestseller', 'text' => 'Хит продаж'];
        }

        return $payload;
    }

    public static function buildPagination(int $total, int $perPage, int $page): array
    {
        $totalPages = ($total > 0 && $perPage > 0) ? (int) ceil($total / $perPage) : 1;

        return [
            'totalItems'  => max(0, $total),
            'totalPages'  => max(1, $totalPages),
            'currentPage' => max(1, $page),
        ];
    }
}
