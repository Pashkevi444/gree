<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\Contract\Service\WhereToBuyServiceInterface;
use Gree\View\WhereToBuyViewData;

final class WhereToBuyController extends BaseController
{
    public function __construct(
        private readonly WhereToBuyServiceInterface $whereToBuy,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('where-to-buy'));
        $this->addPageAssets('where-to-buy');

        return $this->view('where-to-buy/index', new WhereToBuyViewData(
            breadcrumbs: $this->breadcrumbs->whereToBuy(),
            locations: $this->whereToBuy->getLocations(),
            partners: $this->whereToBuy->getPartners(),
            chains: $this->whereToBuy->getChains(),
        ));
    }
}
