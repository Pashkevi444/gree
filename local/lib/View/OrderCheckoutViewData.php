<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\CartLineCollection;
use Gree\Enum\DeliveryCity;
use Gree\Enum\PaymentMethod;

final readonly class OrderCheckoutViewData extends BaseViewData
{
    /**
     * @param array<int, DeliveryCity>  $cities
     * @param array<int, PaymentMethod> $paymentMethods
     */
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public CartLineCollection $lines,
        public int $total,
        public int $itemsCount,
        public array $cities,
        public array $paymentMethods,
    ) {}
}
