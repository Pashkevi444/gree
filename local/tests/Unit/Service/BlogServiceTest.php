<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\BlogArticleCollection;
use Gree\Contract\Repository\BlogRepositoryInterface;
use Gree\DTO\BlogArticleDto;
use Gree\Enum\BlogCategory;
use Gree\Service\BlogService;
use PHPUnit\Framework\TestCase;

final class BlogServiceTest extends TestCase
{
    private BlogRepositoryInterface $repo;
    private BlogService             $service;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(BlogRepositoryInterface::class);
        $this->service = new BlogService($this->repo);
    }

    public function testPaginateAggregatesItemsTotalHasMore(): void
    {
        $items = new BlogArticleCollection(
            new BlogArticleDto(id: 1, code: 'a', title: 'A', description: '', date: '', image: '', url: '/blog/a/', category: BlogCategory::Tips, readingTime: 0),
            new BlogArticleDto(id: 2, code: 'b', title: 'B', description: '', date: '', image: '', url: '/blog/b/', category: BlogCategory::Tips, readingTime: 0),
        );
        $this->repo->method('paginate')->willReturn($items);
        $this->repo->method('count')->willReturn(5);

        $result = $this->service->paginate(BlogCategory::Tips, 0, 2);

        $this->assertSame($items, $result['items']);
        $this->assertSame(5, $result['total']);
        $this->assertTrue($result['hasMore']);
    }

    public function testPaginateHasMoreFalseWhenAllReturned(): void
    {
        $items = new BlogArticleCollection(
            new BlogArticleDto(id: 1, code: 'a', title: 'A', description: '', date: '', image: '', url: '/blog/a/', category: BlogCategory::News, readingTime: 0),
        );
        $this->repo->method('paginate')->willReturn($items);
        $this->repo->method('count')->willReturn(1);

        $this->assertFalse($this->service->paginate(BlogCategory::News, 0, 5)['hasMore']);
    }

    public function testPaginateReturnsSafeFallbackOnException(): void
    {
        $this->repo->method('paginate')->willThrowException(new \RuntimeException('boom'));

        $result = $this->service->paginate(null, 0, 3);

        $this->assertCount(0, $result['items']);
        $this->assertSame(0, $result['total']);
        $this->assertFalse($result['hasMore']);
    }

    public function testFindDelegatesAndReturnsDto(): void
    {
        $dto = new BlogArticleDto(id: 7, code: 'x', title: 'X', description: '', date: '', image: '', url: '/blog/x/', category: BlogCategory::Tips, readingTime: 0);
        $this->repo->expects($this->once())->method('findByCode')->with('x')->willReturn($dto);
        $this->assertSame($dto, $this->service->find('x'));
    }

    public function testFindReturnsNullOnException(): void
    {
        $this->repo->method('findByCode')->willThrowException(new \RuntimeException('boom'));
        $this->assertNull($this->service->find('x'));
    }

    public function testRecentDelegates(): void
    {
        $c = new BlogArticleCollection();
        $this->repo->expects($this->once())->method('recent')->with(BlogCategory::Tips, 9, 3)->willReturn($c);
        $this->assertSame($c, $this->service->recent(BlogCategory::Tips, 9, 3));
    }

    public function testRecentReturnsEmptyOnException(): void
    {
        $this->repo->method('recent')->willThrowException(new \RuntimeException('boom'));
        $this->assertCount(0, $this->service->recent(BlogCategory::News, 1, 3));
    }
}
