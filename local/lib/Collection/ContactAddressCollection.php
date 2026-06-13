<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\ContactAddressDto;

/** @extends BaseCollection<ContactAddressDto> */
final class ContactAddressCollection extends BaseCollection
{
    public function __construct(ContactAddressDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return ContactAddressDto::class;
    }
}
