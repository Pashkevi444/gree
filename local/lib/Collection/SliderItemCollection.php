<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\SliderItemDto;

/** @extends BaseCollection<SliderItemDto> */
final class SliderItemCollection extends BaseCollection
{
    public function __construct(SliderItemDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return SliderItemDto::class;
    }
}
