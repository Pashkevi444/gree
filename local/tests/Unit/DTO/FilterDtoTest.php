<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\DTO;

use Gree\DTO\FilterDto;
use Gree\Enum\ProductType;
use Gree\Enum\SortField;
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

    public function testFromRequestDelegatesToFromArray(): void
    {
        $dict = $this->createMock(\Bitrix\Main\Type\ParameterDictionary::class);
        $dict->method('toArray')->willReturn(['sort' => 'price_asc', 'page' => '2', 'per_page' => '24']);

        $request = $this->createMock(\Bitrix\Main\HttpRequest::class);
        $request->method('getQueryList')->willReturn($dict);

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::PriceAsc, $filter->sortField);
        $this->assertSame(2, $filter->page);
        $this->assertSame(24, $filter->perPage);
    }

    public function testFromRequestSavesFilterParamsToSession(): void
    {
        $dict = $this->createMock(\Bitrix\Main\Type\ParameterDictionary::class);
        $dict->method('toArray')->willReturn(['sort' => 'price_desc', 'type' => ['wall']]);

        $request = $this->createMock(\Bitrix\Main\HttpRequest::class);
        $request->method('getQueryList')->willReturn($dict);

        FilterDto::fromRequest($request);

        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $this->assertTrue($session->has('catalog_filter'));
        $saved = $session->get('catalog_filter');
        $this->assertSame('price_desc', $saved['sort']);
    }

    public function testFromRequestRestoresFilterFromSessionWhenNoFilterParams(): void
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('catalog_filter', ['sort' => 'price_asc', 'type' => ['column']]);

        $dict = $this->createMock(\Bitrix\Main\Type\ParameterDictionary::class);
        $dict->method('toArray')->willReturn(['page' => '2']);

        $request = $this->createMock(\Bitrix\Main\HttpRequest::class);
        $request->method('getQueryList')->willReturn($dict);

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::PriceAsc, $filter->sortField);
        $this->assertCount(1, $filter->types);
        $this->assertContains(ProductType::Column, $filter->types);
        $this->assertSame(2, $filter->page);
    }

    public function testFromRequestIgnoresSessionWhenFilterParamsPresent(): void
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('catalog_filter', ['sort' => 'price_asc']);

        $dict = $this->createMock(\Bitrix\Main\Type\ParameterDictionary::class);
        $dict->method('toArray')->willReturn(['sort' => 'price_desc']);

        $request = $this->createMock(\Bitrix\Main\HttpRequest::class);
        $request->method('getQueryList')->willReturn($dict);

        $filter = FilterDto::fromRequest($request);

        $this->assertSame(SortField::PriceDesc, $filter->sortField);
    }

    public function testDefaultPageAndPerPage(): void
    {
        $filter = new FilterDto();

        $this->assertSame(1, $filter->page);
        $this->assertSame(3, $filter->perPage);
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
