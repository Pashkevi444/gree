<?php

declare(strict_types=1);

namespace Gree\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

final class Blade
{
    private static ?Factory $factory = null;

    private function __construct() {}

    public static function factory(): Factory
    {
        if (self::$factory === null) {
            self::$factory = self::createFactory();
        }

        return self::$factory;
    }

    private static function createFactory(): Factory
    {
        // Use Bitrix's documented accessor so we're not coupled to $_SERVER.
        // Application::getDocumentRoot() is the framework-native source.
        $root = \Bitrix\Main\Application::getDocumentRoot();
        $viewsPath = $root . '/local/views';
        $cachePath = $root . '/local/cache/blade';
        $templatePath = $root . '/local/templates/gree';

        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        $files = new Filesystem();
        $resolver = new EngineResolver();

        $resolver->register('blade', static function () use ($files, $cachePath): BitrixBladeEngine {
            return new BitrixBladeEngine(new BladeCompiler($files, $cachePath), $files);
        });

        $resolver->register('php', static function () use ($files): PhpEngine {
            return new PhpEngine($files);
        });

        $finder = new FileViewFinder($files, [$viewsPath]);

        $factory = new Factory($resolver, $finder, new Dispatcher());
        $factory->share('templatePath', $templatePath);

        return $factory;
    }
}
