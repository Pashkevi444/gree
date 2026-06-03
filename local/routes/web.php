<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CartController;
use Gree\Controller\CatalogController;
use Gree\Controller\ContactsController;
use Gree\Controller\HelpController;
use Gree\Controller\OrderController;
use Gree\Controller\HomeController;
use Gree\Controller\LanguageController;
use Gree\Controller\PartnersController;
use Gree\Controller\ProductController;
use Gree\Controller\WhereToBuyController;
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

    // ─── Blog (listing + category + article) ─────────────────────────────────
    $routes->prefix('blog')->name('blog.')->group(static function (RoutingConfigurator $routes): void {
        $routes
            ->get('', static fn() => App::get(BlogController::class)->index())
            ->name('index');

        // ВАЖНО: category-роут ДО show. У них одинаковый шаблон /blog/{x}/,
        // регексы на параметры решают конфликт (advice|news vs остальное).
        $routes
            ->get('{category}/', static fn(string $category) => App::get(BlogController::class)->category($category))
            ->where('category', 'advice|news')
            ->name('category');

        $routes
            ->get('{code}/', static fn(string $code) => App::get(BlogController::class)->show($code))
            ->where('code', '[\w\d\-]+')
            ->name('show');
    });

    // ─── Cart ────────────────────────────────────────────────────────────────
    $routes
        ->get('/cart/', static fn() => App::get(CartController::class)->index())
        ->name('cart.index');

    // ─── Checkout ────────────────────────────────────────────────────────────
    $routes->prefix('order')->name('order.')->group(static function (RoutingConfigurator $routes): void {
        $routes
            ->get('', static fn() => App::get(OrderController::class)->checkout())
            ->name('checkout');
        $routes
            ->get('success/{publicId}/', static fn(string $publicId) => App::get(OrderController::class)->success($publicId))
            ->where('publicId', '[a-f0-9]{8,16}')
            ->name('success');
    });

    // ─── Help / FAQ ─────────────────────────────────────────────────────────
    $routes
        ->get('/help/', static fn() => App::get(HelpController::class)->index())
        ->name('help.index');

    // ─── Contacts ───────────────────────────────────────────────────────────
    $routes
        ->get('/contacts/', static fn() => App::get(ContactsController::class)->index())
        ->name('contacts.index');

    // ─── Where to buy ───────────────────────────────────────────────────────
    $routes
        ->get('/where-to-buy/', static fn() => App::get(WhereToBuyController::class)->index())
        ->name('where_to_buy.index');

    // ─── Partners ───────────────────────────────────────────────────────────
    $routes
        ->get('/partners/', static fn() => App::get(PartnersController::class)->index())
        ->name('partners.index');

    // ─── Language switch ─────────────────────────────────────────────────────
    // ВАЖНО: pattern захардкожен. Использовать Gree\Enum\Locale::pattern() здесь
    // нельзя — Bitrix Routing исполняет ->where(...) на стадии парсинга web.php
    // в собственном routing-cache, где Composer-autoload ещё не прогрет, и
    // получаем "Class Gree\Enum\Locale not found".
    $routes
        ->get('/lang/{locale}/', static fn(string $locale) => App::get(LanguageController::class)->switch($locale))
        ->where('locale', 'ru|uz')
        ->name('lang.switch');
};
