<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\BrandAboutCardCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\TechnologyCollection;
use Gree\DTO\BrandHistoryDto;
use Gree\DTO\BrandWhyGreeDto;

interface BrandRepositoryInterface
{
    public function getHistory(): ?BrandHistoryDto;
    public function getWhyGree(): ?BrandWhyGreeDto;
    public function getGreeCards(): GreeCardCollection;
    public function getGreeStats(): GreeStatCollection;
    public function getAboutCards(): BrandAboutCardCollection;
    public function getTechnologies(): TechnologyCollection;
}
