<?php

declare(strict_types=1);

namespace Gree\Http;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Http\HttpContextInterface;

/**
 * Базовый абстрактный класс для реализаций {@see HttpContextInterface}.
 *
 * Сейчас не разделяет состояние между {@see BitrixHttpContext} (прод) и
 * {@see InMemoryHttpContext} (тесты) — Bitrix-вариант общается с
 * Application::getInstance() и держит свою queue, in-memory просто массивы.
 *
 * База существует, чтобы:
 *   - типизировать «семейство контекстов» одним абстрактным предком (можно
 *     писать `BaseHttpContext` в сигнатурах, если интерфейса мало);
 *   - дать одну точку для общей логики, когда она появится (например,
 *     нормализация имени куки, регрессии safetly-headers).
 */
abstract class BaseHttpContext implements HttpContextInterface
{
    abstract public function getCookie(string $name): ?string;

    abstract public function setCookie(string $name, string $value, CookieOptions $options = new CookieOptions()): void;

    abstract public function getHeader(string $name): ?string;

    abstract public function getRequestMethod(): string;

    abstract public function isHttps(): bool;

    abstract public function getRemoteAddress(): ?string;

    abstract public function getRequestUri(): ?string;

    abstract public function flushCookiesInto(HttpResponse $response): void;
}
