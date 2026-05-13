<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\Contract\Service\HomeServiceInterface;

final class HomeService extends BaseService implements HomeServiceInterface
{
    public function __construct(private readonly HomeRepositoryInterface $homeRepository) {}

    public function getSlider(): SliderItemCollection
    {
        return $this->homeRepository->getSlider();
    }

    public function getGreeCards(): GreeCardCollection
    {
        return $this->homeRepository->getGreeCards();
    }

    public function getGreeStats(): GreeStatCollection
    {
        return $this->homeRepository->getGreeStats();
    }

    public function getAppFeatures(): AppFeatureCollection
    {
        return $this->homeRepository->getAppFeatures();
    }

    public function getTechnologies(): TechnologyCollection
    {
        return $this->homeRepository->getTechnologies();
    }
}
