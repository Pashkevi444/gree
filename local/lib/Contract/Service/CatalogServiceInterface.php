<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;

interface CatalogServiceInterface
{
    public function getList(FilterDto $filter): ProductCollection;

    public function count(FilterDto $filter): int;

    public function getByCode(string $code): ?ProductDto;

    public function getGreeCards(): GreeCardCollection;

    public function getGreeStats(): GreeStatCollection;
}
