<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\B2bCardCollection;
use Gree\Collection\CompanyLogoCollection;
use Gree\Collection\HowItWorksCardCollection;
use Gree\Contract\Repository\PartnersRepositoryInterface;
use Gree\Contract\Service\PartnersServiceInterface;
use Gree\Logging\FileLogger;

final class PartnersService extends BaseService implements PartnersServiceInterface
{
    public function __construct(private readonly PartnersRepositoryInterface $repository) {}

    public function getB2b(): B2bCardCollection
    {
        try {
            return $this->repository->getB2b();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getHowItWorks(): HowItWorksCardCollection
    {
        try {
            return $this->repository->getHowItWorks();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getCompanies(): CompanyLogoCollection
    {
        try {
            return $this->repository->getCompanies();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
