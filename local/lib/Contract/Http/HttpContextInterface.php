<?php

declare(strict_types=1);

namespace Gree\Contract\Http;

use Bitrix\Main\HttpResponse;
use Gree\Http\CookieOptions;

/** Тонкий фасад над Bitrix HTTP-стеком — нужен для подмены в тестах и чтоб сервисы не тыкали $_COOKIE / setcookie() напрямую. */
interface HttpContextInterface
{
    /** Имя как пришло от браузера — Bitrix-префиксы не применяются. */
    public function getCookie(string $name): ?string;

    /** Кука встаёт в очередь; на провод уйдёт когда контроллер вызовет flushCookiesInto(). */
    public function setCookie(string $name, string $value, CookieOptions $options = new CookieOptions()): void;

    public function getHeader(string $name): ?string;

    public function getRequestMethod(): string;

    public function isHttps(): bool;

    public function getRemoteAddress(): ?string;

    public function getRequestUri(): ?string;

    /** Идемпотентно: очередь чистится, повторный flush — no-op. */
    public function flushCookiesInto(HttpResponse $response): void;
}
