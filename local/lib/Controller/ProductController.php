<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\IblockCode;
use Gree\Enum\ProductType;
use Gree\View\ProductViewData;

final class ProductController extends BaseController
{
    public function __construct(
        private readonly CatalogServiceInterface $catalogService,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    /**
     * URL: /catalog/{section}/{code}/
     *
     * Section is informational here — the product DTO knows its own type — but
     * we use it for breadcrumbs and could later 301-redirect if the slug in the
     * URL disagrees with the product's actual type.
     */
    public function show(string $code, ?string $section = null): HttpResponse
    {
        $product = $this->catalogService->getByCode($code);

        // Per-element SEO from iblock IPROPERTY (templates + per-element overrides
        // in Bitrix admin). Falls back to product name when templates are empty.
        if ($product !== null) {
            $seo = $this->seo->forElement(IblockCode::Products, $product->id)
                ?? new SeoDto(title: $product->name);
            $this->applySeo($seo);
        } else {
            $this->setMeta('Кондиционер Gree');
        }
        $this->addPageAssets('product');

        $crumbs = $product
            ? $this->breadcrumbs->product($product)
            : new BreadcrumbCollection();

        // The "Why Gree" section at the bottom reuses the catalog iblock —
        // same content as the listing page, no separate iblock for product detail.
        $greeCards = $product ? $this->catalogService->getGreeCards() : new GreeCardCollection();
        $greeStats = $product ? $this->catalogService->getGreeStats() : new GreeStatCollection();

        // Validate section slug if it was provided — log mismatches for now,
        // future: 301 to canonical URL.
        if ($section !== null && $product !== null) {
            $expected = $product->type->slug();
            if ($section !== $expected) {
                \Gree\Logging\FileLogger::getInstance()->warning('product.url.section_mismatch', [
                    'code' => $code,
                    'urlSection' => $section,
                    'expected' => $expected,
                ]);
            }
        }

        return $this->view('catalog/product', new ProductViewData(
            product: $product,
            code: $code,
            breadcrumbs: $crumbs,
            greeCards: $greeCards,
            greeStats: $greeStats,
        ));
    }
}
