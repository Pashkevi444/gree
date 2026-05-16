<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\MenuItemCollection;

interface MenuRepositoryInterface
{
    /**
     * Returns the menu tree: top-level items with nested children.
     */
    public function getTree(): MenuItemCollection;
}
