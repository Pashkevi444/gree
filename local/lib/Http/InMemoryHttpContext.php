<?php

declare(strict_types=1);

namespace Gree\Http;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Http\HttpContextInterface;

/**
 * In-memory HttpContext for unit tests. Holds cookies and headers as plain
 * arrays; flushCookiesInto() is a no-op (tests assert on the queue directly
 * if they need to).
 */
final class InMemoryHttpContext implements HttpContextInterface
{
    /** @var array<string, string> */
    private array $cookies = [];
    /** @var array<string, string> */
    private array $headers = [];
    private bool $https = true;

    public function __construct(
        private string $requestMethod = 'GET',
        private ?string $remoteAddress = '127.0.0.1',
        private ?string $requestUri = '/',
    ) {}

    public function getCookie(string $name): ?string
    {
        return $this->cookies[$name] ?? null;
    }

    public function setCookie(string $name, string $value, CookieOptions $options = new CookieOptions()): void
    {
        $this->cookies[$name] = $value;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[strtolower($name)] = $value;
    }

    public function setRequestMethod(string $method): void
    {
        $this->requestMethod = $method;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function isHttps(): bool
    {
        return $this->https;
    }

    public function getRemoteAddress(): ?string
    {
        return $this->remoteAddress;
    }

    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }

    public function flushCookiesInto(HttpResponse $response): void
    {
        // no-op: tests inspect cookies via getCookie() instead.
    }
}
