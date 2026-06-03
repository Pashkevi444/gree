<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\ServiceFeatureDto;

/** @extends BaseCollection<ServiceFeatureDto> */
final class ServiceFeatureCollection extends BaseCollection
{
    public function __construct(ServiceFeatureDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return ServiceFeatureDto::class;
    }
}
