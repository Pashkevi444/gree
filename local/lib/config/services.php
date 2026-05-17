<?php

declare(strict_types=1);

use Gree\Contract\DB\TransactionServiceInterface;
use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Contract\Repository\BlogRepositoryInterface;
use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\CatalogRepositoryInterface;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\Contract\Repository\MenuRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\OrderItemRepositoryInterface;
use Gree\Contract\Repository\OrderRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Repository\SeoRepositoryInterface;
use Gree\Contract\Service\BlogServiceInterface;
use Gree\Contract\Service\BrandServiceInterface;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CartServiceInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\MenuServiceInterface;
use Gree\Contract\Service\OrderServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\Contract\Service\TranslationLoaderInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CartController;
use Gree\Controller\CatalogController;
use Gree\Controller\HomeController;
use Gree\Controller\OrderController;
use Gree\Controller\LanguageController;
use Gree\Controller\ProductController;
use Gree\DB\TransactionService;
use Gree\Http\BitrixHttpContext;
use Gree\Repository\BlogRepository;
use Gree\Security\ApiGuard;
use Gree\Security\CsrfService;
use Gree\Repository\BrandRepository;
use Gree\Repository\CartItemRepository;
use Gree\Repository\CartRepository;
use Gree\Repository\CatalogRepository;
use Gree\Repository\HomeRepository;
use Gree\Repository\MenuRepository;
use Gree\Repository\OfferRepository;
use Gree\Repository\OrderItemRepository;
use Gree\Repository\OrderRepository;
use Gree\Repository\ProductRepository;
use Gree\Repository\SeoRepository;
use Gree\Repository\TranslationRepository;
use Gree\Service\BlogService;
use Gree\Service\BrandService;
use Gree\Service\BreadcrumbsService;
use Gree\Service\CartService;
use Gree\Service\CartTokenService;
use Gree\Service\CatalogService;
use Gree\Service\HomeService;
use Gree\Service\LanguageService;
use Gree\Service\MenuService;
use Gree\Service\OrderService;
use Gree\Service\SeoService;
use Gree\Service\TranslatorService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

// ─── i18n ─────────────────────────────────────────────────────────────────
$container->register(LanguageService::class)->setPublic(true);
$container->setAlias(LanguageServiceInterface::class, LanguageService::class)->setPublic(true);

// ─── Repositories ─────────────────────────────────────────────────────────
$container
    ->register(BrandRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(BrandRepositoryInterface::class, BrandRepository::class)->setPublic(true);

$container
    ->register(HomeRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(HomeRepositoryInterface::class, HomeRepository::class)->setPublic(true);

$container
    ->register(OfferRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(OfferRepositoryInterface::class, OfferRepository::class)->setPublic(true);

$container
    ->register(ProductRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->addArgument(new Reference(OfferRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(ProductRepositoryInterface::class, ProductRepository::class)->setPublic(true);

$container
    ->register(CatalogRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(CatalogRepositoryInterface::class, CatalogRepository::class)->setPublic(true);

$container
    ->register(MenuRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(MenuRepositoryInterface::class, MenuRepository::class)->setPublic(true);

$container
    ->register(BlogRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(BlogRepositoryInterface::class, BlogRepository::class)->setPublic(true);

$container->register(SeoRepository::class)->setPublic(true);
$container->setAlias(SeoRepositoryInterface::class, SeoRepository::class)->setPublic(true);

// ─── Translator stack ─────────────────────────────────────────────────────
$container->register(TranslationRepository::class)->setPublic(true);
$container->setAlias(TranslationLoaderInterface::class, TranslationRepository::class)->setPublic(true);

$container
    ->register(TranslatorService::class)
    ->addArgument(new Reference(TranslationLoaderInterface::class))
    ->setPublic(true);
$container->setAlias(TranslatorServiceInterface::class, TranslatorService::class)->setPublic(true);

// ─── Domain services ──────────────────────────────────────────────────────
$container
    ->register(HomeService::class)
    ->addArgument(new Reference(HomeRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(HomeServiceInterface::class, HomeService::class)->setPublic(true);

$container
    ->register(CatalogService::class)
    ->addArgument(new Reference(ProductRepositoryInterface::class))
    ->addArgument(new Reference(CatalogRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(CatalogServiceInterface::class, CatalogService::class)->setPublic(true);

$container
    ->register(BrandService::class)
    ->addArgument(new Reference(BrandRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(BrandServiceInterface::class, BrandService::class)->setPublic(true);

$container
    ->register(BreadcrumbsService::class)
    ->addArgument(new Reference(TranslatorServiceInterface::class))
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(BreadcrumbsServiceInterface::class, BreadcrumbsService::class)->setPublic(true);

$container
    ->register(MenuService::class)
    ->addArgument(new Reference(MenuRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(MenuServiceInterface::class, MenuService::class)->setPublic(true);

$container
    ->register(BlogService::class)
    ->addArgument(new Reference(BlogRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(BlogServiceInterface::class, BlogService::class)->setPublic(true);

$container
    ->register(SeoService::class)
    ->addArgument(new Reference(SeoRepositoryInterface::class))
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(SeoServiceInterface::class, SeoService::class)->setPublic(true);

// ─── DB ───────────────────────────────────────────────────────────────────
// Singleton — стейт savepoint'ов общий на запрос. Factory регистрирует
// геттер, чтобы контейнер выдавал один и тот же инстанс.
$container
    ->register(TransactionService::class)
    ->setFactory([TransactionService::class, 'getInstance'])
    ->setPublic(true);
$container->setAlias(TransactionServiceInterface::class, TransactionService::class)->setPublic(true);

// ─── HTTP context (cookies + headers + request meta) ──────────────────────
$container->register(BitrixHttpContext::class)->setPublic(true);
$container->setAlias(HttpContextInterface::class, BitrixHttpContext::class)->setPublic(true);

// ─── Security ─────────────────────────────────────────────────────────────
$container
    ->register(CsrfService::class)
    ->addArgument(new Reference(HttpContextInterface::class))
    ->setPublic(true);
$container->setAlias(CsrfServiceInterface::class, CsrfService::class)->setPublic(true);

// Список хостов, которым ApiGuard разрешает выступать Origin/Referer'ом.
// Источники (в порядке слияния):
//   1. ENV `GREE_ALLOWED_HOSTS` (через запятую) — основной способ задать прод,
//      особенно за reverse proxy/CDN, где `HTTP_HOST` приходит чужой
//      (внутренний/IP), а браузер шлёт `Origin: https://gree.all4it.org`.
//   2. `HTTP_HOST` из текущего запроса — авто-дефолт для локалки, где никакого
//      proxy нет и оба значения совпадают.
//   3. `X-Forwarded-Host` (если ставит proxy) — даём поддержку из коробки.
//   4. Дефолт `localhost` под CLI (PHPUnit, sprint.migration).
$allowedHosts = [];
$context = \Bitrix\Main\Application::getInstance()->getContext();
if ($context !== null && $context->getServer() !== null) {
    $server = $context->getServer();
    if (method_exists($server, 'getHttpHost') && $server->getHttpHost()) {
        $allowedHosts[] = $server->getHttpHost();
    }
    $forwarded = $server->get('HTTP_X_FORWARDED_HOST');
    if (is_string($forwarded) && $forwarded !== '') {
        // X-Forwarded-Host может содержать список через запятую — берём всё.
        foreach (explode(',', $forwarded) as $host) {
            $allowedHosts[] = trim($host);
        }
    }
}
$envHosts = getenv('GREE_ALLOWED_HOSTS') ?: '';
if ($envHosts !== '') {
    foreach (explode(',', $envHosts) as $host) {
        $allowedHosts[] = trim($host);
    }
}
$allowedHosts = array_values(array_unique(array_filter($allowedHosts)));
if (empty($allowedHosts)) {
    $allowedHosts = ['localhost'];
}

$container
    ->register(ApiGuard::class)
    ->addArgument(new Reference(CsrfServiceInterface::class))
    ->addArgument(new Reference(HttpContextInterface::class))
    ->addArgument($allowedHosts)
    ->setPublic(true);
$container->setAlias(ApiGuardInterface::class, ApiGuard::class)->setPublic(true);

// ─── Cart ─────────────────────────────────────────────────────────────────
$container->register(CartRepository::class)->setPublic(true);
$container->setAlias(CartRepositoryInterface::class, CartRepository::class)->setPublic(true);

$container->register(CartItemRepository::class)->setPublic(true);
$container->setAlias(CartItemRepositoryInterface::class, CartItemRepository::class)->setPublic(true);

$container
    ->register(CartTokenService::class)
    ->addArgument(new Reference(HttpContextInterface::class))
    ->setPublic(true);
$container->setAlias(CartTokenServiceInterface::class, CartTokenService::class)->setPublic(true);

$container
    ->register(CartService::class)
    ->addArgument(new Reference(CartRepositoryInterface::class))
    ->addArgument(new Reference(CartItemRepositoryInterface::class))
    ->addArgument(new Reference(OfferRepositoryInterface::class))
    ->addArgument(new Reference(ProductRepositoryInterface::class))
    ->addArgument(new Reference(CartTokenServiceInterface::class))
    ->setPublic(true);
$container->setAlias(CartServiceInterface::class, CartService::class)->setPublic(true);

// ─── Orders ───────────────────────────────────────────────────────────────
$container->register(OrderRepository::class)->setPublic(true);
$container->setAlias(OrderRepositoryInterface::class, OrderRepository::class)->setPublic(true);

$container->register(OrderItemRepository::class)->setPublic(true);
$container->setAlias(OrderItemRepositoryInterface::class, OrderItemRepository::class)->setPublic(true);

$container
    ->register(OrderService::class)
    ->addArgument(new Reference(OrderRepositoryInterface::class))
    ->addArgument(new Reference(OrderItemRepositoryInterface::class))
    ->addArgument(new Reference(CartTokenServiceInterface::class))
    ->addArgument(new Reference(CartRepositoryInterface::class))
    ->addArgument(new Reference(CartItemRepositoryInterface::class))
    ->addArgument(new Reference(OfferRepositoryInterface::class))
    ->addArgument(new Reference(ProductRepositoryInterface::class))
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->addArgument(new Reference(HttpContextInterface::class))
    ->addArgument(new Reference(TransactionServiceInterface::class))
    ->setPublic(true);
$container->setAlias(OrderServiceInterface::class, OrderService::class)->setPublic(true);

// ─── Controllers ──────────────────────────────────────────────────────────
$container
    ->register(HomeController::class)
    ->addArgument(new Reference(HomeServiceInterface::class))
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(BrandController::class)
    ->addArgument(new Reference(BrandServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(BlogController::class)
    ->addArgument(new Reference(BlogServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(CatalogController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(ProductController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(LanguageController::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);

$container
    ->register(CartController::class)
    ->addArgument(new Reference(CartServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->addArgument(new Reference(ApiGuardInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->setPublic(true);

$container
    ->register(OrderController::class)
    ->addArgument(new Reference(OrderServiceInterface::class))
    ->addArgument(new Reference(CartServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->addArgument(new Reference(SeoServiceInterface::class))
    ->addArgument(new Reference(ApiGuardInterface::class))
    ->setPublic(true);

$container->compile();

return $container;
