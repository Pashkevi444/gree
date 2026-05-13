<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\ProductCollection;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;

interface ProductRepositoryInterface
{
    public function getList(FilterDto $filter): ProductCollection;

    public function count(FilterDto $filter): int;

    public function getByCode(string $code): ?ProductDto;
}
