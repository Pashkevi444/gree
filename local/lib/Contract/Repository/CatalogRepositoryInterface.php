<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;

/**
 * Catalog-page block content (the "Почему выбирают Gree" section). Mirrors
 * HomeRepositoryInterface / BrandRepositoryInterface — each page has its own
 * iblocks so editors can change the catalog text without affecting home/brand.
 *
 * Products themselves live in ProductRepositoryInterface — different concern.
 */
interface CatalogRepositoryInterface
{
    public function getGreeCards(): GreeCardCollection;

    public function getGreeStats(): GreeStatCollection;
}
