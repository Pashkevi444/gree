<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixLoader
{
    public static function includeModule(string $module): bool
    {
        return true;
    }
}
