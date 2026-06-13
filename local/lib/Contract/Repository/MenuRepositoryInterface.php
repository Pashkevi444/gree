<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\MenuItemCollection;

interface MenuRepositoryInterface
{
    /**
     * Дерево пунктов главного (шапка) меню.
     */
    public function getTree(): MenuItemCollection;

    /**
     * Дерево пунктов футер-меню. Корневые секции — заголовки колонок.
     */
    public function getFooterTree(): MenuItemCollection;
}
