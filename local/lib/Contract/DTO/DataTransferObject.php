<?php

declare(strict_types=1);

namespace Gree\Contract\DTO;

interface DataTransferObject
{
    public static function fromArray(array $data): static;
}
