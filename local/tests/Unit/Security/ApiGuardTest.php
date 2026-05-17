<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Security;

use Gree\Http\InMemoryHttpContext;
use Gree\Security\AccessDeniedException;
use Gree\Security\ApiGuard;
use Gree\Security\CsrfService;
use PHPUnit\Framework\TestCase;

final class ApiGuardTest extends TestCase
{
    private InMemoryHttpContext $http;
    private CsrfService $csrf;
    private ApiGuard $guard;

    protected function setUp(): void
    {
        $this->http = new InMemoryHttpContext(requestMethod: 'POST');
        $this->csrf = new CsrfService($this->http);
        $this->guard = new ApiGuard($this->csrf, $this->http, allowedHosts: 'gree:8890');
    }

    public function testStateChangingRequestRejectsForeignOrigin(): void
    {
        $token = $this->csrf->issue();
        $this->http->setHeader('Origin', 'https://evil.com');
        $this->http->setHeader('X-CSRF-Token', $token);

        $this->expectException(AccessDeniedException::class);
        $this->guard->guardStateChanging((object) []);
    }

    public function testStateChangingRequestRejectsMissingCsrf(): void
    {
        $this->csrf->issue();
        $this->http->setHeader('Origin', 'https://gree:8890');

        $this->expectException(AccessDeniedException::class);
        $this->guard->guardStateChanging((object) []);
    }

    public function testStateChangingRequestRejectsWrongCsrf(): void
    {
        $this->csrf->issue();
        $this->http->setHeader('Origin', 'https://gree:8890');
        $this->http->setHeader('X-CSRF-Token', 'forged');

        $this->expectException(AccessDeniedException::class);
        $this->guard->guardStateChanging((object) []);
    }

    public function testStateChangingRequestPassesWithMatchingTokenAndSameOrigin(): void
    {
        $token = $this->csrf->issue();
        $this->http->setHeader('Origin', 'https://gree:8890');
        $this->http->setHeader('X-CSRF-Token', $token);

        $this->guard->guardStateChanging((object) []);
        $this->addToAssertionCount(1);
    }

    public function testStateChangingRequestFallsBackToRefererWhenOriginMissing(): void
    {
        $token = $this->csrf->issue();
        $this->http->setHeader('Referer', 'https://gree:8890/catalog/nastennie/gree-bora-x-07/');
        $this->http->setHeader('X-CSRF-Token', $token);

        $this->guard->guardStateChanging((object) []);
        $this->addToAssertionCount(1);
    }

    public function testStateChangingRequestRejectsBlankOriginAndReferer(): void
    {
        $this->csrf->issue();
        $this->http->setHeader('X-CSRF-Token', 'whatever');

        $this->expectException(AccessDeniedException::class);
        $this->guard->guardStateChanging((object) []);
    }

    public function testAcceptsAnyHostFromMultiHostAllowList(): void
    {
        $http = new InMemoryHttpContext(requestMethod: 'POST');
        $csrf = new CsrfService($http);
        $guard = new ApiGuard($csrf, $http, allowedHosts: ['gree:8890', 'gree.all4it.org']);

        $token = $csrf->issue();
        $http->setHeader('Origin', 'https://gree.all4it.org');
        $http->setHeader('X-CSRF-Token', $token);

        $guard->guardStateChanging((object) []);
        $this->addToAssertionCount(1);
    }

    public function testNormalisesHostCaseBeforeComparison(): void
    {
        // ENV/конфиг могут хранить хост в любом регистре. Origin браузер тоже
        // присылает «как есть». Сравнение должно быть case-insensitive.
        $http = new InMemoryHttpContext(requestMethod: 'POST');
        $csrf = new CsrfService($http);
        $guard = new ApiGuard($csrf, $http, allowedHosts: 'GREE.all4it.ORG');

        $token = $csrf->issue();
        $http->setHeader('Origin', 'https://gree.all4it.org');
        $http->setHeader('X-CSRF-Token', $token);

        $guard->guardStateChanging((object) []);
        $this->addToAssertionCount(1);
    }

    public function testSafeMethodIsNeverGated(): void
    {
        $this->http->setRequestMethod('GET');
        // no token, no origin, no nothing — must not throw
        $this->guard->guardStateChanging((object) []);
        $this->addToAssertionCount(1);
    }
}
