<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\BreadcrumbCollection;
use Gree\DTO\ProductDto;
use Gree\Enum\ProductType;

interface BreadcrumbsServiceInterface
{
    /**
     * Home → Catalog (current).
     */
    public function catalog(): BreadcrumbCollection;

    /**
     * Home → Catalog → <type> (current).
     */
    public function catalogSection(ProductType $type): BreadcrumbCollection;

    /**
     * Home → Catalog → <type> → <product name> (current).
     */
    public function product(ProductDto $product): BreadcrumbCollection;
}
