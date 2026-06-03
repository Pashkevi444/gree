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
        $this->assertCount(34, IblockCode::cases());
    }

    public function testWhereToBuyCasesPresent(): void
    {
        $this->assertSame('WhereToBuyLocations', IblockCode::WhereToBuyLocations->value);
        $this->assertSame('WhereToBuyPartners',  IblockCode::WhereToBuyPartners->value);
        $this->assertSame('WhereToBuyChains',    IblockCode::WhereToBuyChains->value);
    }

    public function testPartnersCasesPresent(): void
    {
        $this->assertSame('PartnersB2b',            IblockCode::PartnersB2b->value);
        $this->assertSame('PartnersHowItWorks',     IblockCode::PartnersHowItWorks->value);
        $this->assertSame('PartnersCompaniesTrust', IblockCode::PartnersCompaniesTrust->value);
    }

    public function testFooterMenuCasePresent(): void
    {
        $this->assertSame('FooterMenu', IblockCode::FooterMenu->value);
    }

    public function testContactsCasesPresent(): void
    {
        $this->assertSame('ContactsChannels',  IblockCode::ContactsChannels->value);
        $this->assertSame('ContactsAddresses', IblockCode::ContactsAddresses->value);
    }

    public function testHelpCasesPresent(): void
    {
        $this->assertSame('HelpPaymentMethods',  IblockCode::HelpPaymentMethods->value);
        $this->assertSame('HelpDelivery',        IblockCode::HelpDelivery->value);
        $this->assertSame('HelpExchangeSteps',   IblockCode::HelpExchangeSteps->value);
        $this->assertSame('HelpRefundSteps',     IblockCode::HelpRefundSteps->value);
        $this->assertSame('HelpServiceFeatures', IblockCode::HelpServiceFeatures->value);
        $this->assertSame('HelpServiceHero',     IblockCode::HelpServiceHero->value);
        $this->assertSame('HelpServiceCards',    IblockCode::HelpServiceCards->value);
    }

    public function testTryFromReturnsNullOnInvalid(): void
    {
        $this->assertNull(IblockCode::tryFrom('UnknownBlock'));
    }
}
