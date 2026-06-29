<?php

declare(strict_types=1);

namespace Gree\DTO;

use Gree\Enum\Locale;

/** Город доставки из справочника HL «Cities». */
final readonly class CityDto extends BaseDto
{
    public function __construct(
        public int $id,
        public string $code,
        public string $nameRu,
        public string $nameUz,
        public int $sort = 0,
    ) {}

    /** @return array{id:int, code:string, name_ru:string, name_uz:string, sort:int} */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'code'    => $this->code,
            'name_ru' => $this->nameRu,
            'name_uz' => $this->nameUz,
            'sort'    => $this->sort,
        ];
    }

    public function nameFor(Locale $locale): string
    {
        return match ($locale) {
            Locale::Uz => $this->nameUz !== '' ? $this->nameUz : $this->nameRu,
            default    => $this->nameRu !== '' ? $this->nameRu : $this->nameUz,
        };
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id:     (int) ($data['id'] ?? 0),
            code:   (string) ($data['code'] ?? ''),
            nameRu: (string) ($data['name_ru'] ?? ''),
            nameUz: (string) ($data['name_uz'] ?? ''),
            sort:   (int) ($data['sort'] ?? 0),
        );
    }
}
