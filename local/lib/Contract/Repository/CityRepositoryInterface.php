<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\CityCollection;
use Gree\DTO\CityDto;

interface CityRepositoryInterface
{
    /** Все активные города, отсортированы по UF_SORT. */
    public function all(): CityCollection;

    public function findById(int $id): ?CityDto;
}
