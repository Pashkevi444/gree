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
}
