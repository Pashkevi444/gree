<?php

declare(strict_types=1);

namespace Gree\Http;

use Bitrix\Main\Application;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\Cookie;

/**
 * Bitrix-backed HttpContext.
 *
 * Cookies:
 *   - `getCookie()` reads via HttpRequest::getCookieRaw() because our cookies
 *     do NOT carry the BITRIX_SM_ prefix Bitrix's regular getCookie() expects.
 *   - `setCookie()` queues a Web\Cookie with `addPrefix=false` so the raw name
 *     stays intact across the round-trip. The cookie is materialised on the
 *     response inside flushCookiesInto().
 *
 * Same-request consistency: queued cookies are mirrored into an in-memory
 * cache, so `setCookie('x', 'y')` followed by `getCookie('x')` returns `'y'`
 * even though Bitrix's request cookie list is immutable.
 */
final class BitrixHttpContext extends BaseHttpContext
{
    /** @var array<string, string> Cookies queued for the outgoing response. */
    private array $queued = [];

    /** @var array<string, CookieOptions> */
    private array $queuedOptions = [];

    public function getCookie(string $name): ?string
    {
        if (array_key_exists($name, $this->queued)) {
            return $this->queued[$name];
        }
        // В CLI (интеграционные тесты с bootstrap'ом Bitrix без HTTP-контекста)
        // request() == null. Туда читаем напрямую из $_COOKIE — тест-кейс уже
        // выставляет в него значения. Прод-режим всегда идёт через Bitrix.
        $request = $this->request();
        if ($request === null) {
            $raw = $_COOKIE[$name] ?? null;
            return is_string($raw) ? $raw : null;
        }
        $raw = $request->getCookieRaw($name);
        return is_string($raw) ? $raw : null;
    }

    public function setCookie(string $name, string $value, CookieOptions $options = new CookieOptions()): void
    {
        $this->queued[$name] = $value;
        $this->queuedOptions[$name] = $options;
    }

    public function getHeader(string $name): ?string
    {
        $request = $this->request();
        if ($request === null) {
            return null;
        }
        $val = $request->getHeader($name);
        return $val !== null && $val !== '' ? (string) $val : null;
    }

    public function getRequestMethod(): string
    {
        $request = $this->request();
        // CLI без HTTP-контекста — отдаём GET (безопасный безопасный метод; для
        // тестов он не state-changing, ApiGuard его не валидирует).
        return $request !== null ? $request->getRequestMethod() : 'GET';
    }

    public function isHttps(): bool
    {
        $request = $this->request();
        return $request !== null && $request->isHttps();
    }

    public function getRemoteAddress(): ?string
    {
        $request = $this->request();
        if ($request === null) {
            return null;
        }
        $addr = $request->getRemoteAddress();
        return $addr !== '' ? $addr : null;
    }

    public function getRequestUri(): ?string
    {
        $request = $this->request();
        if ($request === null) {
            return null;
        }
        $uri = $request->getRequestUri();
        return $uri !== '' ? $uri : null;
    }

    public function flushCookiesInto(HttpResponse $response): void
    {
        if (!$this->queued) {
            return;
        }
        $isHttps = $this->isHttps();
        foreach ($this->queued as $name => $value) {
            $opt = $this->queuedOptions[$name] ?? new CookieOptions();
            $expires = $opt->lifetimeSeconds > 0 ? time() + $opt->lifetimeSeconds : 0;

            $cookie = (new Cookie($name, $value, $expires, false))
                ->setPath($opt->path)
                ->setSecure($opt->secure ?? $isHttps)
                ->setHttpOnly($opt->httpOnly)
                ->setSameSite($opt->sameSite);

            $response->addCookie($cookie);
        }
        $this->queued = [];
        $this->queuedOptions = [];
    }

    /**
     * Returns the Bitrix HttpRequest if one is available. Under CLI (PHPUnit
     * integration runs, sprint.migration, agents) the request/context isn't
     * initialised — callers must handle null.
     */
    private function request(): ?\Bitrix\Main\HttpRequest
    {
        $context = Application::getInstance()->getContext();
        if ($context === null) {
            return null;
        }
        $request = $context->getRequest();
        return $request instanceof \Bitrix\Main\HttpRequest ? $request : null;
    }
}
