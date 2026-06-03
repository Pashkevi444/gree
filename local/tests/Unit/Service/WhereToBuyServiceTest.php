<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\ChainLogoCollection;
use Gree\Collection\PartnerLogoCollection;
use Gree\Collection\WhereToBuyLocationCollection;
use Gree\Contract\Repository\WhereToBuyRepositoryInterface;
use Gree\Service\WhereToBuyService;
use PHPUnit\Framework\TestCase;

final class WhereToBuyServiceTest extends TestCase
{
    private WhereToBuyRepositoryInterface $repo;
    private WhereToBuyService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(WhereToBuyRepositoryInterface::class);
        $this->service = new WhereToBuyService($this->repo);
    }

    public function testGetLocationsDelegates(): void
    {
        $c = new WhereToBuyLocationCollection();
        $this->repo->expects($this->once())->method('getLocations')->willReturn($c);
        $this->assertSame($c, $this->service->getLocations());
    }

    public function testGetPartnersDelegates(): void
    {
        $c = new PartnerLogoCollection();
        $this->repo->expects($this->once())->method('getPartners')->willReturn($c);
        $this->assertSame($c, $this->service->getPartners());
    }

    public function testGetChainsDelegates(): void
    {
        $c = new ChainLogoCollection();
        $this->repo->expects($this->once())->method('getChains')->willReturn($c);
        $this->assertSame($c, $this->service->getChains());
    }

    public function testRepositoryExceptionPropagates(): void
    {
        $this->repo->method('getLocations')->willThrowException(new \RuntimeException('boom'));
        $this->expectException(\RuntimeException::class);
        $this->service->getLocations();
    }
}
