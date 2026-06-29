<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\CartLineCollection;
use Gree\Collection\CityCollection;
use Gree\Enum\PaymentMethod;

final readonly class OrderCheckoutViewData extends BaseViewData
{
    /** @param array<int, PaymentMethod> $paymentMethods */
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public CartLineCollection $lines,
        public int $total,
        public int $itemsCount,
        public CityCollection $cities,
        public array $paymentMethods,
    ) {}
}
