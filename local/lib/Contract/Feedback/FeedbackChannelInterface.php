<?php

declare(strict_types=1);

namespace Gree\Contract\Feedback;

use Gree\Enum\HlblockCode;

/** Канал обратной связи: id+HL для записи заявок, список полей, mapToRow для записи. Реализации регистрируются в FeedbackChannelRegistry через DI. */
interface FeedbackChannelInterface
{
    /** Slug в URL /api/v1/feedback/{id}, только [a-z0-9-]+. */
    public function id(): string;

    public function hlblock(): HlblockCode;

    /** @return string[] */
    public function allowedFields(): array;

    /** @return string[] подмножество allowedFields() */
    public function requiredFields(): array;

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed> UF_*-поля (без UF_CREATED_AT — его ставит FeedbackService)
     */
    public function mapToRow(array $input): array;
}
