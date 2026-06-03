<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\DeliveryItemCollection;
use Gree\Collection\HelpStepCollection;
use Gree\Collection\PaymentMethodCollection;
use Gree\Collection\ServiceCardCollection;
use Gree\Collection\ServiceFeatureCollection;
use Gree\DTO\ServiceHeroDto;

final readonly class HelpViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public PaymentMethodCollection $paymentMethods,
        public DeliveryItemCollection $delivery,
        public HelpStepCollection $exchangeSteps,
        public HelpStepCollection $refundSteps,
        public ServiceFeatureCollection $serviceFeatures,
        public ?ServiceHeroDto $serviceHero,
        public ServiceCardCollection $serviceCards,
    ) {}
}
