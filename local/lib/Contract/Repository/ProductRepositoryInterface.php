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

    /**
     * Bulk-load products by ID. Used by the cart layer to enrich cart lines
     * with product metadata without going through getList()/getByCode() one
     * by one.
     *
     * @param int[] $ids
     * @return array<int, ProductDto> map of id → DTO; missing ids are skipped
     */
    public function getByIds(array $ids): array;
}
