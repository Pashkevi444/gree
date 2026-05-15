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

    /**
     * URL slug used in /catalog/{slug}/ routes. Kept separate from the
     * machine-readable `value` because the slug is user-facing Russian
     * transliteration and must stay stable for SEO even if internal
     * enum values change.
     */
    public function slug(): string
    {
        return match ($this) {
            self::Wall => 'nastennie',
            self::Column => 'kolonnye',
            self::Industrial => 'promyshlennye',
        };
    }

    public static function fromSlug(string $slug): ?self
    {
        return match ($slug) {
            'nastennie' => self::Wall,
            'kolonnye' => self::Column,
            'promyshlennye' => self::Industrial,
            default => null,
        };
    }
}
