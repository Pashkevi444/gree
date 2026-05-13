<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CatalogController;
use Gree\Controller\HomeController;
use Gree\Controller\ProductController;
use Gree\Core\App;

return static function (RoutingConfigurator $routes): void {

    $routes->get('/', static fn() => App::container()->get(HomeController::class)->index())->name('home');

    $routes->get('/catalog/', static fn() => App::container()->get(CatalogController::class)->index())->name('catalog.index');
    $routes
        ->get('/catalog/{code}/', static fn(string $code) => App::container()->get(ProductController::class)->show($code))
        ->where('code', '[\w\d\-]+')
        ->name('catalog.product');

    $routes
        ->get('/brand/{code}/', static fn(string $code) => App::container()->get(BrandController::class)->show($code))
        ->where('code', '[\w\d\-]+')
        ->name('brand');

    $routes->get('/blog/', static fn() => App::container()->get(BlogController::class)->index())->name('blog.index');
    $routes
        ->get('/blog/{code}/', static fn(string $code) => App::container()->get(BlogController::class)->show($code))
        ->where('code', '[\w\d\-]+')
        ->name('blog.show');

};
