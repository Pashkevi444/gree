<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Gree\Controller\BlogController;
use Gree\Controller\CartController;
use Gree\Controller\CatalogController;
use Gree\Core\App;

/**
 * API routes — every handler returns an HttpResponse (JSON).
 *
 * IMPORTANT: handlers MUST be closures, not [Class::class, 'method'] arrays.
 * Bitrix Routing calls Loader::requireClass() on array-style actions and tries
 * to load them as a Bitrix module (e.g. namespace Gree\Controller → module
 * "gree.controller"). For non-module Composer classes this throws
 * LoaderException. Closures resolve through Composer's autoloader instead.
 *
 * Grouping notes (Bitrix Routing quirks):
 *   - prefix() args must NOT start with `/`. The router prepends a leading
 *     slash itself; a leading slash in the argument produces `//api/...`.
 *   - URIs inside a group also must NOT start with `/` — they're joined to
 *     the prefix with a single `/` (uri="/x" yields `prefix//x`).
 *   - An empty URI (`get('', …)`) gets a trailing slash from the same join.
 *     That's why the "list" endpoint of cart resolves to `/api/v1/cart/`.
 */
return static function (RoutingConfigurator $routes): void {

    // ─── Legacy frontend-pinned endpoint (kept under /api/, not /api/v1/) ───
    $routes
        ->get('/api/catalog', static fn() => App::get(CatalogController::class)->filter())
        ->name('api.catalog.filter');

    // ─── Versioned API ───────────────────────────────────────────────────────
    $routes->prefix('api/v1')->name('api.v1.')->group(static function (RoutingConfigurator $routes): void {

        // Blog: GET /api/v1/blog?category=tips&offset=3&limit=3
        $routes
            ->get('blog', static fn() => App::get(BlogController::class)->paginate())
            ->name('blog.paginate');

        // Cart: 1 read + 3 mutations
        $routes->prefix('cart')->name('cart.')->group(static function (RoutingConfigurator $routes): void {
            // GET /api/v1/cart/ — empty URI gets a trailing slash from prefix join
            $routes
                ->get('', static fn() => App::get(CartController::class)->get())
                ->name('get');
            $routes
                ->post('items', static fn() => App::get(CartController::class)->add())
                ->name('items.add');
            $routes
                ->patch('items/{id}', static fn(int $id) => App::get(CartController::class)->update($id))
                ->where('id', '\d+')
                ->name('items.update');
            $routes
                ->delete('items/{id}', static fn(int $id) => App::get(CartController::class)->remove($id))
                ->where('id', '\d+')
                ->name('items.remove');
        });
    });
};
