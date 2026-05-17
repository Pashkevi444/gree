<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BlogServiceInterface;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\SeoDto;
use Gree\Enum\BlogCategory;
use Gree\Enum\IblockCode;
use Gree\Helpers\Language;
use Gree\View\BlogArticleViewData;
use Gree\View\BlogViewData;

final class BlogController extends BaseController
{
    private const int PAGE_SIZE = 3;

    public function __construct(
        private readonly BlogServiceInterface $blogService,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('blog'));
        $this->addPageAssets('blog');

        $tips = $this->blogService->paginate(BlogCategory::Tips, 0, self::PAGE_SIZE);
        $news = $this->blogService->paginate(BlogCategory::News, 0, self::PAGE_SIZE);

        return $this->view('blog/index', new BlogViewData(
            breadcrumbs: $this->breadcrumbs->blog(),
            tips:        $tips['items'],
            hasMoreTips: $tips['hasMore'],
            news:        $news['items'],
            hasMoreNews: $news['hasMore'],
            pageSize:    self::PAGE_SIZE,
        ));
    }

    public function show(string $code): HttpResponse
    {
        $article = $this->blogService->find($code);
        if ($article === null) {
            return $this->view('errors/404')->setStatus('404 Not Found');
        }

        $seo = $this->seo->forElement(IblockCode::Blog, $article->id)
            ?? new SeoDto(title: $article->title, description: $article->description);
        $this->applySeo($seo);
        $this->addPageAssets('blog-item');

        $categoryKey = $article->category === BlogCategory::News ? 'blog.news' : 'blog.title';

        return $this->view('blog/show', new BlogArticleViewData(
            breadcrumbs:   $this->breadcrumbs->blogArticle($article),
            article:       $article,
            categoryLabel: Language::t($categoryKey),
            related:       $this->blogService->recent($article->category, $article->id, 3),
        ));
    }

    /**
     * GET /api/v1/blog?category=tips&offset=3&limit=3
     * → { items: [...], total: N, hasMore: bool }
     */
    public function paginate(): HttpResponse
    {
        $request = $this->getRequest();
        $category = BlogCategory::tryFromOrNull($request->get('category'));

        $offset = max(0, (int) $request->get('offset'));
        $limit = (int) ($request->get('limit') ?? self::PAGE_SIZE);
        $limit = $limit > 0 && $limit <= 50 ? $limit : self::PAGE_SIZE;

        $page = $this->blogService->paginate($category, $offset, $limit);

        $items = [];
        foreach ($page['items'] as $article) {
            $items[] = $article->toJson();
        }

        return $this->json([
            'items'   => $items,
            'total'   => $page['total'],
            'hasMore' => $page['hasMore'],
            'offset'  => $offset,
            'limit'   => $limit,
        ]);
    }
}
