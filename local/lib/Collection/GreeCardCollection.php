<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\GreeCardDto;

/** @extends BaseCollection<GreeCardDto> */
final class GreeCardCollection extends BaseCollection
{
    public function __construct(GreeCardDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return GreeCardDto::class;
    }
}
