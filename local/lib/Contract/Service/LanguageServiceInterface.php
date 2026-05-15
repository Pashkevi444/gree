<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Enum\Locale;

interface LanguageServiceInterface
{
    public function get(): Locale;

    public function set(Locale $locale): void;

    public function detectAndStore(\Bitrix\Main\HttpRequest $request): Locale;
}
