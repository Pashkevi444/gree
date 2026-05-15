<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
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
 */
return static function (RoutingConfigurator $routes): void {

    // Frontend-pinned endpoints (paths fixed by the JS bundle in /dist).
    $routes
        ->get('/api/catalog', static fn() => App::container()->get(CatalogController::class)->filter())
        ->name('api.catalog.filter');

};
