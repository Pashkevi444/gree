<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Enum\Locale;

interface TranslatorServiceInterface
{
    /**
     * Translate code by locale, with optional :placeholder replacements.
     *
     * @param array<string, string|int|float> $params
     */
    public function translate(string $code, Locale $locale, array $params = []): string;
}
