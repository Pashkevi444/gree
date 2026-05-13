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

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function reset(): void
    {
        self::$container = null;
    }
}
