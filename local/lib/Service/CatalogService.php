<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\Contract\Repository\CatalogRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\DTO\FilterDto;
use Gree\DTO\ProductDto;
use Gree\Logging\FileLogger;

final class CatalogService extends BaseService implements CatalogServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CatalogRepositoryInterface $catalogRepository,
    ) {}

    public function getList(FilterDto $filter): ProductCollection
    {
        try {
            return $this->productRepository->getList($filter);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function count(FilterDto $filter): int
    {
        try {
            return $this->productRepository->count($filter);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getByCode(string $code): ?ProductDto
    {
        try {
            return $this->productRepository->getByCode($code);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['code' => $code, 'exception' => $e]);
            throw $e;
        }
    }

    public function getGreeCards(): GreeCardCollection
    {
        try {
            return $this->catalogRepository->getGreeCards();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getGreeStats(): GreeStatCollection
    {
        try {
            return $this->catalogRepository->getGreeStats();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
