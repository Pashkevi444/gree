<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\HowItWorksCardDto;

/** @extends BaseCollection<HowItWorksCardDto> */
final class HowItWorksCardCollection extends BaseCollection
{
    public function __construct(HowItWorksCardDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return HowItWorksCardDto::class;
    }
}
