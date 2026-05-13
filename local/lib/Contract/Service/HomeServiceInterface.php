<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;

interface HomeServiceInterface
{
    public function getSlider(): SliderItemCollection;
    public function getGreeCards(): GreeCardCollection;
    public function getGreeStats(): GreeStatCollection;
    public function getAppFeatures(): AppFeatureCollection;
    public function getTechnologies(): TechnologyCollection;
}
