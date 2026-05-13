<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\BrandAboutCardDto;

/** @extends BaseCollection<BrandAboutCardDto> */
final class BrandAboutCardCollection extends BaseCollection
{
    public function __construct(BrandAboutCardDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return BrandAboutCardDto::class;
    }
}
