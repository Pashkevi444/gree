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

final class BrandService extends BaseService implements BrandServiceInterface
{
    public function __construct(private readonly BrandRepositoryInterface $repo) {}

    public function getHistory(): ?BrandHistoryDto
    {
        return $this->repo->getHistory();
    }

    public function getWhyGree(): ?BrandWhyGreeDto
    {
        return $this->repo->getWhyGree();
    }

    public function getGreeCards(): GreeCardCollection
    {
        return $this->repo->getGreeCards();
    }

    public function getGreeStats(): GreeStatCollection
    {
        return $this->repo->getGreeStats();
    }

    public function getAboutCards(): BrandAboutCardCollection
    {
        return $this->repo->getAboutCards();
    }

    public function getTechnologies(): TechnologyCollection
    {
        return $this->repo->getTechnologies();
    }
}
