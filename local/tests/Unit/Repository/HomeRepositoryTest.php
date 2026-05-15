<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Repository;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\Locale;
use Gree\Repository\HomeRepository;
use PHPUnit\Framework\TestCase;

final class HomeRepositoryTest extends TestCase
{
    private HomeRepository $repo;

    protected function setUp(): void
    {
        $language = $this->createMock(LanguageServiceInterface::class);
        $language->method('get')->willReturn(Locale::Ru);
        $this->repo = new HomeRepository($language);
    }

    public function testGetSliderReturnsSliderItemCollection(): void
    {
        $this->assertInstanceOf(SliderItemCollection::class, $this->repo->getSlider());
    }

    public function testGetGreeCardsReturnsGreeCardCollection(): void
    {
        $this->assertInstanceOf(GreeCardCollection::class, $this->repo->getGreeCards());
    }

    public function testGetGreeStatsReturnsGreeStatCollection(): void
    {
        $this->assertInstanceOf(GreeStatCollection::class, $this->repo->getGreeStats());
    }

    public function testGetAppFeaturesReturnsAppFeatureCollection(): void
    {
        $this->assertInstanceOf(AppFeatureCollection::class, $this->repo->getAppFeatures());
    }

    public function testGetTechnologiesReturnsTechnologyCollection(): void
    {
        $this->assertInstanceOf(TechnologyCollection::class, $this->repo->getTechnologies());
    }
}
