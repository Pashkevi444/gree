<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\BlogArticleCollection;
use Gree\Contract\Repository\BlogRepositoryInterface;
use Gree\Contract\Service\BlogServiceInterface;
use Gree\DTO\BlogArticleDto;
use Gree\Enum\BlogCategory;
use Gree\Logging\FileLogger;

final class BlogService extends BaseService implements BlogServiceInterface
{
    public function __construct(
        private readonly BlogRepositoryInterface $blogRepository,
    ) {}

    public function paginate(?BlogCategory $category, int $offset, int $limit): array
    {
        try {
            $items = $this->blogRepository->paginate($category, $offset, $limit);
            $total = $this->blogRepository->count($category);

            return [
                'items'   => $items,
                'total'   => $total,
                'hasMore' => ($offset + $items->count()) < $total,
            ];
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return ['items' => new BlogArticleCollection(), 'total' => 0, 'hasMore' => false];
        }
    }

    public function find(string $code): ?BlogArticleDto
    {
        try {
            return $this->blogRepository->findByCode($code);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return null;
        }
    }

    public function recent(BlogCategory $category, int $excludeId, int $limit): BlogArticleCollection
    {
        try {
            return $this->blogRepository->recent($category, $excludeId, $limit);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return new BlogArticleCollection();
        }
    }
}
