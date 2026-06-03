<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;

interface ContactsRepositoryInterface
{
    public function getChannels(): ContactChannelCollection;

    public function getAddresses(): ContactAddressCollection;
}
