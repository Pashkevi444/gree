<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\BreadcrumbCollection;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\DTO\BlogArticleDto;
use Gree\DTO\BreadcrumbDto;
use Gree\DTO\ProductDto;
use Gree\Enum\BlogCategory;
use Gree\Enum\ProductType;
use Gree\Logging\FileLogger;

final class BreadcrumbsService extends BaseService implements BreadcrumbsServiceInterface
{
    public function __construct(
        private readonly TranslatorServiceInterface $translator,
        private readonly LanguageServiceInterface $language,
    ) {}

    public function catalog(): BreadcrumbCollection
    {
        try {
            return new BreadcrumbCollection(
                $this->home(),
                new BreadcrumbDto(label: $this->t('breadcrumbs.catalog')),
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function catalogSection(ProductType $type): BreadcrumbCollection
    {
        try {
            return new BreadcrumbCollection(
                $this->home(),
                new BreadcrumbDto(label: $this->t('breadcrumbs.catalog'), url: '/catalog/'),
                new BreadcrumbDto(label: $this->t('product.types.' . $type->value)),
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'type' => $type->value,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function product(ProductDto $product): BreadcrumbCollection
    {
        try {
            return new BreadcrumbCollection(
                $this->home(),
                new BreadcrumbDto(label: $this->t('breadcrumbs.catalog'), url: '/catalog/'),
                new BreadcrumbDto(
                    label: $this->t('product.types.' . $product->type->value),
                    url: '/catalog/' . $product->type->slug() . '/',
                ),
                new BreadcrumbDto(label: $product->name),
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'product' => $product->code,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function blog(): BreadcrumbCollection
    {
        try {
            return new BreadcrumbCollection(
                $this->home(),
                new BreadcrumbDto(label: $this->t('blog.section')),
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function blogArticle(BlogArticleDto $article): BreadcrumbCollection
    {
        try {
            $categoryKey = $article->category === BlogCategory::News ? 'blog.news' : 'blog.title';

            return new BreadcrumbCollection(
                $this->home(),
                new BreadcrumbDto(label: $this->t('blog.section'), url: '/blog/'),
                new BreadcrumbDto(label: $this->t($categoryKey), url: '/blog/'),
                new BreadcrumbDto(label: $article->title),
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'article' => $article->code,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    private function home(): BreadcrumbDto
    {
        return new BreadcrumbDto(label: $this->t('breadcrumbs.home'), url: '/');
    }

    private function t(string $code): string
    {
        return $this->translator->translate($code, $this->language->get());
    }
}
