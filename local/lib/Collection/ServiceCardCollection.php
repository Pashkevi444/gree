<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\ServiceCardDto;

/** @extends BaseCollection<ServiceCardDto> */
final class ServiceCardCollection extends BaseCollection
{
    public function __construct(ServiceCardDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return ServiceCardDto::class;
    }
}
