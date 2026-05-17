<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\BrandServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\Controller\BrandController;
use PHPUnit\Framework\TestCase;

final class BrandControllerTest extends TestCase
{
    public function testConstructorAcceptsBrandService(): void
    {
        $service    = $this->createMock(BrandServiceInterface::class);
        $seo        = $this->createMock(SeoServiceInterface::class);
        $controller = new BrandController($service, $seo);

        $this->assertInstanceOf(BrandController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new BrandController();
    }
}
