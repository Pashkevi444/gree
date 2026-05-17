<?php

declare(strict_types=1);

namespace Gree\View;

use Gree\DTO\OrderDto;

final readonly class OrderSuccessViewData extends BaseViewData
{
    public function __construct(
        public OrderDto $order,
    ) {}
}
