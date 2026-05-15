<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\DTO\FilterDto;
use Gree\Enum\ProductType;

final readonly class CatalogViewData extends BaseViewData
{
    public function __construct(
        public ProductCollection $products,
        public FilterDto $filter,
        public int $total,
        public GreeCardCollection $greeCards,
        public GreeStatCollection $greeStats,
        public BreadcrumbCollection $breadcrumbs,
        /**
         * If non-null, the URL fixes the product type — the catalog template
         * should hide the "Тип" filter group and not let the user toggle it.
         */
        public ?ProductType $lockedType = null,
    ) {}
}
