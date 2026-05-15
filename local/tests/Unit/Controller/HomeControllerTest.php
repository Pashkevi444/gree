<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\Controller\HomeController;
use PHPUnit\Framework\TestCase;

final class HomeControllerTest extends TestCase
{
    public function testConstructorAcceptsHomeAndCatalogServices(): void
    {
        $home = $this->createMock(HomeServiceInterface::class);
        $catalog = $this->createMock(CatalogServiceInterface::class);
        $controller = new HomeController($home, $catalog);

        $this->assertInstanceOf(HomeController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new HomeController();
    }
}
