<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\Contract\Service\BrandServiceInterface;
use Gree\DTO\BrandHistoryDto;
use Gree\DTO\BrandWhyGreeDto;
use Gree\Logging\FileLogger;

final class BrandService extends BaseService implements BrandServiceInterface
{
    public function __construct(private readonly BrandRepositoryInterface $repo) {}

    public function getHistory(): ?BrandHistoryDto
    {
        try {
            return $this->repo->getHistory();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getWhyGree(): ?BrandWhyGreeDto
    {
        try {
            return $this->repo->getWhyGree();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getGreeCards(): GreeCardCollection
    {
        try {
            return $this->repo->getGreeCards();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getGreeStats(): GreeStatCollection
    {
        try {
            return $this->repo->getGreeStats();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getAboutCards(): BrandAboutCardCollection
    {
        try {
            return $this->repo->getAboutCards();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getTechnologies(): TechnologyCollection
    {
        try {
            return $this->repo->getTechnologies();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
