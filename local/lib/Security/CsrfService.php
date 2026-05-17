<?php

declare(strict_types=1);

namespace Gree\Security;

use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Http\CookieOptions;
use Gree\Service\BaseService;

/**
 * Double-submit cookie pattern.
 *
 * The CSRF token is written to a `csrf_token` cookie that is NOT HttpOnly so
 * page JS can read it and mirror the value into a request header. The server
 * compares header value with the cookie via constant-time compare. Because
 * cross-origin scripts can't read our cookies (SameSite=Strict on the cookie
 * AND CORS forbids reading set-cookie cross-origin), an attacker has no way
 * to fabricate the header — request fails.
 *
 * Why not store the token in HttpOnly cookie like cart_token: same-origin JS
 * must read it to forward in a header. HttpOnly would defeat that. The token
 * is meaningless without a matching cookie, so leaking it via JS to a same-
 * origin XSS is no worse than the XSS itself.
 */
final class CsrfService extends BaseService implements CsrfServiceInterface
{
    public const string COOKIE_NAME = 'csrf_token';
    private const int LIFETIME_SECONDS = 31_536_000; // 1 year

    public function __construct(
        private readonly HttpContextInterface $http,
    ) {}

    public function read(): ?string
    {
        $raw = $this->http->getCookie(self::COOKIE_NAME);
        if ($raw === null) {
            return null;
        }
        $raw = trim($raw);
        if (!preg_match('/^[0-9a-f]{64}$/', $raw)) {
            return null;
        }
        return $raw;
    }

    public function issue(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->http->setCookie(self::COOKIE_NAME, $token, new CookieOptions(
            lifetimeSeconds: self::LIFETIME_SECONDS,
            httpOnly: false,           // JS must mirror this into a header
            sameSite: 'Strict',
        ));
        return $token;
    }

    public function readOrIssue(): string
    {
        return $this->read() ?? $this->issue();
    }

    public function matches(string $sent): bool
    {
        $expected = $this->read();
        if ($expected === null || $sent === '') {
            return false;
        }
        return hash_equals($expected, $sent);
    }
}
