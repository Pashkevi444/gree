<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\AppFeatureDto;

/** @extends BaseCollection<AppFeatureDto> */
final class AppFeatureCollection extends BaseCollection
{
    public function __construct(AppFeatureDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return AppFeatureDto::class;
    }
}
