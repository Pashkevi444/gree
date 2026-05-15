<?php

declare(strict_types=1);

namespace Gree\Enum;

enum Color: string
{
    case White = 'white';
    case Silver = 'silver';
    case Black = 'black';
    case Champagne = 'champagne';

    public function hex(): string
    {
        return match ($this) {
            self::White => '#ffffff',
            self::Silver => '#8c8c8c',
            self::Black => '#000000',
            self::Champagne => '#f5deb3',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::White => 'Белый',
            self::Silver => 'Серебристый',
            self::Black => 'Чёрный',
            self::Champagne => 'Шампань',
        };
    }
}
