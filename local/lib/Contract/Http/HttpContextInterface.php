<?php

declare(strict_types=1);

namespace Gree\Contract\Http;

use Bitrix\Main\HttpResponse;
use Gree\Http\CookieOptions;

/**
 * Thin façade over the Bitrix request/response stack. Services and security
 * code depend on this contract instead of touching $_COOKIE / $_SERVER /
 * setcookie() directly. Two reasons:
 *
 *   1. Tests can swap an in-memory implementation in.
 *   2. Bitrix already exposes context-aware request/response objects; using
 *      them is the framework-native way and survives Bitrix internals
 *      changing (e.g. cookie name prefixing, CookieCrypter, session policies).
 */
interface HttpContextInterface
{
    /**
     * Read an incoming cookie value. Bitrix's prefix machinery is bypassed —
     * raw names are returned exactly as the browser sent them.
     */
    public function getCookie(string $name): ?string;

    /**
     * Queue an outgoing cookie. Actually written to the wire when the active
     * controller drains the queue into its HttpResponse via flushCookiesInto().
     * Subsequent getCookie() calls in the same request see the queued value.
     */
    public function setCookie(string $name, string $value, CookieOptions $options = new CookieOptions()): void;

    public function getHeader(string $name): ?string;

    public function getRequestMethod(): string;

    public function isHttps(): bool;

    public function getRemoteAddress(): ?string;

    public function getRequestUri(): ?string;

    /**
     * Move every queued cookie onto the given response. Idempotent — the
     * queue is cleared so a second flush is a no-op.
     */
    public function flushCookiesInto(HttpResponse $response): void;
}
