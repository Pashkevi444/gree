<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\BlogArticleCollection;
use Gree\DTO\BlogArticleDto;
use Gree\Enum\BlogCategory;

interface BlogServiceInterface
{
    /**
     * @return array{items: BlogArticleCollection, total: int, hasMore: bool}
     */
    public function paginate(?BlogCategory $category, int $offset, int $limit): array;

    public function find(string $code): ?BlogArticleDto;

    public function recent(BlogCategory $category, int $excludeId, int $limit): BlogArticleCollection;
}
