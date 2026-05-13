<?php

declare(strict_types=1);

namespace Gree\DTO;

final readonly class SliderItemDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $subtitle = '',
        public string $buttonText = '',
        public string $buttonUrl = '',
        public string $backgroundImage = '',
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            subtitle: (string) ($data['subtitle'] ?? ''),
            buttonText: (string) ($data['button_text'] ?? ''),
            buttonUrl: (string) ($data['button_url'] ?? ''),
            backgroundImage: (string) ($data['background_image'] ?? ''),
        );
    }
}
