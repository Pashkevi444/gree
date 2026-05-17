<?php

declare(strict_types=1);

namespace Gree\Helpers;

/**
 * Маркер-база для static-facade хелперов (Language, Route и т. д.).
 *
 * Каждый хелпер — `final` с только статическими методами, инстанцировать
 * его никто не должен. Запрещаем создание через private-конструктор в базе:
 * наследникам не нужно дублировать эту защиту.
 *
 * Тело почти пустое. Если в будущем все хелперы получат общий механизм
 * (например, единая точка для DI-resolve через App::get или общий cache),
 * — переедет сюда.
 */
abstract class BaseHelper
{
    /** Static-only API: запрещаем `new` для всех наследников. */
    private function __construct() {}
}
