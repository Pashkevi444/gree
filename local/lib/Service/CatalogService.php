<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;

final class CatalogService extends BaseService implements CatalogServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function getList(FilterDto $filter): ProductCollection
    {
        return $this->productRepository->getList($filter);
    }

    public function count(FilterDto $filter): int
    {
        return $this->productRepository->count($filter);
    }

    public function getByCode(string $code): ?ProductDto
    {
        return $this->productRepository->getByCode($code);
    }
}
