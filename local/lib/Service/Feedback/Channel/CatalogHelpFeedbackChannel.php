<?php

declare(strict_types=1);

namespace Gree\Service\Feedback\Channel;

use Gree\Contract\Feedback\FeedbackChannelInterface;
use Gree\Enum\HlblockCode;

/**
 * Канал «Нужна помощь?» с детальной карточки товара. Минимум полей:
 * name + phone, отдельный HL «CatalogHelpFeedback» (созданный
 * Version20260604000011).
 */
final class CatalogHelpFeedbackChannel implements FeedbackChannelInterface
{
    public function id(): string
    {
        return 'catalog-help';
    }

    public function hlblock(): HlblockCode
    {
        return HlblockCode::CatalogHelpFeedback;
    }

    public function allowedFields(): array
    {
        return ['name', 'phone'];
    }

    public function requiredFields(): array
    {
        return ['name', 'phone'];
    }

    public function mapToRow(array $input): array
    {
        return [
            'UF_NAME'  => trim((string) ($input['name']  ?? '')),
            'UF_PHONE' => trim((string) ($input['phone'] ?? '')),
        ];
    }
}
