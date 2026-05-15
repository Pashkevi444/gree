<?php

declare(strict_types=1);

namespace Gree\Enum;

enum Locale: string
{
    case Ru = 'ru';
    case En = 'en';

    private const CIS_LANGUAGES = [
        'ru', // Russian
        'uk', // Ukrainian
        'be', // Belarusian
        'kk', // Kazakh
        'ky', // Kyrgyz (also 'kg' in some lists)
        'uz', // Uzbek
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
            self::En => 'Eng',
        };
    }

    /**
     * Detect locale from an Accept-Language header.
     * CIS-language preference → Ru; everything else (including empty/null) → En.
     */
    public static function fromAcceptLanguage(?string $header): self
    {
        if ($header === null || trim($header) === '') {
            return self::En;
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0] ?? ''));
            if ($tag === '') {
                continue;
            }
            $primary = explode('-', $tag)[0];
            if (in_array($primary, self::CIS_LANGUAGES, true)) {
                return self::Ru;
            }
        }

        return self::En;
    }
}
