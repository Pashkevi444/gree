<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\B2bCardCollection;
use Gree\Collection\CompanyLogoCollection;
use Gree\Collection\HowItWorksCardCollection;

interface PartnersRepositoryInterface
{
    public function getB2b(): B2bCardCollection;

    public function getHowItWorks(): HowItWorksCardCollection;

    public function getCompanies(): CompanyLogoCollection;
}
