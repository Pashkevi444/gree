<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Enum\HlblockCode;

interface FeedbackRepositoryInterface
{
    /**
     * Записывает строку в указанный HL-блок. UF_CREATED_AT добавляется
     * автоматически (репозиторий — единственный, кто знает «когда»).
     *
     * @param array<string, mixed> $fields  UF_*-поля, кроме UF_CREATED_AT
     * @return int  ID созданной HL-строки
     */
    public function insert(HlblockCode $hlblock, array $fields): int;
}
