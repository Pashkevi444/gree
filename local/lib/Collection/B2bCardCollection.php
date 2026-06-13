<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\B2bCardDto;

/** @extends BaseCollection<B2bCardDto> */
final class B2bCardCollection extends BaseCollection
{
    public function __construct(B2bCardDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return B2bCardDto::class;
    }
}
