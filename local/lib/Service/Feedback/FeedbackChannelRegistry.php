<?php

declare(strict_types=1);

namespace Gree\Service\Feedback;

use Gree\Contract\Feedback\FeedbackChannelInterface;

/**
 * Реестр feedback-каналов. Заполняется через DI: все каналы передаются
 * массивом в конструктор. Реестр индексирует их по id() и резолвит по
 * slug'у из URL.
 */
final class FeedbackChannelRegistry
{
    /** @var array<string, FeedbackChannelInterface> */
    private array $byId = [];

    /**
     * @param iterable<FeedbackChannelInterface> $channels
     */
    public function __construct(iterable $channels = [])
    {
        foreach ($channels as $channel) {
            $this->register($channel);
        }
    }

    public function register(FeedbackChannelInterface $channel): void
    {
        $id = $channel->id();
        if (isset($this->byId[$id])) {
            throw new \LogicException("FeedbackChannel «{$id}» уже зарегистрирован");
        }
        $this->byId[$id] = $channel;
    }

    public function get(string $id): ?FeedbackChannelInterface
    {
        return $this->byId[$id] ?? null;
    }

    /**
     * @return string[]
     */
    public function ids(): array
    {
        return array_keys($this->byId);
    }
}
