<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Security;

use Gree\Http\InMemoryHttpContext;
use Gree\Security\CsrfService;
use PHPUnit\Framework\TestCase;

final class CsrfServiceTest extends TestCase
{
    private InMemoryHttpContext $http;
    private CsrfService $svc;

    protected function setUp(): void
    {
        $this->http = new InMemoryHttpContext();
        $this->svc = new CsrfService($this->http);
    }

    public function testReadReturnsNullWhenCookieMissing(): void
    {
        $this->assertNull($this->svc->read());
    }

    public function testReadReturnsNullWhenCookieMalformed(): void
    {
        $this->http->setCookie(CsrfService::COOKIE_NAME, 'too short');
        $this->assertNull($this->svc->read());
    }

    public function testIssueGeneratesAndStoresToken(): void
    {
        $token = $this->svc->issue();

        $this->assertSame(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
        $this->assertSame($token, $this->http->getCookie(CsrfService::COOKIE_NAME));
    }

    public function testReadReturnsIssuedToken(): void
    {
        $token = $this->svc->issue();
        $this->assertSame($token, $this->svc->read());
    }

    public function testMatchesIsConstantTimeAndCorrect(): void
    {
        $token = $this->svc->issue();

        $this->assertTrue($this->svc->matches($token));
        $this->assertFalse($this->svc->matches('wrong'));
        $this->assertFalse($this->svc->matches(''));
    }

    public function testMatchesFailsWithoutCookie(): void
    {
        $this->assertFalse($this->svc->matches('anything'));
    }
}
