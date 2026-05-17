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
 * Base class for repositories backed by Bitrix Highloadblocks.
 *
 * Subclasses declare the HL-block via a {@see HlblockCode} enum case — keeps
 * names out of free-form strings (same pattern we use with iblocks via
 * {@see \Gree\Enum\IblockCode}). This base lazily compiles the entity once
 * and caches the data class FQN, so `compileEntity()` doesn't run on every
 * method call.
 *
 * PhpStorm and HL DataManager methods
 * -----------------------------------
 * Bitrix generates the DataManager class at runtime via `eval()`, so PhpStorm
 * can't inspect it via the class name. We expose typed helpers `query()`,
 * `addRow()`, `updateRow()`, `deleteRow()` here — each forwards to the
 * dynamic data class but advertises a concrete return type (`Query`,
 * `AddResult`, etc.). Subclasses navigate through these helpers and PhpStorm
 * autocompletes the entire chain.
 *
 * Why not extend BaseRepository: BaseRepository is iblock-specific (language
 * service, sort order, localized select helpers). HL-block repos don't share
 * those concerns — pulling them in via inheritance would force unrelated
 * deps onto cart/translation tables.
 */
abstract class BaseHlblockRepository
{
    /** @var class-string<\Bitrix\Main\ORM\Data\DataManager>|null */
    private ?string $dataClass = null;

    /**
     * Which Highloadblock this repository talks to. Concrete repo returns a
     * case from {@see HlblockCode}, e.g. `HlblockCode::Carts`.
     */
    abstract protected function hlblock(): HlblockCode;

    /**
     * Start a typed query against the underlying data class. PhpStorm follows
     * the chain via the explicit `Query` return type.
     */
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
