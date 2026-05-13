<?php

declare(strict_types=1);

namespace Gree\View;

abstract readonly class BaseViewData
{
    public function toArray(): array
    {
        return (array) $this;
    }
}
