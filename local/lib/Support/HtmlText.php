<?php

declare(strict_types=1);

namespace Gree\Support;

use Illuminate\Support\HtmlString;

/**
 * Htmlable + Stringable + JsonSerializable. Blade `{{ }}` НЕ экранирует Htmlable,
 * inline-HTML переводов рендерится как разметка. В strict_types для string-параметров — явный (string)-каст.
 */
final class HtmlText extends HtmlString implements \JsonSerializable
{
    public function jsonSerialize(): string
    {
        return $this->toHtml();
    }
}
