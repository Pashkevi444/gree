<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

/**
 * Stub of \Bitrix\Main\Routing\Router for unit tests. Returns a deterministic
 * `/route-name/?param=…` shape so assertions can inspect the result without
 * a live Bitrix bootstrap.
 *
 * Production Route::to() uses Application::getInstance()->getRouter()->route()
 * which is class-aliased to this stub via tests/bootstrap.php (no separate
 * alias yet — Route.php imports the real class). For now Route.php is the
 * caller; we need the stubbed Application::getRouter() to return this Router.
 */
class BitrixRouter
{
    /**
     * @param array<string, scalar> $parameters
     */
    public function route(string $name, array $parameters = []): ?string
    {
        $url = '/' . $name . '/';
        if ($parameters) {
            $url .= '?' . http_build_query($parameters);
        }
        return $url;
    }
}
