<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\Service\HomeService;
use PHPUnit\Framework\TestCase;

final class HomeServiceTest extends TestCase
{
    private HomeRepositoryInterface $repo;
    private HomeService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(HomeRepositoryInterface::class);
        $this->service = new HomeService($this->repo);
    }

    public function testGetSliderDelegatesToRepository(): void
    {
        $collection = new SliderItemCollection();
        $this->repo->expects($this->once())->method('getSlider')->willReturn($collection);

        $this->assertSame($collection, $this->service->getSlider());
    }

    public function testGetGreeCardsDelegatesToRepository(): void
    {
        $collection = new GreeCardCollection();
        $this->repo->expects($this->once())->method('getGreeCards')->willReturn($collection);

        $this->assertSame($collection, $this->service->getGreeCards());
    }

    public function testGetGreeStatsDelegatesToRepository(): void
    {
        $collection = new GreeStatCollection();
        $this->repo->expects($this->once())->method('getGreeStats')->willReturn($collection);

        $this->assertSame($collection, $this->service->getGreeStats());
    }

    public function testGetAppFeaturesDelegatesToRepository(): void
    {
        $collection = new AppFeatureCollection();
        $this->repo->expects($this->once())->method('getAppFeatures')->willReturn($collection);

        $this->assertSame($collection, $this->service->getAppFeatures());
    }

    public function testGetTechnologiesDelegatesToRepository(): void
    {
        $collection = new TechnologyCollection();
        $this->repo->expects($this->once())->method('getTechnologies')->willReturn($collection);

        $this->assertSame($collection, $this->service->getTechnologies());
    }
}
