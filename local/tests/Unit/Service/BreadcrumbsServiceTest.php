<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\DTO\ProductDto;
use Gree\Enum\Locale;
use Gree\Enum\ProductType;
use Gree\Service\BreadcrumbsService;
use PHPUnit\Framework\TestCase;

final class BreadcrumbsServiceTest extends TestCase
{
    public function testForCatalogReturnsTwoCrumbs(): void
    {
        $service = $this->makeService([
            'breadcrumbs.home' => 'Home',
            'breadcrumbs.catalog' => 'Catalog',
        ]);

        $crumbs = $service->catalog()->toArray();

        $this->assertCount(2, $crumbs);
        $this->assertSame('Home', $crumbs[0]->label);
        $this->assertSame('/', $crumbs[0]->url);
        $this->assertSame('Catalog', $crumbs[1]->label);
        $this->assertSame('', $crumbs[1]->url);
        $this->assertTrue($crumbs[1]->isCurrent());
    }

    public function testForProductReturnsFourCrumbsLastIsProductName(): void
    {
        $service = $this->makeService([
            'breadcrumbs.home' => 'Home',
            'breadcrumbs.catalog' => 'Catalog',
            'product.types.wall' => 'Wall-mounted',
        ]);

        $product = new ProductDto(
            id: 7,
            name: 'Gree BORA X 07',
            code: 'gree-bora-x-07',
            type: ProductType::Wall,
            price: 3_490_000,
            area: 20,
        );

        $crumbs = $service->product($product)->toArray();

        $this->assertCount(4, $crumbs);
        $this->assertSame('Home', $crumbs[0]->label);
        $this->assertSame('/', $crumbs[0]->url);
        $this->assertSame('Catalog', $crumbs[1]->label);
        // URL is what Route::to() produces — stub returns "/<route-name>/?..."
        // so we just check the route name is present.
        $this->assertStringContainsString('catalog.index', $crumbs[1]->url);
        $this->assertSame('Wall-mounted', $crumbs[2]->label);
        $this->assertStringContainsString('catalog.section', $crumbs[2]->url);
        $this->assertStringContainsString('section=nastennie', $crumbs[2]->url);
        $this->assertSame('Gree BORA X 07', $crumbs[3]->label);
        $this->assertTrue($crumbs[3]->isCurrent());
    }

    public function testBlogCategoryReturnsThreeCrumbsLastIsCategoryName(): void
    {
        $crumbs = $this->makeService([
            'breadcrumbs.home' => 'Главная',
            'blog.section'     => 'Блог',
            'blog.news'        => 'Новости',
        ])->blogCategory(\Gree\Enum\BlogCategory::News)->toArray();

        $this->assertCount(3, $crumbs);
        $this->assertSame('Главная', $crumbs[0]->label);
        $this->assertSame('Блог',    $crumbs[1]->label);
        $this->assertNotSame('', $crumbs[1]->url, 'middle crumb должен иметь URL на /blog/');
        $this->assertSame('Новости', $crumbs[2]->label);
        $this->assertTrue($crumbs[2]->isCurrent());
    }

    public function testHelpReturnsTwoCrumbs(): void
    {
        $crumbs = $this->makeService([
            'breadcrumbs.home' => 'Главная',
            'breadcrumbs.help' => 'Помощь',
        ])->help()->toArray();

        $this->assertCount(2, $crumbs);
        $this->assertSame('Главная', $crumbs[0]->label);
        $this->assertSame('Помощь', $crumbs[1]->label);
        $this->assertTrue($crumbs[1]->isCurrent());
    }

    public function testContactsReturnsTwoCrumbs(): void
    {
        $crumbs = $this->makeService([
            'breadcrumbs.home'     => 'Главная',
            'breadcrumbs.contacts' => 'Контакты',
        ])->contacts()->toArray();

        $this->assertCount(2, $crumbs);
        $this->assertSame('Контакты', $crumbs[1]->label);
        $this->assertTrue($crumbs[1]->isCurrent());
    }

    public function testWhereToBuyReturnsTwoCrumbs(): void
    {
        $crumbs = $this->makeService([
            'breadcrumbs.home'         => 'Главная',
            'breadcrumbs.where_to_buy' => 'Где купить',
        ])->whereToBuy()->toArray();

        $this->assertCount(2, $crumbs);
        $this->assertSame('Где купить', $crumbs[1]->label);
        $this->assertTrue($crumbs[1]->isCurrent());
    }

    /** @param array<string, string> $translations code → label */
    private function makeService(array $translations): BreadcrumbsService
    {
        $translator = $this->createMock(TranslatorServiceInterface::class);
        $translator->method('translate')->willReturnCallback(
            fn(string $code, Locale $locale) => $translations[$code] ?? $code
        );

        $language = $this->createMock(LanguageServiceInterface::class);
        $language->method('get')->willReturn(Locale::Uz);

        return new BreadcrumbsService($translator, $language);
    }
}
