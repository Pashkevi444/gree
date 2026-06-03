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
use Gree\View\BlogCategoryViewData;
use Gree\View\BlogViewData;

final class BlogController extends BaseController
{
    /** Сколько карточек в каждой секции на главной /blog/ (до кнопки «Показать ещё»). */
    private const int INDEX_PREVIEW_SIZE = 3;

    /** Жёсткий cap на /blog/{advice|news}/ — статей у нас немного, прокрутка прокрутится. */
    private const int CATEGORY_PAGE_SIZE = 100;

    public function __construct(
        private readonly BlogServiceInterface $blogService,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('blog'));
        $this->addPageAssets('blog');

        $tips = $this->blogService->paginate(BlogCategory::Tips, 0, self::INDEX_PREVIEW_SIZE);
        $news = $this->blogService->paginate(BlogCategory::News, 0, self::INDEX_PREVIEW_SIZE);

        return $this->view('blog/index', new BlogViewData(
            breadcrumbs: $this->breadcrumbs->blog(),
            tips:        $tips['items'],
            news:        $news['items'],
        ));
    }

    /**
     * GET /blog/{slug}/ где slug = advice|news. Полный список статей категории.
     */
    public function category(string $slug): HttpResponse
    {
        $category = BlogCategory::fromUrlSlug($slug);
        if ($category === null) {
            return $this->view('errors/404')->setStatus('404 Not Found');
        }

        $pageCode = 'blog-' . $slug;  // blog-advice / blog-news
        $this->applySeo($this->seo->forPage($pageCode));
        $this->addPageAssets('blog');

        $page = $this->blogService->paginate($category, 0, self::CATEGORY_PAGE_SIZE);

        return $this->view('blog/category', new BlogCategoryViewData(
            breadcrumbs: $this->breadcrumbs->blogCategory($category),
            category:    $category,
            items:       $page['items'],
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
}
