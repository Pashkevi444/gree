<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\ChainLogoCollection;
use Gree\Collection\PartnerLogoCollection;
use Gree\Collection\WhereToBuyLocationCollection;

interface WhereToBuyServiceInterface
{
    public function getLocations(): WhereToBuyLocationCollection;

    public function getPartners(): PartnerLogoCollection;

    public function getChains(): ChainLogoCollection;
}
