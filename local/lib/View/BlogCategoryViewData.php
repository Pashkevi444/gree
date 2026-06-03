<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BlogArticleCollection;
use Gree\Collection\BreadcrumbCollection;
use Gree\Enum\BlogCategory;

final readonly class BlogCategoryViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public BlogCategory $category,
        public BlogArticleCollection $items,
    ) {}
}
