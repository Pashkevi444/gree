<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\DeliveryItemCollection;
use Gree\Collection\HelpStepCollection;
use Gree\Collection\PaymentMethodCollection;
use Gree\Collection\ServiceCardCollection;
use Gree\Collection\ServiceFeatureCollection;
use Gree\Contract\Repository\HelpRepositoryInterface;
use Gree\DTO\ServiceHeroDto;
use Gree\Service\HelpService;
use PHPUnit\Framework\TestCase;

final class HelpServiceTest extends TestCase
{
    private HelpRepositoryInterface $repo;
    private HelpService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(HelpRepositoryInterface::class);
        $this->service = new HelpService($this->repo);
    }

    public function testGetPaymentMethodsDelegates(): void
    {
        $c = new PaymentMethodCollection();
        $this->repo->expects($this->once())->method('getPaymentMethods')->willReturn($c);
        $this->assertSame($c, $this->service->getPaymentMethods());
    }

    public function testGetDeliveryDelegates(): void
    {
        $c = new DeliveryItemCollection();
        $this->repo->expects($this->once())->method('getDelivery')->willReturn($c);
        $this->assertSame($c, $this->service->getDelivery());
    }

    public function testGetExchangeStepsDelegates(): void
    {
        $c = new HelpStepCollection();
        $this->repo->expects($this->once())->method('getExchangeSteps')->willReturn($c);
        $this->assertSame($c, $this->service->getExchangeSteps());
    }

    public function testGetRefundStepsDelegates(): void
    {
        $c = new HelpStepCollection();
        $this->repo->expects($this->once())->method('getRefundSteps')->willReturn($c);
        $this->assertSame($c, $this->service->getRefundSteps());
    }

    public function testGetServiceFeaturesDelegates(): void
    {
        $c = new ServiceFeatureCollection();
        $this->repo->expects($this->once())->method('getServiceFeatures')->willReturn($c);
        $this->assertSame($c, $this->service->getServiceFeatures());
    }

    public function testGetServiceHeroDelegates(): void
    {
        $hero = new ServiceHeroDto(id: 1, name: 'X');
        $this->repo->expects($this->once())->method('getServiceHero')->willReturn($hero);
        $this->assertSame($hero, $this->service->getServiceHero());
    }

    public function testGetServiceHeroNullWhenAbsent(): void
    {
        $this->repo->method('getServiceHero')->willReturn(null);
        $this->assertNull($this->service->getServiceHero());
    }

    public function testGetServiceCardsDelegates(): void
    {
        $c = new ServiceCardCollection();
        $this->repo->expects($this->once())->method('getServiceCards')->willReturn($c);
        $this->assertSame($c, $this->service->getServiceCards());
    }

    public function testRepositoryExceptionPropagates(): void
    {
        $this->repo->method('getPaymentMethods')->willThrowException(new \RuntimeException('boom'));
        $this->expectException(\RuntimeException::class);
        $this->service->getPaymentMethods();
    }
}
