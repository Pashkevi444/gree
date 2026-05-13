<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\CatalogServiceInterface;

final class ProductController extends BaseController
{
    public function __construct(private readonly CatalogServiceInterface $catalogService) {}

    public function show(string $code): HttpResponse
    {
        $product = $this->catalogService->getByCode($code);

        $this->setMeta($product?->name ?? 'Кондиционер Gree');
        $this->addPageAssets('product');

        return $this->view('catalog/product', ['product' => $product, 'code' => $code]);
    }
}
