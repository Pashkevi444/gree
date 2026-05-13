<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\ProductDto;

/** @extends BaseCollection<ProductDto> */
final class ProductCollection extends BaseCollection
{
    public function __construct(ProductDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return ProductDto::class;
    }
}
