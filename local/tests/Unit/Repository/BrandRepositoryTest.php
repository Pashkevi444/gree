<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Repository;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\Locale;
use Gree\Repository\BrandRepository;
use PHPUnit\Framework\TestCase;

final class BrandRepositoryTest extends TestCase
{
    private BrandRepository $repo;

    protected function setUp(): void
    {
        $language = $this->createMock(LanguageServiceInterface::class);
        $language->method('get')->willReturn(Locale::Ru);
        $this->repo = new BrandRepository($language);
    }

    public function testGetHistoryReturnsNullWhenIblockMissing(): void
    {
        $this->assertNull($this->repo->getHistory());
    }

    public function testGetWhyGreeReturnsNullWhenIblockMissing(): void
    {
        $this->assertNull($this->repo->getWhyGree());
    }

    public function testGetGreeCardsReturnsGreeCardCollection(): void
    {
        $this->assertInstanceOf(GreeCardCollection::class, $this->repo->getGreeCards());
    }

    public function testGetGreeStatsReturnsGreeStatCollection(): void
    {
        $this->assertInstanceOf(GreeStatCollection::class, $this->repo->getGreeStats());
    }

    public function testGetAboutCardsReturnsBrandAboutCardCollection(): void
    {
        $this->assertInstanceOf(BrandAboutCardCollection::class, $this->repo->getAboutCards());
    }

    public function testGetTechnologiesReturnsTechnologyCollection(): void
    {
        $this->assertInstanceOf(TechnologyCollection::class, $this->repo->getTechnologies());
    }
}
