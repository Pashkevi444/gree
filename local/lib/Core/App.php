<?php

declare(strict_types=1);

namespace Gree\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class App
{
    private static ?ContainerInterface $container = null;

    private function __construct() {}

    public static function container(): ContainerInterface
    {
        if (self::$container === null) {
            self::$container = require dirname(__DIR__) . '/config/services.php';
        }

        return self::$container;
    }

    /**
     * Typed shortcut to fetch a service from the container. The generic PHPDoc
     * lets PhpStorm / PHPStan / Psalm infer the concrete return type from the
     * passed class-string, so navigation and autocompletion work:
     *
     *   App::get(CatalogService::class)->getList($filter)  // ↑ CatalogService
     *   App::get(LanguageServiceInterface::class)->get()    // ↑ LanguageServiceInterface
     *
     * Prefer this over `App::container()->get(...)` everywhere outside DI bootstrap.
     *
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public static function get(string $id): object
    {
        /** @var T $service */
        $service = self::container()->get($id);
        return $service;
    }

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function reset(): void
    {
        self::$container = null;
    }
}
