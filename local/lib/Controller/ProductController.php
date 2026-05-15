<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Collection\BreadcrumbCollection;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\View\ProductViewData;

final class ProductController extends BaseController
{
    public function __construct(
        private readonly CatalogServiceInterface $catalogService,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
    ) {}

    public function show(string $code): HttpResponse
    {
        $product = $this->catalogService->getByCode($code);

        $this->setMeta($product?->name ?? 'Кондиционер Gree');
        $this->addPageAssets('product');

        $crumbs = $product
            ? $this->breadcrumbs->product($product)
            : new BreadcrumbCollection();

        return $this->view('catalog/product', new ProductViewData(
            product: $product,
            code: $code,
            breadcrumbs: $crumbs,
        ));
    }
}
