<?php

declare(strict_types=1);

namespace Gree\Repository;

use Gree\Collection\MenuItemCollection;
use Gree\Contract\Repository\MenuRepositoryInterface;
use Gree\DTO\MenuItemDto;
use Gree\Enum\IblockCode;
use Gree\Enum\Locale;

final class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    public function getTree(): MenuItemCollection
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblockId = $this->resolveIblockId(IblockCode::Menu);
        if (!$iblockId) {
            return new MenuItemCollection();
        }

        $sectionClass = \Bitrix\Iblock\Model\Section::compileEntityByIblock($iblockId);
        if (!$sectionClass) {
            return new MenuItemCollection();
        }

        $result = $sectionClass::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'CODE', 'IBLOCK_SECTION_ID', 'SORT', 'UF_LABEL_RU', 'UF_LABEL_EN', 'UF_URL'])
            ->setOrder(['DEPTH_LEVEL' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC'])
            ->setCacheTtl(self::TTL)
            ->exec();

        $byParent = [];
        while ($row = $result->fetch()) {
            $parent = (int) ($row['IBLOCK_SECTION_ID'] ?? 0);
            $byParent[$parent][] = $row;
        }

        return $this->buildBranch(0, $byParent);
    }

    /**
     * @param array<int, array<int, array<string, mixed>>> $byParent
     */
    private function buildBranch(int $parentId, array $byParent): MenuItemCollection
    {
        $rows = $byParent[$parentId] ?? [];
        $items = [];
        foreach ($rows as $row) {
            $id = (int) $row['ID'];
            $items[] = new MenuItemDto(
                id: $id,
                code: (string) ($row['CODE'] ?? ''),
                label: $this->pickLabel($row),
                url: (string) ($row['UF_URL'] ?? ''),
                children: $this->buildBranch($id, $byParent),
            );
        }
        return new MenuItemCollection(...$items);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function pickLabel(array $row): string
    {
        $ru = (string) ($row['UF_LABEL_RU'] ?? '');
        $en = (string) ($row['UF_LABEL_EN'] ?? '');

        if ($this->locale() === Locale::En) {
            return $en !== '' ? $en : $ru;
        }
        return $ru !== '' ? $ru : $en;
    }
}
