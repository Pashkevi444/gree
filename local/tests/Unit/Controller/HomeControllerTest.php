<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\HomeServiceInterface;
use Gree\Controller\HomeController;
use PHPUnit\Framework\TestCase;

final class HomeControllerTest extends TestCase
{
    public function testConstructorAcceptsHomeService(): void
    {
        $service    = $this->createMock(HomeServiceInterface::class);
        $controller = new HomeController($service);

        $this->assertInstanceOf(HomeController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new HomeController();
    }
}
