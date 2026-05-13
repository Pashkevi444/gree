<?php

declare(strict_types=1);

namespace Gree\Enum;

enum ProductType: string
{
    case Wall = 'wall';
    case Column = 'column';
    case Industrial = 'industrial';

    public function label(): string
    {
        return match ($this) {
            self::Wall => 'Настенный',
            self::Column => 'Колонный',
            self::Industrial => 'Промышленный',
        };
    }
}
