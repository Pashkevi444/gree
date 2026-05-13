<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\TechnologyDto;

/** @extends BaseCollection<TechnologyDto> */
final class TechnologyCollection extends BaseCollection
{
    public function __construct(TechnologyDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return TechnologyDto::class;
    }
}
