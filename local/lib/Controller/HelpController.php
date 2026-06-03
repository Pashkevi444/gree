<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\HelpServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\View\HelpViewData;

final class HelpController extends BaseController
{
    public function __construct(
        private readonly HelpServiceInterface $help,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('help'));
        $this->addPageAssets('help');

        return $this->view('help/index', new HelpViewData(
            breadcrumbs: $this->breadcrumbs->help(),
            paymentMethods: $this->help->getPaymentMethods(),
            delivery: $this->help->getDelivery(),
            exchangeSteps: $this->help->getExchangeSteps(),
            refundSteps: $this->help->getRefundSteps(),
            serviceFeatures: $this->help->getServiceFeatures(),
            serviceHero: $this->help->getServiceHero(),
            serviceCards: $this->help->getServiceCards(),
        ));
    }
}
