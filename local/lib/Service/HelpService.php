<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\DeliveryItemCollection;
use Gree\Collection\HelpStepCollection;
use Gree\Collection\PaymentMethodCollection;
use Gree\Collection\ServiceCardCollection;
use Gree\Collection\ServiceFeatureCollection;
use Gree\Contract\Repository\HelpRepositoryInterface;
use Gree\Contract\Service\HelpServiceInterface;
use Gree\DTO\ServiceHeroDto;
use Gree\Logging\FileLogger;

final class HelpService extends BaseService implements HelpServiceInterface
{
    public function __construct(private readonly HelpRepositoryInterface $helpRepository) {}

    public function getPaymentMethods(): PaymentMethodCollection
    {
        try {
            return $this->helpRepository->getPaymentMethods();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getDelivery(): DeliveryItemCollection
    {
        try {
            return $this->helpRepository->getDelivery();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getExchangeSteps(): HelpStepCollection
    {
        try {
            return $this->helpRepository->getExchangeSteps();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getRefundSteps(): HelpStepCollection
    {
        try {
            return $this->helpRepository->getRefundSteps();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getServiceFeatures(): ServiceFeatureCollection
    {
        try {
            return $this->helpRepository->getServiceFeatures();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getServiceHero(): ?ServiceHeroDto
    {
        try {
            return $this->helpRepository->getServiceHero();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getServiceCards(): ServiceCardCollection
    {
        try {
            return $this->helpRepository->getServiceCards();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
