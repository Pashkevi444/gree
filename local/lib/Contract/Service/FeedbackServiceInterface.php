<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

interface FeedbackServiceInterface
{
    /**
     * Сохраняет заявку через канал с заданным id.
     *
     * @param array<string, mixed> $input
     * @return int  ID HL-строки
     *
     * @throws \DomainException        канал с таким id не зарегистрирован
     * @throws \InvalidArgumentException требуемое поле пустое
     */
    public function save(string $channelId, array $input): int;
}
