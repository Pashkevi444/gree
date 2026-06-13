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

    /**
     * URL-slug категории — это часть публичного URL вида /blog/{slug}/.
     * Намеренно отличается от value enum'а: исторически у нас Tips='tips',
     * а в URL «советы» = `advice` (отдельная страница списка).
     */
    public function urlSlug(): string
    {
        return match ($this) {
            self::Tips => 'advice',
            self::News => 'news',
        };
    }

    public static function fromUrlSlug(string $slug): ?self
    {
        return match ($slug) {
            'advice' => self::Tips,
            'news'   => self::News,
            default  => null,
        };
    }
}
