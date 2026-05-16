<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BlogArticleCollection;
use Gree\Collection\BreadcrumbCollection;
use Gree\DTO\BlogArticleDto;

final readonly class BlogArticleViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public BlogArticleDto $article,
        public string $categoryLabel,
        public BlogArticleCollection $related,
    ) {}
}
