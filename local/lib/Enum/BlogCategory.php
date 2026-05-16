<?php

declare(strict_types=1);

namespace Gree\Enum;

enum BlogCategory: string
{
    case Tips = 'tips';
    case News = 'news';

    public function label(): string
    {
        return match ($this) {
            self::Tips => 'Советы',
            self::News => 'Новости',
        };
    }

    public static function tryFromOrNull(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }
        return self::tryFrom($value);
    }
}
