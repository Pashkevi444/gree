<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Controller\BaseController;
use PHPUnit\Framework\TestCase;

final class BaseControllerTest extends TestCase
{
    private BaseController $controller;
    private \ReflectionMethod $statusText;

    protected function setUp(): void
    {
        $this->controller = new class extends BaseController {};
        $this->statusText = new \ReflectionMethod(BaseController::class, 'statusText');
    }

    public function testStatusTextReturnsOkFor200(): void
    {
        $this->assertSame('OK', $this->statusText->invoke($this->controller, 200));
    }

    public function testStatusTextReturnsNotFoundFor404(): void
    {
        $this->assertSame('Not Found', $this->statusText->invoke($this->controller, 404));
    }

    public function testStatusTextReturnsEmptyStringForUnknownCode(): void
    {
        $this->assertSame('', $this->statusText->invoke($this->controller, 999));
    }
}
