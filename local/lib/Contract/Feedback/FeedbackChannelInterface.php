<?php

declare(strict_types=1);

namespace Gree\Contract\Feedback;

use Gree\Enum\HlblockCode;

/**
 * Канал обратной связи — одна форма/модалка/источник заявок.
 *
 * Каждая реализация:
 *   - объявляет свой публичный id (часть URL: /api/v1/feedback/{id}),
 *   - указывает HL-блок, в который пишутся заявки (отдельный grid у менеджера),
 *   - объявляет список разрешённых и обязательных полей,
 *   - мапит провалидированный input в массив UF_* для записи в HL.
 *
 * Регистрируется в {@see FeedbackChannelRegistry} через DI. Чтобы поднять
 * новую модалку — достаточно одного класса (контракт) + регистрации.
 */
interface FeedbackChannelInterface
{
    /** Идентификатор канала (slug в URL). Только [a-z0-9-]+. */
    public function id(): string;

    /** Хайлоад-блок, в который пишутся заявки этого канала. */
    public function hlblock(): HlblockCode;

    /**
     * Список ключей, которые канал ожидает во входе.
     *
     * @return string[]
     */
    public function allowedFields(): array;

    /**
     * Список обязательных ключей. Должен быть подмножеством allowedFields().
     *
     * @return string[]
     */
    public function requiredFields(): array;

    /**
     * Маппинг провалидированного input → массив UF_*-полей для HL.
     * Реализация не должна добавлять UF_CREATED_AT — это делает FeedbackService.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function mapToRow(array $input): array;
}
