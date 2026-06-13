<?php

declare(strict_types=1);

namespace Gree\Helpers;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Core\App;
use Gree\Support\HtmlText;

/** Статический фасад над i18n — для шаблонов; в сервисах инжектируйте TranslatorServiceInterface + LanguageServiceInterface. */
final class Language extends BaseHelper
{
    /**
     * Возвращает HtmlText — Blade `{{ }}` НЕ экранирует, inline-теги переводов рендерятся как HTML.
     * В strict_types-контексте для string-параметров нужен явный (string)-каст.
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

    /** ISO-дата → «1 мая 2026» / «1 may 2026». Без IntlDateFormatter (не гарантирован на всех хостах). */
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
