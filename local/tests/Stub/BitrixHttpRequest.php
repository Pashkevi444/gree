<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixHttpRequest
{
    public function getQueryList(): BitrixParameterDictionary
    {
        return new BitrixParameterDictionary([]);
    }
}
