<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\DTO\BrandHistoryDto;
use Gree\DTO\BrandWhyGreeDto;

final readonly class BrandViewData extends BaseViewData
{
    public function __construct(
        public ?BrandHistoryDto $history,
        public ?BrandWhyGreeDto $whyGree,
        public GreeCardCollection $greeCards,
        public GreeStatCollection $greeStats,
        public BrandAboutCardCollection $aboutCards,
        public TechnologyCollection $technologies,
    ) {}
}
