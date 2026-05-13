<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Controller\CatalogController;
use PHPUnit\Framework\TestCase;

final class CatalogControllerTest extends TestCase
{
    public function testConstructorAcceptsCatalogService(): void
    {
        $service    = $this->createMock(CatalogServiceInterface::class);
        $controller = new CatalogController($service);

        $this->assertInstanceOf(CatalogController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new CatalogController();
    }

    public function testHasFilterMethod(): void
    {
        $service    = $this->createMock(CatalogServiceInterface::class);
        $controller = new CatalogController($service);

        $this->assertTrue(method_exists($controller, 'filter'));
    }

    public function testHasIndexMethod(): void
    {
        $service    = $this->createMock(CatalogServiceInterface::class);
        $controller = new CatalogController($service);

        $this->assertTrue(method_exists($controller, 'index'));
    }
}
