<?php

declare(strict_types=1);

namespace Gree\Security;

use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Logging\FileLogger;
use Gree\Service\BaseService;

/**
 * Pre-controller gate for state-changing API requests.
 *
 * Two independent checks, both must pass:
 *
 *   1. Same-origin: Origin (or Referer when Origin is absent — Safari + cross-
 *      origin redirects sometimes drop it) must match the configured host.
 *      Defense-in-depth — SameSite=Strict cookies already block most cross-
 *      site abuse, this catches the rest.
 *
 *   2. CSRF: the `csrf_token` cookie value must equal the `X-CSRF-Token`
 *      header. See CsrfService for the rationale.
 *
 * GET / HEAD / OPTIONS are never gated — they must be safe.
 */
final class ApiGuard extends BaseService implements ApiGuardInterface
{
    public function __construct(
        private readonly CsrfServiceInterface $csrf,
        private readonly HttpContextInterface $http,
        /** Hostname this site is served as (e.g. "gree:8890"). Compared against Origin/Referer. */
        private readonly string $allowedHost,
    ) {}

    public function guardStateChanging(object $request): void
    {
        $method = strtoupper($this->http->getRequestMethod());
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        $this->assertSameOrigin();
        $this->assertCsrf();
    }

    private function assertSameOrigin(): void
    {
        $origin = $this->http->getHeader('Origin');
        $referer = $this->http->getHeader('Referer');

        $sourceHost = $this->hostOf($origin) ?? $this->hostOf($referer);
        if ($sourceHost === null) {
            $this->deny('missing Origin/Referer on state-changing request');
        }
        if (!hash_equals($this->allowedHost, $sourceHost)) {
            $this->deny('foreign origin: ' . $sourceHost);
        }
    }

    private function assertCsrf(): void
    {
        $sent = $this->http->getHeader('X-CSRF-Token') ?? '';
        if (!$this->csrf->matches($sent)) {
            $this->deny('CSRF token mismatch');
        }
    }

    private function hostOf(?string $url): ?string
    {
        if ($url === null || $url === '' || $url === 'null') {
            return null;
        }
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            return null;
        }
        $host = $parts['host'];
        if (isset($parts['port'])) {
            $host .= ':' . $parts['port'];
        }
        return $host;
    }

    private function deny(string $reason): never
    {
        FileLogger::getInstance()->critical('ApiGuard denied: ' . $reason, [
            'ip'  => $this->http->getRemoteAddress(),
            'uri' => $this->http->getRequestUri(),
        ]);
        throw new AccessDeniedException($reason);
    }
}
