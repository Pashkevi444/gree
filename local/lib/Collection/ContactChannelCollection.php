<?php

declare(strict_types=1);

namespace Gree\Collection;

use Gree\DTO\ContactChannelDto;

/** @extends BaseCollection<ContactChannelDto> */
final class ContactChannelCollection extends BaseCollection
{
    public function __construct(ContactChannelDto ...$items)
    {
        parent::__construct(array_values($items));
    }

    protected function itemClass(): string
    {
        return ContactChannelDto::class;
    }
}
