<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\MenuItemCollection;

interface MenuServiceInterface
{
    public function getHeaderMenu(): MenuItemCollection;
}
