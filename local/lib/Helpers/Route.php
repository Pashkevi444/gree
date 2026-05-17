<?php

declare(strict_types=1);

namespace Gree\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Routing\Exceptions\ParameterNotFoundException;
use Gree\Logging\FileLogger;

/**
 * Static facade over Bitrix Router::route() — generates URLs from route
 * names declared in `local/routes/{web,api}.php`.
 *
 * Why a facade and not DI: templates (`*.blade.php`, `header.php`, `footer.php`)
 * pull URLs all the time and don't have access to a service container without
 * passing it through every ViewData. A static call keeps templates clean.
 *
 *   Route::to('cart.index')
 *     → "/cart/"
 *
 *   Route::to('catalog.product', ['section' => 'nastennie', 'code' => 'gree-bora-x-07'])
 *     → "/catalog/nastennie/gree-bora-x-07/"
 *
 *   Route::to('api.v1.cart.items.update', ['id' => 42])
 *     → "/api/v1/cart/items/42"
 *
 * If the name is unknown or required parameters are missing — returns "#" and
 * logs critical. Better a broken link than a 500 on render.
 */
final class Route
{
    /**
     * @param array<string, scalar> $parameters
     */
    public static function to(string $name, array $parameters = []): string
    {
        try {
            $url = Application::getInstance()->getRouter()->route($name, $parameters);
            if ($url === null) {
                FileLogger::getInstance()->critical('Route::to() unknown name', ['name' => $name]);
                return '#';
            }
            return $url;
        } catch (ParameterNotFoundException $e) {
            FileLogger::getInstance()->critical('Route::to() missing parameter', [
                'name' => $name,
                'parameters' => array_keys($parameters),
                'exception' => $e,
            ]);
            return '#';
        }
    }
}
