<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Controller;

use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Controller\CatalogController;
use Gree\DTO\ProductDto;
use Gree\Enum\Color;
use Gree\Enum\ProductType;
use PHPUnit\Framework\TestCase;

final class CatalogControllerTest extends TestCase
{
    public function testConstructorAcceptsCatalogService(): void
    {
        $service = $this->createMock(CatalogServiceInterface::class);
        $crumbs = $this->createMock(\Gree\Contract\Service\BreadcrumbsServiceInterface::class);
        $controller = new CatalogController($service, $crumbs);

        $this->assertInstanceOf(CatalogController::class, $controller);
    }

    public function testConstructorIsRequired(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new CatalogController();
    }

    public function testHasFilterMethod(): void
    {
        $service = $this->createMock(CatalogServiceInterface::class);
        $crumbs = $this->createMock(\Gree\Contract\Service\BreadcrumbsServiceInterface::class);
        $controller = new CatalogController($service, $crumbs);

        $this->assertTrue(method_exists($controller, 'filter'));
    }

    public function testHasIndexMethod(): void
    {
        $service = $this->createMock(CatalogServiceInterface::class);
        $crumbs = $this->createMock(\Gree\Contract\Service\BreadcrumbsServiceInterface::class);
        $controller = new CatalogController($service, $crumbs);

        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function testBuildItemPayloadBestseller(): void
    {
        $product = new ProductDto(
            id: 1,
            name: 'Gree BORA X',
            code: 'gree-bora-x',
            type: ProductType::Wall,
            price: 3_490_000,
            area: 30,
            isBestseller: true,
            image: '/upload/p.png',
            colors: [Color::White, Color::Silver],
        );

        $payload = CatalogController::buildItemPayload($product);

        $this->assertSame(['type' => 'bestseller', 'text' => 'Хит продаж'], $payload['badge']);
        $this->assertSame('/upload/p.png', $payload['image']);
        $this->assertSame('Gree BORA X', $payload['name']);
        $this->assertSame('Площадь — 30 м²', $payload['meta']['text']);
        $this->assertSame(['#ffffff', '#8c8c8c'], $payload['meta']['colors']); // serialised as hex strings
        $this->assertSame(3_490_000, $payload['price']);
        $this->assertSame('/catalog/nastennie/gree-bora-x/', $payload['href']);
    }

    public function testBuildItemPayloadOmitsBadgeWhenNotBestseller(): void
    {
        $product = new ProductDto(
            id: 1,
            name: 'Gree',
            code: 'gree',
            type: ProductType::Wall,
            price: 1000,
            area: 20,
        );

        $payload = CatalogController::buildItemPayload($product);

        $this->assertArrayNotHasKey('badge', $payload);
    }

    public function testBuildItemPayloadEmptyAreaText(): void
    {
        $product = new ProductDto(
            id: 1,
            name: 'Gree',
            code: 'gree',
            type: ProductType::Wall,
            price: 1000,
            area: 0,
        );

        $payload = CatalogController::buildItemPayload($product);

        $this->assertSame('', $payload['meta']['text']);
    }

    public function testBuildPaginationMinimumOne(): void
    {
        $pag = CatalogController::buildPagination(0, 3, 1);

        $this->assertSame(1, $pag['totalPages']);
        $this->assertSame(1, $pag['currentPage']);
    }

    public function testBuildPaginationCalculatesTotalPages(): void
    {
        $pag = CatalogController::buildPagination(10, 3, 2);

        $this->assertSame(4, $pag['totalPages']);
        $this->assertSame(2, $pag['currentPage']);
    }

    public function testBuildPaginationClampsCurrentPageToOne(): void
    {
        $pag = CatalogController::buildPagination(10, 3, 0);

        $this->assertSame(1, $pag['currentPage']);
    }
}
