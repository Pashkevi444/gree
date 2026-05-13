<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\DTO\BrandHistoryDto;
use Gree\DTO\BrandWhyGreeDto;
use Gree\Service\BrandService;
use PHPUnit\Framework\TestCase;

final class BrandServiceTest extends TestCase
{
    private BrandRepositoryInterface $repo;
    private BrandService $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(BrandRepositoryInterface::class);
        $this->service = new BrandService($this->repo);
    }

    public function testGetHistoryDelegatesToRepository(): void
    {
        $dto = new BrandHistoryDto(id: 1, name: 'История', text: 'Текст');
        $this->repo->method('getHistory')->willReturn($dto);

        $this->assertSame($dto, $this->service->getHistory());
    }

    public function testGetWhyGreeDelegatesToRepository(): void
    {
        $dto = new BrandWhyGreeDto(id: 1, name: 'Почему', description: 'Текст');
        $this->repo->method('getWhyGree')->willReturn($dto);

        $this->assertSame($dto, $this->service->getWhyGree());
    }

    public function testGetGreeCardsDelegatesToRepository(): void
    {
        $collection = new GreeCardCollection();
        $this->repo->method('getGreeCards')->willReturn($collection);

        $this->assertSame($collection, $this->service->getGreeCards());
    }

    public function testGetGreeStatsDelegatesToRepository(): void
    {
        $collection = new GreeStatCollection();
        $this->repo->method('getGreeStats')->willReturn($collection);

        $this->assertSame($collection, $this->service->getGreeStats());
    }

    public function testGetAboutCardsDelegatesToRepository(): void
    {
        $collection = new BrandAboutCardCollection();
        $this->repo->method('getAboutCards')->willReturn($collection);

        $this->assertSame($collection, $this->service->getAboutCards());
    }

    public function testGetTechnologiesDelegatesToRepository(): void
    {
        $collection = new TechnologyCollection();
        $this->repo->method('getTechnologies')->willReturn($collection);

        $this->assertSame($collection, $this->service->getTechnologies());
    }
}
