<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\CartLineCollection;

final readonly class CartViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public CartLineCollection $lines,
        public int $total,
        public int $itemsCount,
    ) {}
}
