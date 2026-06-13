<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\ChainLogoCollection;
use Gree\Collection\PartnerLogoCollection;
use Gree\Collection\WhereToBuyLocationCollection;
use Gree\Contract\Repository\WhereToBuyRepositoryInterface;
use Gree\Contract\Service\WhereToBuyServiceInterface;
use Gree\Logging\FileLogger;

final class WhereToBuyService extends BaseService implements WhereToBuyServiceInterface
{
    public function __construct(private readonly WhereToBuyRepositoryInterface $repository) {}

    public function getLocations(): WhereToBuyLocationCollection
    {
        try {
            return $this->repository->getLocations();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getPartners(): PartnerLogoCollection
    {
        try {
            return $this->repository->getPartners();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getChains(): ChainLogoCollection
    {
        try {
            return $this->repository->getChains();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
