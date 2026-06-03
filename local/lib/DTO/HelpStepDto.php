<?php

declare(strict_types=1);

namespace Gree\DTO;

/**
 * Универсальная DTO для шага инструкции (exchange / refund). Tooltip
 * непустой только у некоторых шагов (например, 3-й шаг возврата).
 */
final readonly class HelpStepDto extends BaseDto
{
    public function __construct(
        public int $id,
        public int $stepNumber,
        public string $name,
        public string $description = '',
        public string $tooltip = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            stepNumber: (int) ($data['step_number'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            tooltip: (string) ($data['tooltip'] ?? ''),
        );
    }
}
