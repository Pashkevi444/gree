<?php

declare(strict_types=1);

namespace Gree\Http;

use Bitrix\Main\Application;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\Cookie;

/** Cookies без BITRIX_SM_-префикса (getCookieRaw / addPrefix=false). Queue зеркалится в RAM — get внутри запроса видит свежевыставленные значения. */
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
        // CLI (интеграционные тесты): request==null, читаем напрямую из $_COOKIE.
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
        // CLI без HTTP-контекста — GET (не state-changing, ApiGuard его не валидирует).
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

    /** Под CLI (PHPUnit integration, sprint.migration, агенты) context==null — caller обязан обработать null. */
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
