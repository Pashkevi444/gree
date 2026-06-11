<?php

declare(strict_types=1);

namespace Gree\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Routing\Exceptions\ParameterNotFoundException;
use Gree\Logging\FileLogger;

/** Статический фасад над Bitrix Router::route(). При неизвестном имени или missing-параметре — "#" + critical-лог (лучше битая ссылка, чем 500 на рендере). */
final class Route extends BaseHelper
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
