<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\MenuItemDto;

/** @extends BaseCollection<MenuItemDto> */
final class MenuItemCollection extends BaseCollection
{
    public function __construct(MenuItemDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return MenuItemDto::class;
    }
}
