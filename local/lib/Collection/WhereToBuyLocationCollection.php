<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\WhereToBuyLocationDto;

/** @extends BaseCollection<WhereToBuyLocationDto> */
final class WhereToBuyLocationCollection extends BaseCollection
{
    public function __construct(WhereToBuyLocationDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return WhereToBuyLocationDto::class;
    }
}
