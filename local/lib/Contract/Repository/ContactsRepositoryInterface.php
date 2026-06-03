<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\DTO\ContactChannelDto;

interface ContactsRepositoryInterface
{
    public function getChannels(): ContactChannelCollection;

    public function getAddresses(): ContactAddressCollection;

    /**
     * Один канал по стабильному CODE iblock-элемента
     * (orders-telegram / office / service-center / email).
     */
    public function findChannelByCode(string $code): ?ContactChannelDto;
}
