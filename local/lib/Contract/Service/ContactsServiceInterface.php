<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;

interface ContactsServiceInterface
{
    public function getChannels(): ContactChannelCollection;

    public function getAddresses(): ContactAddressCollection;
}
