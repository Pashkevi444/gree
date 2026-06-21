<?php

declare(strict_types=1);

namespace Gree\Enum;

enum Color: string
{
    case White = 'white';
    case Black = 'black';
    case Gold = 'gold';
    case Blue = 'blue';
    case Silver = 'silver';

    public function hex(): string
    {
        return match ($this) {
            self::White => '#ffffff',
            self::Black => '#000000',
            self::Gold => '#d4af37',
            self::Blue => '#2f40d5',
            self::Silver => '#8c8c8c',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::White => 'Белый',
            self::Black => 'Чёрный',
            self::Gold => 'Золотой',
            self::Blue => 'Синий',
            self::Silver => 'Серебряный',
        };
    }
}
