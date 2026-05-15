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
use Gree\Logging\FileLogger;

final class HomeService extends BaseService implements HomeServiceInterface
{
    public function __construct(private readonly HomeRepositoryInterface $homeRepository) {}

    public function getSlider(): SliderItemCollection
    {
        try {
            return $this->homeRepository->getSlider();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getGreeCards(): GreeCardCollection
    {
        try {
            return $this->homeRepository->getGreeCards();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getGreeStats(): GreeStatCollection
    {
        try {
            return $this->homeRepository->getGreeStats();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getAppFeatures(): AppFeatureCollection
    {
        try {
            return $this->homeRepository->getAppFeatures();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getTechnologies(): TechnologyCollection
    {
        try {
            return $this->homeRepository->getTechnologies();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
