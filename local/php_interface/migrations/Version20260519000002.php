<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;

/**
 * Локализационный rename: UF-поля _EN → _UZ.
 *
 *   HL «Translations»:  UF_VALUE_EN  → UF_VALUE_UZ
 *   HL «Seo»:           UF_TITLE_EN, UF_DESCRIPTION_EN, UF_KEYWORDS_EN,
 *                       UF_OG_TITLE_EN, UF_OG_DESCRIPTION_EN  → _UZ
 *   iblock «menu»
 *   (секции):           UF_LABEL_EN  → UF_LABEL_UZ
 *
 * Bitrix хранит UF-данные в физических колонках b_uts_*. Простое переименование
 * FIELD_NAME оставило бы данные осиротевшими, поэтому работаем тремя шагами:
 *   1) создать новое UF_*_UZ → появляется новая колонка;
 *   2) скопировать SQL'ем значения из старой колонки в новую (только в пустые
 *      ячейки — иначе повторный запуск миграции после уже накатанного
 *      Version20260519000003 перетёр бы залитый узбекский текст английским);
 *   3) удалить старое UF_*_EN → исчезает старая колонка.
 *
 * Полностью идемпотентна: каждый из трёх шагов проверяет текущее состояние и
 * пропускается, если уже выполнен. Безопасно прогонять повторно.
 */
class Version20260519000002 extends Version
{
    protected $description = "Переименование HL/UF полей _EN → _UZ (идемпотентно)";

    /** @var array<string, string[]> hlblock NAME → список base-имён без _EN */
    private array $hlblocks = [
        'Translations' => ['UF_VALUE'],
        'Seo'          => ['UF_TITLE', 'UF_DESCRIPTION', 'UF_KEYWORDS', 'UF_OG_TITLE', 'UF_OG_DESCRIPTION'],
    ];

    public function up(): void
    {
        $this->renameAll(reverse: false);
    }

    public function down(): void
    {
        $this->renameAll(reverse: true);
    }

    private function renameAll(bool $reverse): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $renamed = 0;

        foreach ($this->hlblocks as $hlblockName => $bases) {
            $hlblockId = $helper->Hlblock()->getHlblockIdIfExists($hlblockName);
            if (!$hlblockId) {
                $this->out('  HL %s не найден, пропуск', $hlblockName);
                continue;
            }
            $tableName = (string) HighloadBlockTable::getById($hlblockId)->fetch()['TABLE_NAME'];

            foreach ($bases as $base) {
                if ($this->renameHlField($hlblockName, $tableName, $base, $reverse)) {
                    $renamed++;
                }
            }
            $this->out('  HL %s обработан', $hlblockName);
        }

        $menuIblockId = $helper->Iblock()->getIblockIdIfExists('menu');
        if ($menuIblockId) {
            if ($this->renameMenuSectionField($menuIblockId, $reverse)) {
                $renamed++;
            }
            $this->out('  iblock menu (sections) обработан');
        }

        $this->outSuccess('Готово. Изменено полей: %d', $renamed);
    }

    /**
     * Безопасное переименование UF-поля у HL-блока: create new → SQL copy
     * (только в пустые ячейки) → удалить old. Идемпотентно: при повторном
     * запуске пропускает уже выполненные шаги.
     */
    private function renameHlField(string $hlblockName, string $tableName, string $base, bool $reverse): bool
    {
        $helper = $this->getHelperManager();

        $oldName = $base . ($reverse ? '_UZ' : '_EN');
        $newName = $base . ($reverse ? '_EN' : '_UZ');

        $old = $helper->Hlblock()->getField($hlblockName, $oldName);
        $new = $helper->Hlblock()->getField($hlblockName, $newName);

        // Уже всё сделано.
        if (!$old && $new) {
            return false;
        }
        // Совсем ничего нет — нечего переименовывать.
        if (!$old && !$new) {
            return false;
        }

        // Создаём новое поле, если ещё нет.
        if (!$new) {
            $helper->Hlblock()->saveField($hlblockName, [
                'FIELD_NAME'        => $newName,
                'USER_TYPE_ID'      => $old['USER_TYPE_ID'] ?? 'string',
                'MANDATORY'         => $old['MANDATORY'] ?? 'N',
                'SHOW_FILTER'       => $old['SHOW_FILTER'] ?? 'N',
                'EDIT_FORM_LABEL'   => $this->relabel($old['EDIT_FORM_LABEL'] ?? [], $reverse),
                'LIST_COLUMN_LABEL' => $this->relabel($old['LIST_COLUMN_LABEL'] ?? [], $reverse),
                'SETTINGS'          => $old['SETTINGS'] ?? [],
            ]);
        }

        // SQL-копирование: только в строки, где новое поле пустое, чтобы не
        // затереть уже залитый узбекский из Version20260519000003.
        $connection = Application::getConnection();
        $sql = $connection->getSqlHelper();
        $qNew   = $sql->quote($newName);
        $qOld   = $sql->quote($oldName);
        $qTable = $sql->quote($tableName);
        $connection->queryExecute(
            "UPDATE {$qTable} SET {$qNew} = {$qOld} WHERE ({$qNew} IS NULL OR {$qNew} = '')"
        );

        $helper->Hlblock()->deleteField($hlblockName, $oldName);

        return true;
    }

    private function renameMenuSectionField(int $iblockId, bool $reverse): bool
    {
        $oldName = 'UF_LABEL' . ($reverse ? '_UZ' : '_EN');
        $newName = 'UF_LABEL' . ($reverse ? '_EN' : '_UZ');

        $entityId = 'IBLOCK_' . $iblockId . '_SECTION';
        $entity = new \CUserTypeEntity();

        $oldRow = \CUserTypeEntity::GetList([], ['ENTITY_ID' => $entityId, 'FIELD_NAME' => $oldName])->Fetch();
        $newRow = \CUserTypeEntity::GetList([], ['ENTITY_ID' => $entityId, 'FIELD_NAME' => $newName])->Fetch();

        if (!$oldRow && $newRow) {
            return false;
        }
        if (!$oldRow && !$newRow) {
            return false;
        }

        if (!$newRow) {
            $entity->Add([
                'ENTITY_ID'         => $entityId,
                'FIELD_NAME'        => $newName,
                'USER_TYPE_ID'      => $oldRow['USER_TYPE_ID'] ?: 'string',
                'XML_ID'            => '',
                'SORT'              => $oldRow['SORT'] ?: 110,
                'MULTIPLE'          => $oldRow['MULTIPLE'] ?: 'N',
                'MANDATORY'         => $oldRow['MANDATORY'] ?: 'N',
                'SHOW_FILTER'       => $oldRow['SHOW_FILTER'] ?: 'N',
                'SHOW_IN_LIST'      => $oldRow['SHOW_IN_LIST'] ?: 'Y',
                'EDIT_IN_LIST'      => $oldRow['EDIT_IN_LIST'] ?: 'Y',
                'IS_SEARCHABLE'     => $oldRow['IS_SEARCHABLE'] ?: 'N',
                'EDIT_FORM_LABEL'   => $this->relabel(['ru' => $oldRow['EDIT_FORM_LABEL'] ?? 'Название'], $reverse),
                'LIST_COLUMN_LABEL' => $this->relabel(['ru' => $oldRow['LIST_COLUMN_LABEL'] ?? 'UZ'], $reverse),
                'LIST_FILTER_LABEL' => [],
                'ERROR_MESSAGE'     => [],
                'HELP_MESSAGE'      => [],
            ]);
        }

        $connection = Application::getConnection();
        $sql = $connection->getSqlHelper();
        $tableName = 'b_uts_iblock_' . $iblockId . '_section';
        $qTable = $sql->quote($tableName);
        $qNew = $sql->quote($newName);
        $qOld = $sql->quote($oldName);
        $connection->queryExecute(
            "UPDATE {$qTable} SET {$qNew} = {$qOld} WHERE ({$qNew} IS NULL OR {$qNew} = '')"
        );

        if ($oldRow) {
            $entity->Delete((int) $oldRow['ID']);
        }

        return true;
    }

    /**
     * @param array<string, string>|string $label
     * @return array<string, string>
     */
    private function relabel(array|string $label, bool $reverse): array
    {
        if (is_string($label)) {
            $label = ['ru' => $label];
        }
        $from = $reverse ? '/(UZ)/u' : '/(EN)/u';
        $to   = $reverse ? 'EN' : 'UZ';
        $out  = [];
        foreach ($label as $lang => $text) {
            $out[$lang] = (string) preg_replace($from, $to, (string) $text);
        }
        return $out ?: ['ru' => $reverse ? 'EN' : 'UZ'];
    }
}
