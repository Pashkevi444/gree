<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\GreeStatDto;

/** @extends BaseCollection<GreeStatDto> */
final class GreeStatCollection extends BaseCollection
{
    public function __construct(GreeStatDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return GreeStatDto::class;
    }
}
