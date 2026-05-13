<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Contract\DTO\DataTransferObject;

abstract readonly class BaseDto implements DataTransferObject
{
    abstract public static function fromArray(array $data): static;
}
