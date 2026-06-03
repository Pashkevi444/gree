<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\PartnersServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\View\PartnersViewData;

final class PartnersController extends BaseController
{
    public function __construct(
        private readonly PartnersServiceInterface $partners,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('partners'));
        $this->addPageAssets('partners');

        return $this->view('partners/index', new PartnersViewData(
            breadcrumbs: $this->breadcrumbs->partners(),
            b2b:         $this->partners->getB2b(),
            howItWorks:  $this->partners->getHowItWorks(),
            companies:   $this->partners->getCompanies(),
        ));
    }
}
