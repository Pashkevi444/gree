<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\BreadcrumbDto;

/** @extends BaseCollection<BreadcrumbDto> */
final class BreadcrumbCollection extends BaseCollection
{
    public function __construct(BreadcrumbDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return BreadcrumbDto::class;
    }
}
