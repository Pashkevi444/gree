<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\DTO\FilterDto;
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
        $pages = ($total > 0 && $filter->perPage > 0) ? (int) ceil($total / $filter->perPage) : 0;

        return $this->json([
            'products' => array_map(fn($p) => $p->toArray(), $products->toArray()),
            'total' => $total,
            'page' => $filter->page,
            'per_page' => $filter->perPage,
            'pages' => $pages,
        ]);
    }
}
