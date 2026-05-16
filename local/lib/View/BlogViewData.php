<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BlogArticleCollection;
use Gree\Collection\BreadcrumbCollection;

final readonly class BlogViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public BlogArticleCollection $tips,
        public bool $hasMoreTips,
        public BlogArticleCollection $news,
        public bool $hasMoreNews,
        public int $pageSize,
    ) {}
}
