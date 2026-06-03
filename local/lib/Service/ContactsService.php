<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\ContactAddressCollection;
use Gree\Collection\ContactChannelCollection;
use Gree\Contract\Repository\ContactsRepositoryInterface;
use Gree\Contract\Service\ContactsServiceInterface;
use Gree\DTO\ContactChannelDto;
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

    /**
     * Fail-soft: вызывается из шапки/футера на каждый запрос — если HL внезапно
     * упал или iblock пропал, возвращаем null чтобы не положить весь шаблон.
     */
    public function findChannelByCode(string $code): ?ContactChannelDto
    {
        try {
            return $this->contactsRepository->findChannelByCode($code);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'code'      => $code,
                'exception' => $e,
            ]);
            return null;
        }
    }
}
