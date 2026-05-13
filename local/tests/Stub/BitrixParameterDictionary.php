<?php

declare(strict_types=1);

namespace Gree\Tests\Stub;

class BitrixParameterDictionary
{
    public function __construct(private array $data = []) {}

    public function toArray(): array
    {
        return $this->data;
    }
}
