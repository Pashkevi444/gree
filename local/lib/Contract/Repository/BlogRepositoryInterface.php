<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\BlogArticleCollection;
use Gree\Enum\BlogCategory;

interface BlogRepositoryInterface
{
    public function paginate(?BlogCategory $category, int $offset, int $limit): BlogArticleCollection;

    public function count(?BlogCategory $category): int;

    public function findByCode(string $code): ?\Gree\DTO\BlogArticleDto;

    /**
     * Most recent articles in a category, optionally excluding one ID.
     */
    public function recent(BlogCategory $category, int $excludeId, int $limit): BlogArticleCollection;
}
