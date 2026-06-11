<?php

declare(strict_types=1);

namespace Gree\Helpers;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Core\App;
use Gree\Support\HtmlText;

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
final class Language extends BaseHelper
{
    /**
     * Возвращает HtmlText (Htmlable + Stringable + JsonSerializable):
     * Blade `{{ }}` НЕ экранирует результат — inline-теги (<br>, <strong>)
     * из переводов рендерятся как разметка. В строковых контекстах работает
     * __toString; в PHP-коде со strict_types для string-параметров нужен
     * явный (string)-каст.
     *
     * @param array<string, string|int|float> $params
     */
    public static function t(string $code, array $params = []): HtmlText
    {
        return new HtmlText(
            App::get(TranslatorServiceInterface::class)
                ->translate($code, App::get(LanguageServiceInterface::class)->get(), $params)
        );
    }

    /**
     * Форматирует ISO-дату (`Y-m-d` или любой strtotime-парсимый ввод) в
     * локализованную строку «1 мая 2026» / «1 may 2026». Пустой ввод → ''.
     *
     * Без IntlDateFormatter сознательно: intl-расширение не гарантировано на
     * всех хостах, словарь нужен всего на 2 локали.
     */
    public static function date(string $iso): string
    {
        if ($iso === '') {
            return '';
        }
        $ts = strtotime($iso);
        if ($ts === false) {
            return '';
        }

        static $months = [
            'ru' => [
                1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
                'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
            ],
            'uz' => [
                1 => 'yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun',
                'iyul', 'avgust', 'sentyabr', 'oktyabr', 'noyabr', 'dekabr',
            ],
        ];
        $locale = App::get(LanguageServiceInterface::class)->get()->value;
        $dict = $months[$locale] ?? $months['ru'];

        $day = (int) date('j', $ts);
        $month = $dict[(int) date('n', $ts)];
        $year = (int) date('Y', $ts);

        return "{$day} {$month} {$year}";
    }
}
