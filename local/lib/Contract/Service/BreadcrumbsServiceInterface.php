<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\BreadcrumbCollection;
use Gree\DTO\BlogArticleDto;
use Gree\DTO\ProductDto;
use Gree\Enum\ProductType;

interface BreadcrumbsServiceInterface
{
    public function catalog(): BreadcrumbCollection;
    public function catalogSection(ProductType $type): BreadcrumbCollection;
    public function product(ProductDto $product): BreadcrumbCollection;
    public function blog(): BreadcrumbCollection;
    public function blogCategory(\Gree\Enum\BlogCategory $category): BreadcrumbCollection;
    public function blogArticle(BlogArticleDto $article): BreadcrumbCollection;
    public function cart(): BreadcrumbCollection;
    public function checkout(): BreadcrumbCollection;
    public function help(): BreadcrumbCollection;
    public function contacts(): BreadcrumbCollection;
    public function whereToBuy(): BreadcrumbCollection;
    public function partners(): BreadcrumbCollection;
}
