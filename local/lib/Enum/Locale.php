<?php

declare(strict_types=1);

namespace Gree\Enum;

enum Locale: string
{
    case Ru = 'ru';
    case Uz = 'uz';

    private const CIS_LANGUAGES = [
        'ru', // Russian
        'uk', // Ukrainian
        'be', // Belarusian
        'kk', // Kazakh
        'ky', // Kyrgyz
        'tg', // Tajik
        'tk', // Turkmen
        'hy', // Armenian
        'az', // Azerbaijani
        'mo', // Moldovan
        'ro', // Romanian (Moldova fallback)
    ];

    public static function default(): self
    {
        return self::Ru;
    }

    public function label(): string
    {
        return match ($this) {
            self::Ru => 'Рус',
            self::Uz => 'Узб',
        };
    }

    /**
     * Regex-альтернатива из всех value (для роутера `where('locale', …)`).
     * Автоматически расширяется при добавлении нового case'а.
     */
    public static function pattern(): string
    {
        return implode('|', array_column(self::cases(), 'value'));
    }

    /**
     * Detect locale from an Accept-Language header.
     *   uz tag wins (узбекистанский сайт — родной язык приоритетнее);
     *   CIS-language → Ru (русский — fallback для русскоязычных гостей);
     *   всё остальное / пустой header → default (Ru).
     *
     * English как локаль больше не существует — иностранные посетители,
     * не попавшие в CIS-список, получат русскую версию.
     */
    public static function fromAcceptLanguage(?string $header): self
    {
        if ($header === null || trim($header) === '') {
            return self::default();
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0] ?? ''));
            if ($tag === '') {
                continue;
            }
            $primary = explode('-', $tag)[0];

            if ($primary === 'uz') {
                return self::Uz;
            }
            if (in_array($primary, self::CIS_LANGUAGES, true)) {
                return self::Ru;
            }
        }

        return self::default();
    }
}
