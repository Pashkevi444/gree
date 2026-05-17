<?php

declare(strict_types=1);

namespace Gree\Http;

/**
 * Value object describing how an outgoing cookie should be persisted by the
 * browser. Mapped onto Bitrix\Main\Web\Cookie when flushed into a response.
 */
final readonly class CookieOptions
{
    public function __construct(
        public int $lifetimeSeconds = 0,    // 0 = session cookie
        public string $path = '/',
        public ?bool $secure = null,        // null = auto from current scheme
        public bool $httpOnly = true,
        public string $sameSite = 'Lax',
    ) {}
}
