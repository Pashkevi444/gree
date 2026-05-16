<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\BlogArticleDto;

/** @extends BaseCollection<BlogArticleDto> */
final class BlogArticleCollection extends BaseCollection
{
    public function __construct(BlogArticleDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return BlogArticleDto::class;
    }
}
