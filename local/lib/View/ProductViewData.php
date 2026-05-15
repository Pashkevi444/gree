<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\DTO\ProductDto;

final readonly class ProductViewData extends BaseViewData
{
    public function __construct(
        public ?ProductDto $product,
        public string $code,
        public BreadcrumbCollection $breadcrumbs,
        public GreeCardCollection $greeCards,
        public GreeStatCollection $greeStats,
    ) {}
}
