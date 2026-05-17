<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\FilterDto;
use Gree\Enum\ProductType;
use Gree\View\HomeViewData;

final class HomeController extends BaseController
{
    private const PRODUCTS_PER_TYPE = 3;

    public function __construct(
        private readonly HomeServiceInterface $homeService,
        private readonly CatalogServiceInterface $catalogService,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('home'));
        $this->addPageAssets('home');

        $data = new HomeViewData(
            slider: $this->homeService->getSlider(),
            greeCards: $this->homeService->getGreeCards(),
            greeStats: $this->homeService->getGreeStats(),
            appFeatures: $this->homeService->getAppFeatures(),
            technologies: $this->homeService->getTechnologies(),
            wallProducts: $this->productsByType(ProductType::Wall),
            columnProducts: $this->productsByType(ProductType::Column),
            industrialProducts: $this->productsByType(ProductType::Industrial),
        );

        return $this->view('home/index', $data);
    }

    private function productsByType(ProductType $type): \Gree\Collection\ProductCollection
    {
        return $this->catalogService->getList(new FilterDto(
            types: [$type],
            perPage: self::PRODUCTS_PER_TYPE,
        ));
    }
}
