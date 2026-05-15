<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\IblockCode;
use PHPUnit\Framework\TestCase;

final class IblockCodeTest extends TestCase
{
    public function testAllCasesExist(): void
    {
        $this->assertSame(IblockCode::Brands, IblockCode::from('Brands'));
        $this->assertSame(IblockCode::Products, IblockCode::from('Products'));
        $this->assertSame(IblockCode::Blog, IblockCode::from('Blog'));
        $this->assertSame(IblockCode::BrandHistory, IblockCode::from('BrandHistory'));
        $this->assertSame(IblockCode::BrandWhyGree, IblockCode::from('BrandWhyGree'));
        $this->assertSame(IblockCode::BrandGreeCards, IblockCode::from('BrandGreeCards'));
        $this->assertSame(IblockCode::BrandGreeStats, IblockCode::from('BrandGreeStats'));
        $this->assertSame(IblockCode::BrandAboutCards, IblockCode::from('BrandAboutCards'));
        $this->assertSame(IblockCode::BrandTechnologies, IblockCode::from('BrandTechnologies'));
        $this->assertSame(IblockCode::HomeSlider, IblockCode::from('HomeSlider'));
        $this->assertSame(IblockCode::HomeGreeCards, IblockCode::from('HomeGreeCards'));
        $this->assertSame(IblockCode::HomeGreeStats, IblockCode::from('HomeGreeStats'));
        $this->assertSame(IblockCode::HomeAppFeatures, IblockCode::from('HomeAppFeatures'));
        $this->assertSame(IblockCode::HomeTechnologies, IblockCode::from('HomeTechnologies'));
    }

    public function testValues(): void
    {
        $this->assertSame('BrandHistory', IblockCode::BrandHistory->value);
        $this->assertSame('BrandWhyGree', IblockCode::BrandWhyGree->value);
        $this->assertSame('BrandGreeCards', IblockCode::BrandGreeCards->value);
        $this->assertSame('BrandGreeStats', IblockCode::BrandGreeStats->value);
        $this->assertSame('BrandAboutCards', IblockCode::BrandAboutCards->value);
        $this->assertSame('BrandTechnologies', IblockCode::BrandTechnologies->value);
        $this->assertSame('HomeSlider', IblockCode::HomeSlider->value);
        $this->assertSame('HomeGreeCards', IblockCode::HomeGreeCards->value);
        $this->assertSame('HomeGreeStats', IblockCode::HomeGreeStats->value);
        $this->assertSame('HomeAppFeatures', IblockCode::HomeAppFeatures->value);
        $this->assertSame('HomeTechnologies', IblockCode::HomeTechnologies->value);
    }

    public function testCasesCount(): void
    {
        $this->assertCount(16, IblockCode::cases());
    }

    public function testTryFromReturnsNullOnInvalid(): void
    {
        $this->assertNull(IblockCode::tryFrom('UnknownBlock'));
    }
}
