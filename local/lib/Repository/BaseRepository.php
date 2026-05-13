<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Iblock\IblockTable;
use Gree\Enum\IblockCode;

abstract class BaseRepository
{
    protected const int TTL = 3600;
    protected const array SORT = ['SORT' => 'ASC', 'TIMESTAMP_X' => 'DESC', 'DATE_CREATE' => 'DESC'];

    protected function resolveIblockId(IblockCode $code): int
    {
        $row = IblockTable::getRow([
            'filter' => ['=API_CODE' => $code->value],
            'select' => ['ID'],
        ]);

        return (int) ($row['ID'] ?? 0);
    }
}
