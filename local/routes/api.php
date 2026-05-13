<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;

/**
 * API routes — every handler returns an HttpResponse (JSON).
 *
 * Naming convention: api.<resource>.<action>
 * All routes live under /api/v1/ prefix.
 *
 * Pattern:
 *   Controller::action() collects data → calls $this->json($data) or $this->json($error, 422)
 */
return static function (RoutingConfigurator $routes): void {

    $routes
        ->prefix('api/v1')
        ->name('api.')
        ->group(static function (RoutingConfigurator $routes): void {

            // Example:
            // $routes->post('/feedback/', [FeedbackController::class, 'store'])->name('feedback.store');

            $routes->get('/catalog/filter/', [\Gree\Controller\CatalogController::class, 'filter'])->name('catalog.filter');

        });

};
