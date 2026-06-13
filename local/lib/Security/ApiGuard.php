<?php

declare(strict_types=1);

namespace Gree\Security;

use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Logging\FileLogger;
use Gree\Service\BaseService;

/** Pre-controller gate для state-changing API: same-origin + CSRF cookie==header. GET/HEAD/OPTIONS не гейтятся. */
final class ApiGuard extends BaseService implements ApiGuardInterface
{
    /** @var array<int, string> 'host' или 'host:port', нормализованные */
    private readonly array $allowedHosts;

    /** @param string|array<int, string> $allowedHosts */
    public function __construct(
        private readonly CsrfServiceInterface $csrf,
        private readonly HttpContextInterface $http,
        string|array $allowedHosts,
    ) {
        $list = is_string($allowedHosts) ? [$allowedHosts] : $allowedHosts;
        $this->allowedHosts = array_values(array_filter(
            array_map(static fn(string $h) => strtolower(trim($h)), $list),
        ));
    }

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

        $normalised = strtolower($sourceHost);
        foreach ($this->allowedHosts as $allowed) {
            if (hash_equals($allowed, $normalised)) {
                return;
            }
        }
        $this->deny('foreign origin: ' . $sourceHost);
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
