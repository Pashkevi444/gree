<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\Contract\Repository\ContactsRepositoryInterface;
use Gree\Contract\Service\ContactsServiceInterface;
use Gree\Logging\FileLogger;

final class ContactsService extends BaseService implements ContactsServiceInterface
{
    public function __construct(private readonly ContactsRepositoryInterface $contactsRepository) {}

    public function getChannels(): ContactChannelCollection
    {
        try {
            return $this->contactsRepository->getChannels();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }

    public function getAddresses(): ContactAddressCollection
    {
        try {
            return $this->contactsRepository->getAddresses();
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            throw $e;
        }
    }
}
