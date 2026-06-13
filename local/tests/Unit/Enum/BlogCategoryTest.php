<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Enum;

use Gree\Enum\BlogCategory;
use PHPUnit\Framework\TestCase;

final class BlogCategoryTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame('tips', BlogCategory::Tips->value);
        $this->assertSame('news', BlogCategory::News->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('Советы',   BlogCategory::Tips->label());
        $this->assertSame('Новости',  BlogCategory::News->label());
    }

    public function testTryFromOrNull(): void
    {
        $this->assertSame(BlogCategory::Tips, BlogCategory::tryFromOrNull('tips'));
        $this->assertNull(BlogCategory::tryFromOrNull(null));
        $this->assertNull(BlogCategory::tryFromOrNull(''));
        $this->assertNull(BlogCategory::tryFromOrNull('unknown'));
    }

    public function testUrlSlugDiffersFromValueForTips(): void
    {
        $this->assertSame('advice', BlogCategory::Tips->urlSlug());
        $this->assertSame('news',   BlogCategory::News->urlSlug());
    }

    public function testFromUrlSlug(): void
    {
        $this->assertSame(BlogCategory::Tips, BlogCategory::fromUrlSlug('advice'));
        $this->assertSame(BlogCategory::News, BlogCategory::fromUrlSlug('news'));
        $this->assertNull(BlogCategory::fromUrlSlug('tips'));   // value, не slug
        $this->assertNull(BlogCategory::fromUrlSlug('unknown'));
    }
}
