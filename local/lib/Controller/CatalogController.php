<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\View\CatalogViewData;

final class CatalogController extends BaseController
{
    public function __construct(private readonly CatalogServiceInterface $catalogService) {}

    public function index(): HttpResponse
    {
        $this->setMeta('Каталог кондиционеров Gree');
        $this->addPageAssets('catalog');

        $filter = FilterDto::fromRequest($this->getRequest());
        $products = $this->catalogService->getList($filter);
        $total = $this->catalogService->count($filter);

        return $this->view('catalog/index', new CatalogViewData($products, $filter, $total));
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

    public static function buildItemPayload(ProductDto $product): array
    {
        $payload = [
            'image' => $product->image,
            'name' => $product->name,
            'meta' => [
                'text' => $product->area > 0 ? "Площадь — {$product->area} м²" : '',
                'colors' => $product->colors,
            ],
            'price' => $product->price,
            'href' => "/catalog/{$product->code}/",
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
            'totalPages' => max(1, $totalPages),
            'currentPage' => max(1, $page),
        ];
    }
}
