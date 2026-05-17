<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CartController;
use Gree\Controller\CatalogController;
use Gree\Controller\HomeController;
use Gree\Controller\LanguageController;
use Gree\Controller\ProductController;
use Gree\Core\App;

/**
 * Web routes (HTML responses).
 *
 * Bitrix Routing concatenation rule: prefix args have no leading slash, and
 * URIs inside a group also have no leading slash — see api.php for details.
 * The router puts a single `/` between segments and prepends the leading `/`
 * to the prefix itself.
 *
 *   prefix('catalog') + get('{section}/') → /catalog/{section}/
 *   prefix('catalog') + get('')           → /catalog/
 */
return static function (RoutingConfigurator $routes): void {

    // ─── Home ────────────────────────────────────────────────────────────────
    $routes->get('/', static fn() => App::get(HomeController::class)->index())->name('home');

    // ─── Catalog (hub + section list + product detail) ───────────────────────
    $routes->prefix('catalog')->name('catalog.')->group(static function (RoutingConfigurator $routes): void {
        $routes
            ->get('', static fn() => App::get(CatalogController::class)->index())
            ->name('index');

        $routes
            ->get('{section}/', static fn(string $section) => App::get(CatalogController::class)->section($section))
            ->where('section', 'nastennie|kolonnye|promyshlennye')
            ->name('section');

        $routes
            ->get('{section}/{code}/', static fn(string $section, string $code) => App::get(ProductController::class)->show($code, $section))
            ->where('section', 'nastennie|kolonnye|promyshlennye')
            ->where('code', '[\w\d\-]+')
            ->name('product');
    });

    // ─── Brand ───────────────────────────────────────────────────────────────
    $routes->prefix('brand')->name('brand.')->group(static function (RoutingConfigurator $routes): void {
        $routes
            ->get('{code}/', static fn(string $code) => App::get(BrandController::class)->show($code))
            ->where('code', '[\w\d\-]+')
            ->name('show');
    });

    // ─── Blog (listing + article) ────────────────────────────────────────────
    $routes->prefix('blog')->name('blog.')->group(static function (RoutingConfigurator $routes): void {
        $routes
            ->get('', static fn() => App::get(BlogController::class)->index())
            ->name('index');
        $routes
            ->get('{code}/', static fn(string $code) => App::get(BlogController::class)->show($code))
            ->where('code', '[\w\d\-]+')
            ->name('show');
    });

    // ─── Cart ────────────────────────────────────────────────────────────────
    $routes
        ->get('/cart/', static fn() => App::get(CartController::class)->index())
        ->name('cart.index');

    // ─── Language switch ─────────────────────────────────────────────────────
    $routes
        ->get('/lang/{locale}/', static fn(string $locale) => App::get(LanguageController::class)->switch($locale))
        ->where('locale', 'ru|en')
        ->name('lang.switch');
};
