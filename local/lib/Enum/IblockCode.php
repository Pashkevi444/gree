<?php

declare(strict_types=1);

namespace Gree\Enum;

enum IblockCode: string
{
    // Каталог
    case Brands = 'Brands';
    case Products = 'Products';
    case ProductsOffers = 'ProductsOffers';
    case CatalogGreeCards = 'CatalogGreeCards';
    case CatalogGreeStats = 'CatalogGreeStats';

    // Контент
    case Blog = 'Blog';
    case Menu = 'Menu';
    case FooterMenu = 'FooterMenu';

    // Страница бренда
    case BrandHistory = 'BrandHistory';
    case BrandWhyGree = 'BrandWhyGree';
    case BrandGreeCards = 'BrandGreeCards';
    case BrandGreeStats = 'BrandGreeStats';
    case BrandAboutCards = 'BrandAboutCards';
    case BrandTechnologies = 'BrandTechnologies';

    // Главная страница
    case HomeSlider = 'HomeSlider';
    case HomeGreeCards = 'HomeGreeCards';
    case HomeGreeStats = 'HomeGreeStats';
    case HomeAppFeatures = 'HomeAppFeatures';
    case HomeTechnologies = 'HomeTechnologies';

    // Страница помощи /help/
    case HelpPaymentMethods = 'HelpPaymentMethods';
    case HelpDelivery = 'HelpDelivery';
    case HelpExchangeSteps = 'HelpExchangeSteps';
    case HelpRefundSteps = 'HelpRefundSteps';
    case HelpServiceFeatures = 'HelpServiceFeatures';
    case HelpServiceHero = 'HelpServiceHero';
    case HelpServiceCards = 'HelpServiceCards';

    // Страница контактов /contacts/
    case ContactsChannels = 'ContactsChannels';
    case ContactsAddresses = 'ContactsAddresses';

    // Страница «Где купить» /where-to-buy/
    case WhereToBuyLocations = 'WhereToBuyLocations';
    case WhereToBuyPartners = 'WhereToBuyPartners';
    case WhereToBuyChains = 'WhereToBuyChains';

    // Страница «Партнёрам» /partners/
    case PartnersB2b = 'PartnersB2b';
    case PartnersHowItWorks = 'PartnersHowItWorks';
    case PartnersCompaniesTrust = 'PartnersCompaniesTrust';
}
