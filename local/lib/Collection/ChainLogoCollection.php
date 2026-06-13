<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\BrandLogoDto;

/** @extends BaseCollection<BrandLogoDto> */
final class ChainLogoCollection extends BaseCollection
{
    public function __construct(BrandLogoDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return BrandLogoDto::class;
    }
}
