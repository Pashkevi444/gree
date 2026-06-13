<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\ChainLogoCollection;
use Gree\Collection\PartnerLogoCollection;
use Gree\Collection\WhereToBuyLocationCollection;

final readonly class WhereToBuyViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public WhereToBuyLocationCollection $locations,
        public PartnerLogoCollection $partners,
        public ChainLogoCollection $chains,
    ) {}
}
