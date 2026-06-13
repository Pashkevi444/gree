<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\FilterDto;
use Gree\Enum\Color;
use Gree\Enum\ProductType;
use Gree\Enum\SortField;
use Gree\Tests\Stub\BitrixHttpRequest;
use PHPUnit\Framework\TestCase;

final class FilterDtoTest extends TestCase
{
    protected function setUp(): void
    {
        \Bitrix\Main\Application::resetInstance();
    }

    public function testDefaultValues(): void
    {
        $filter = new FilterDto();

        $this->assertSame([], $filter->types);
        $this->assertSame(0, $filter->priceMin);
        $this->assertSame(PHP_INT_MAX, $filter->priceMax);
        $this->assertSame([], $filter->areas);
        $this->assertSame([], $filter->colors);
        $this->assertNull($filter->bestseller);
        $this->assertNull($filter->inverterMotor);
        $this->assertSame(SortField::Popular, $filter->sortField);
    }

    public function testFromArrayWithTypes(): void
    {
        $filter = FilterDto::fromArray(['type' => ['wall', 'column']]);

        $this->assertCount(2, $filter->types);
        $this->assertContains(ProductType::Wall, $filter->types);
        $this->assertContains(ProductType::Column, $filter->types);
    }

    public function testFromArrayWithColors(): void
    {
        $filter = FilterDto::fromArray(['color' => ['white', 'silver']]);

        $this->assertCount(2, $filter->colors);
        $this->assertContains(Color::White, $filter->colors);
        $this->assertContains(Color::Silver, $filter->colors);
    }

    public function testFromArrayIgnoresInvalidColors(): void
    {
        $filter = FilterDto::fromArray(['color' => ['white', 'gray']]);

        $this->assertCount(1, $filter->colors);
        $this->assertContains(Color::White, $filter->colors);
    }

    public function testFromArrayWithPriceRange(): void
    {
        $filter = FilterDto::fromArray(['price' => ['1000', '9999000']]);

        $this->assertSame(1_000, $filter->priceMin);
        $this->assertSame(9_999_000, $filter->priceMax);
    }

    public function testFromArrayWithBestseller(): void
    {
        $yes = FilterDto::fromArray(['bestseller' => ['yes']]);
        $no = FilterDto::fromArray(['bestseller' => ['no']]);
        $def = FilterDto::fromArray([]);

        $this->assertTrue($yes->bestseller);
        $this->assertFalse($no->bestseller);
        $this->assertNull($def->bestseller);
    }

    public function testFromArrayWithSortField(): void
    {
        $filter = FilterDto::fromArray(['sort' => 'price_asc']);

        $this->assertSame(SortField::PriceAsc, $filter->sortField);
    }

    public function testFromArrayIgnoresInvalidTypes(): void
    {
        $filter = FilterDto::fromArray(['type' => ['wall', 'invalid_type']]);

        $this->assertCount(1, $filter->types);
        $this->assertContains(ProductType::Wall, $filter->types);
    }

    public function testFromRequestPostParsesAllFields(): void
    {
        $request = new BitrixHttpRequest(
            post: ['type' => ['wall'], 'sort' => 'price_desc', 'page' => '2', 'color' => ['black']],
            method: 'POST',
        );

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::PriceDesc, $filter->sortField);
        $this->assertSame(2, $filter->page);
        $this->assertContains(ProductType::Wall, $filter->types);
        $this->assertContains(Color::Black, $filter->colors);
    }

    public function testFromRequestDoesNotPersistToSession(): void
    {
        // Сессионного хранения фильтра нет сознательно: оно «перевозило» фильтр
        // между разделами каталога и ломало HTML-reset (см. fromRequest doc).
        $request = new BitrixHttpRequest(
            post: ['type' => ['wall'], 'sort' => 'price_desc'],
            method: 'POST',
        );

        FilterDto::fromRequest($request);

        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $this->assertFalse($session->has('catalog_filter'));
    }

    public function testFromRequestGetIgnoresSessionState(): void
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('catalog_filter', ['sort' => 'price_asc', 'type' => ['column'], 'page' => '3']);

        $request = new BitrixHttpRequest(method: 'GET');

        $filter = FilterDto::fromRequest($request);

        // Запрос без параметров = дефолтный фильтр, даже если в сессии что-то лежит.
        $this->assertSame(SortField::Popular, $filter->sortField);
        $this->assertSame(1, $filter->page);
        $this->assertSame([], $filter->types);
    }

    public function testFromRequestGetParsesQueryParams(): void
    {
        $request = new BitrixHttpRequest(
            query: ['sort' => 'price_desc', 'type' => ['wall'], 'page' => '2'],
            method: 'GET',
        );

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::PriceDesc, $filter->sortField);
        $this->assertSame(2, $filter->page);
        $this->assertContains(ProductType::Wall, $filter->types);
    }

    public function testFromRequestGetWithoutParamsUsesDefaults(): void
    {
        $request = new BitrixHttpRequest(method: 'GET');

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::Popular, $filter->sortField);
        $this->assertSame(1, $filter->page);
    }

    public function testDefaultPageAndPerPage(): void
    {
        $filter = new FilterDto();

        $this->assertSame(1, $filter->page);
        $this->assertSame(9, $filter->perPage);
    }

    public function testFromArrayWithPage(): void
    {
        $filter = FilterDto::fromArray(['page' => '3']);

        $this->assertSame(3, $filter->page);
    }

    public function testFromArrayWithPerPage(): void
    {
        $filter = FilterDto::fromArray(['per_page' => '24']);

        $this->assertSame(24, $filter->perPage);
    }

    public function testFromArrayPageDefaultsToOneForInvalidValue(): void
    {
        $filter = FilterDto::fromArray(['page' => '0']);

        $this->assertSame(1, $filter->page);
    }

    public function testIsImmutable(): void
    {
        $filter = new FilterDto();

        $this->expectException(\Error::class);
        $filter->priceMin = 1; // @phpstan-ignore-line
    }
}
