<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\CityDto;

/** @extends BaseCollection<CityDto> */
final class CityCollection extends BaseCollection
{
    public function __construct(CityDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return CityDto::class;
    }

    public function findById(int $id): ?CityDto
    {
        foreach ($this as $city) {
            if ($city->id === $id) {
                return $city;
            }
        }
        return null;
    }
}
