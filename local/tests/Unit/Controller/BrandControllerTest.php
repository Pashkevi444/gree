<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\BrandServiceInterface;
use Gree\Controller\BrandController;
use PHPUnit\Framework\TestCase;

final class BrandControllerTest extends TestCase
{
    public function testConstructorAcceptsBrandService(): void
    {
        $service    = $this->createMock(BrandServiceInterface::class);
        $controller = new BrandController($service);

        $this->assertInstanceOf(BrandController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new BrandController();
    }
}
