<?php

declare(strict_types=1);

namespace Gree\Security;

use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Http\CookieOptions;
use Gree\Service\BaseService;

/** Double-submit: cookie без HttpOnly (JS читает и зеркалит в X-CSRF-Token), SameSite=Strict + CORS блокируют чужие сайты. */
final class CsrfService extends BaseService implements CsrfServiceInterface
{
    public const string COOKIE_NAME = 'csrf_token';
    private const int LIFETIME_SECONDS = 31536000; // 1 год

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
            httpOnly: false, // JS зеркалит в X-CSRF-Token
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
