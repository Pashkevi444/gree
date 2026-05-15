<?php

declare(strict_types=1);

namespace Gree\Helpers;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Core\App;

/**
 * Static facade over the i18n stack — meant for use in templates / static
 * contexts where DI injection is impractical:
 *
 *   <?= Language::t('header.catalog') ?>
 *   {{ \Gree\Helpers\Language::t('catalog.found', ['count' => $total]) }}
 *
 * Inside services / controllers / repositories prefer injecting
 * TranslatorServiceInterface + LanguageServiceInterface explicitly.
 */
final class Language
{
    /**
     * @param array<string, string|int|float> $params
     */
    public static function t(string $code, array $params = []): string
    {
        return App::get(TranslatorServiceInterface::class)
            ->translate($code, App::get(LanguageServiceInterface::class)->get(), $params);
    }
}
