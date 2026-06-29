<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\CityCollection;
use Gree\Contract\Repository\CityRepositoryInterface;
use Gree\DTO\CityDto;
use Gree\Enum\HlblockCode;

final class CityRepository extends BaseHlblockRepository implements CityRepositoryInterface
{
    protected function hlblock(): HlblockCode
    {
        return HlblockCode::Cities;
    }

    public function all(): CityCollection
    {
        $items = [];
        try {
            $rows = $this->query()
                ->setSelect(['ID', 'UF_CODE', 'UF_NAME_RU', 'UF_NAME_UZ', 'UF_SORT'])
                ->setOrder(['UF_SORT' => 'ASC', 'ID' => 'ASC'])
                ->setCacheTtl(self::TTL_STATIC)
                ->exec();
            while ($row = $rows->fetch()) {
                $items[] = $this->hydrate($row);
            }
        } catch (\Throwable) {
            // HL ещё не создан / БД недоступна — пустой список (контроллер отдаст пустой select).
        }
        return new CityCollection(...$items);
    }

    public function findById(int $id): ?CityDto
    {
        if ($id <= 0) {
            return null;
        }
        try {
            $row = $this->query()
                ->where('ID', $id)
                ->setSelect(['ID', 'UF_CODE', 'UF_NAME_RU', 'UF_NAME_UZ', 'UF_SORT'])
                ->setLimit(1)
                ->setCacheTtl(self::TTL_STATIC)
                ->exec()
                ->fetch();
        } catch (\Throwable) {
            return null;
        }
        return $row ? $this->hydrate($row) : null;
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): CityDto
    {
        return new CityDto(
            id:     (int) $row['ID'],
            code:   (string) ($row['UF_CODE'] ?? ''),
            nameRu: (string) ($row['UF_NAME_RU'] ?? ''),
            nameUz: (string) ($row['UF_NAME_UZ'] ?? ''),
            sort:   (int) ($row['UF_SORT'] ?? 0),
        );
    }
}
