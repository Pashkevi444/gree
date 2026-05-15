<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\OfferDto;
use Gree\Enum\Color;

/** @extends BaseCollection<OfferDto> */
final class OfferCollection extends BaseCollection
{
    public function __construct(OfferDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return OfferDto::class;
    }

    public function minPrice(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }
        return min(array_map(fn(OfferDto $o) => $o->price, $this->toArray()));
    }

    public function maxArea(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }
        return max(array_map(fn(OfferDto $o) => $o->area, $this->toArray()));
    }

    /**
     * Distinct, in insertion order. Skips offers without an assigned color.
     *
     * @return Color[]
     */
    public function uniqueColors(): array
    {
        $out = [];
        foreach ($this as $offer) {
            if ($offer->color === null) {
                continue;
            }
            if (!in_array($offer->color, $out, true)) {
                $out[] = $offer->color;
            }
        }
        return $out;
    }

    /**
     * Distinct, sorted ascending.
     *
     * @return int[]
     */
    public function uniqueAreas(): array
    {
        $set = [];
        foreach ($this as $offer) {
            $set[$offer->area] = true;
        }
        $areas = array_keys($set);
        sort($areas);
        return $areas;
    }
}
