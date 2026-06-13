<?php

declare(strict_types=1);

namespace Gree\Enum;

enum HlblockCode: string
{
    case Translations = 'Translations';
    case Seo = 'Seo';
    case Carts = 'Carts';
    case CartItems = 'CartItems';
    case Orders = 'Orders';
    case OrderItems = 'OrderItems';
    case CatalogHelpFeedback = 'CatalogHelpFeedback';
}
