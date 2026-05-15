<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Controller\ProductController;
use PHPUnit\Framework\TestCase;

final class ProductControllerTest extends TestCase
{
    public function testConstructorAcceptsServices(): void
    {
        $service    = $this->createMock(CatalogServiceInterface::class);
        $crumbs     = $this->createMock(BreadcrumbsServiceInterface::class);
        $controller = new ProductController($service, $crumbs);

        $this->assertInstanceOf(ProductController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new ProductController();
    }
}
