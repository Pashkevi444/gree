<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\Collection\B2bCardCollection;
use Gree\Collection\BreadcrumbCollection;
use Gree\Collection\CompanyLogoCollection;
use Gree\Collection\HowItWorksCardCollection;

final readonly class PartnersViewData extends BaseViewData
{
    public function __construct(
        public BreadcrumbCollection $breadcrumbs,
        public B2bCardCollection $b2b,
        public HowItWorksCardCollection $howItWorks,
        public CompanyLogoCollection $companies,
    ) {}
}
