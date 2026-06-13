<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\B2bCardCollection;
use Gree\Collection\CompanyLogoCollection;
use Gree\Collection\HowItWorksCardCollection;
use Gree\Contract\Repository\PartnersRepositoryInterface;
use Gree\Service\PartnersService;
use PHPUnit\Framework\TestCase;

final class PartnersServiceTest extends TestCase
{
    private PartnersRepositoryInterface $repo;
    private PartnersService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(PartnersRepositoryInterface::class);
        $this->service = new PartnersService($this->repo);
    }

    public function testGetB2bDelegates(): void
    {
        $c = new B2bCardCollection();
        $this->repo->expects($this->once())->method('getB2b')->willReturn($c);
        $this->assertSame($c, $this->service->getB2b());
    }

    public function testGetHowItWorksDelegates(): void
    {
        $c = new HowItWorksCardCollection();
        $this->repo->expects($this->once())->method('getHowItWorks')->willReturn($c);
        $this->assertSame($c, $this->service->getHowItWorks());
    }

    public function testGetCompaniesDelegates(): void
    {
        $c = new CompanyLogoCollection();
        $this->repo->expects($this->once())->method('getCompanies')->willReturn($c);
        $this->assertSame($c, $this->service->getCompanies());
    }

    public function testRepositoryExceptionPropagates(): void
    {
        $this->repo->method('getB2b')->willThrowException(new \RuntimeException('boom'));
        $this->expectException(\RuntimeException::class);
        $this->service->getB2b();
    }
}
