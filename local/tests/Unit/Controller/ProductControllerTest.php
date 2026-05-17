<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\Controller\ProductController;
use PHPUnit\Framework\TestCase;

final class ProductControllerTest extends TestCase
{
    public function testConstructorAcceptsServices(): void
    {
        $service    = $this->createMock(CatalogServiceInterface::class);
        $crumbs     = $this->createMock(BreadcrumbsServiceInterface::class);
        $seo        = $this->createMock(SeoServiceInterface::class);
        $controller = new ProductController($service, $crumbs, $seo);

        $this->assertInstanceOf(ProductController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new ProductController();
    }
}
