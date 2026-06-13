<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Contract\Repository\FeedbackRepositoryInterface;
use Gree\Contract\Service\FeedbackServiceInterface;
use Gree\Logging\FileLogger;
use Gree\Service\Feedback\FeedbackChannelRegistry;

final class FeedbackService extends BaseService implements FeedbackServiceInterface
{
    public function __construct(
        private readonly FeedbackChannelRegistry $channels,
        private readonly FeedbackRepositoryInterface $repository,
    ) {}

    public function save(string $channelId, array $input): int
    {
        $channel = $this->channels->get($channelId);
        if ($channel === null) {
            throw new \DomainException("Unknown feedback channel «{$channelId}»");
        }

        // Валидация обязательных полей. Whitespace-only считаем пустым.
        $allowed = $channel->allowedFields();
        $sanitised = [];
        foreach ($allowed as $field) {
            $value = $input[$field] ?? '';
            $sanitised[$field] = is_string($value) ? trim($value) : $value;
        }
        foreach ($channel->requiredFields() as $field) {
            $value = $sanitised[$field] ?? '';
            if ($value === '' || $value === null) {
                throw new \InvalidArgumentException("Field «{$field}» is required");
            }
        }

        try {
            $row = $channel->mapToRow($sanitised);
            return $this->repository->insert($channel->hlblock(), $row);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'channel'   => $channelId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
