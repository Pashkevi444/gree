<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Query\Query;
use Gree\Enum\HlblockCode;

/**
 * База для HL-block репозиториев: типизированные хелперы query()/addRow()/… нужны,
 * потому что Bitrix генерит DataManager через eval() и IDE его не видит.
 */
abstract class BaseHlblockRepository
{
    /** Месяц: D7 ORM сам сбрасывает query-кеш при правках из админки, TTL страхует лишь от изменений в обход ORM. */
    protected const int TTL_STATIC = 2592000;

    /** @var class-string<\Bitrix\Main\ORM\Data\DataManager>|null */
    private ?string $dataClass = null;

    abstract protected function hlblock(): HlblockCode;

    protected function query(): Query
    {
        $cls = $this->dataClass();
        return $cls::query();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function addRow(array $data): AddResult
    {
        $cls = $this->dataClass();
        return $cls::add($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function updateRow(int|string $id, array $data): UpdateResult
    {
        $cls = $this->dataClass();
        return $cls::update($id, $data);
    }

    protected function deleteRow(int|string $id): DeleteResult
    {
        $cls = $this->dataClass();
        return $cls::delete($id);
    }

    /**
     * @return class-string<\Bitrix\Main\ORM\Data\DataManager>
     */
    private function dataClass(): string
    {
        if ($this->dataClass !== null) {
            return $this->dataClass;
        }
        Loader::includeModule('highloadblock');

        $name = $this->hlblock()->value;
        $hlblock = HighloadBlockTable::getList(['filter' => ['=NAME' => $name]])->fetch();
        if (!$hlblock) {
            throw new \RuntimeException('Highloadblock «' . $name . '» not found');
        }

        return $this->dataClass = HighloadBlockTable::compileEntity($hlblock)->getDataClass();
    }
}
