<?php

declare(strict_types=1);

namespace Gree\Support;

use Illuminate\Support\HtmlString;

/**
 * Строка перевода, которую Blade выводит БЕЗ экранирования.
 *
 * illuminate/view экранирует всё в `{{ }}`, кроме объектов, реализующих
 * Htmlable (у них зовётся toHtml()). HtmlString — стандартная реализация;
 * мы добавляем JsonSerializable, чтобы `@json([... Language::t() ...])`
 * сериализовал значение как строку, а не как пустой объект.
 *
 * Зачем: контент переводов (HL Translations, UF_VALUE_* типа TEXT) может
 * содержать inline-HTML — <br>, <strong> и т.п. Возврат HtmlText из
 * Language::t() включает рендер тегов во всех `{{ Language::t(...) }}`
 * по всему проекту сразу, без перевода шаблонов на `{!! !!}`.
 *
 * ВАЖНО для PHP-кода со strict_types: объект НЕ коэрсится в string-параметры —
 * там, где результат t() передаётся в string-typehint (setMeta, конструкторы
 * ViewData), оборачивайте в (string). В шаблонах (header.php/footer.php/Blade)
 * strict_types нет — работает __toString.
 *
 * Безопасность: переводы пишут только админы — XSS-вектор внутренний
 * (принятый риск, тот же что у `{!! !!}`). Не складывать в переводы <script>.
 */
final class HtmlText extends HtmlString implements \JsonSerializable
{
    public function jsonSerialize(): string
    {
        return $this->toHtml();
    }
}
