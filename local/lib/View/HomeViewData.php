<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\ProductCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;

final readonly class HomeViewData extends BaseViewData
{
    public function __construct(
        public SliderItemCollection $slider,
        public GreeCardCollection $greeCards,
        public GreeStatCollection $greeStats,
        public AppFeatureCollection $appFeatures,
        public TechnologyCollection $technologies,
        public ProductCollection $wallProducts,
        public ProductCollection $columnProducts,
        public ProductCollection $industrialProducts,
    ) {}
}
