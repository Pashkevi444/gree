<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\DeliveryItemCollection;
use Gree\Collection\HelpStepCollection;
use Gree\Collection\PaymentMethodCollection;
use Gree\Collection\ServiceCardCollection;
use Gree\Collection\ServiceFeatureCollection;
use Gree\DTO\ServiceHeroDto;

interface HelpServiceInterface
{
    public function getPaymentMethods(): PaymentMethodCollection;
    public function getDelivery(): DeliveryItemCollection;
    public function getExchangeSteps(): HelpStepCollection;
    public function getRefundSteps(): HelpStepCollection;
    public function getServiceFeatures(): ServiceFeatureCollection;
    public function getServiceHero(): ?ServiceHeroDto;
    public function getServiceCards(): ServiceCardCollection;
}
