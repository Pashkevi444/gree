<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\Http\CookieOptions;

final class CartTokenService extends BaseService implements CartTokenServiceInterface
{
    public const string COOKIE_NAME = 'cart_token';
    private const int LIFETIME_SECONDS = 31536000; // 1 year

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
        // UUID v4 canonical form, case-insensitive
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $raw)) {
            return null;
        }
        return strtolower($raw);
    }

    public function issue(): string
    {
        $token = $this->generateUuid();
        $this->http->setCookie(self::COOKIE_NAME, $token, new CookieOptions(
            lifetimeSeconds: self::LIFETIME_SECONDS,
            httpOnly: true,
            sameSite: 'Lax',
        ));
        return $token;
    }

    /**
     * Cryptographically random UUID v4. PHP 8.4+ guarantees random_bytes().
     */
    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
