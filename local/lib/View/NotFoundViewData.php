<?php

declare(strict_types=1);

namespace Gree\View;

/**
 * 404 — все строки рендерятся из Language::t, ViewData ничего не несёт.
 * Оставлен как класс ради единообразия (контроллер всегда передаёт DTO).
 */
final readonly class NotFoundViewData extends BaseViewData
{
}
