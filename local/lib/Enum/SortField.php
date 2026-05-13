<?php

declare(strict_types=1);

namespace Gree\Enum;

enum SortField: string
{
    case Popular = 'popular';
    case PriceAsc = 'price_asc';
    case PriceDesc = 'price_desc';

    public static function default(): self
    {
        return self::Popular;
    }
}
