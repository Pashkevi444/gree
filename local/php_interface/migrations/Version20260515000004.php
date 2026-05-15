<?php

namespace Sprint\Migration;

/**
 * Перенос данных из старых полей/свойств в новые _RU свойства.
 *
 *   NAME            (field)  → NAME_RU         (property)
 *   PREVIEW_TEXT    (field)  → PREVIEW_TEXT_RU (property)
 *   DETAIL_TEXT     (field)  → DETAIL_TEXT_RU  (property)
 *   SUBTITLE        (prop)   → SUBTITLE_RU     (property)
 *   BUTTON_TEXT     (prop)   → BUTTON_TEXT_RU  (property)
 *   NUMBER_PREFIX   (prop)   → NUMBER_PREFIX_RU(property)
 *   NUMBER_SUFFIX   (prop)   → NUMBER_SUFFIX_RU(property)
 *
 * EN-свойства остаются пустыми — заполняет Version20260515000005.
 *
 * Зависит от Version20260515000003 (создание _RU/_EN свойств).
 */
class Version20260515000004 extends Version
{
    protected $description = "Перенос текстовых значений в _RU свойства";

    /**
     * iblock CODE → список переносов: [$source, $kind ('field'|'prop'), $targetSuffix]
     */
    private array $map = [
        'products' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
        'brands' => [
            ['NAME',         'field', 'NAME'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
        'home_slider' => [
            ['NAME',         'field', 'NAME'],
            ['SUBTITLE',     'prop',  'SUBTITLE'],
            ['BUTTON_TEXT',  'prop',  'BUTTON_TEXT'],
        ],
        'home_gree_cards' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
        ],
        'home_gree_stats' => [
            ['NAME',           'field', 'NAME'],
            ['PREVIEW_TEXT',   'field', 'PREVIEW_TEXT'],
            ['NUMBER_PREFIX',  'prop',  'NUMBER_PREFIX'],
            ['NUMBER_SUFFIX',  'prop',  'NUMBER_SUFFIX'],
        ],
        'home_app_features' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
        ],
        'home_technologies' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
        ],
        'brand_history' => [
            ['NAME',         'field', 'NAME'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
        'brand_why_gree' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
            ['BUTTON_TEXT',  'prop',  'BUTTON_TEXT'],
        ],
        'brand_gree_cards' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
        ],
        'brand_gree_stats' => [
            ['NAME',           'field', 'NAME'],
            ['PREVIEW_TEXT',   'field', 'PREVIEW_TEXT'],
            ['NUMBER_PREFIX',  'prop',  'NUMBER_PREFIX'],
            ['NUMBER_SUFFIX',  'prop',  'NUMBER_SUFFIX'],
        ],
        'brand_about_cards' => [
            ['NAME',         'field', 'NAME'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
        'brand_technologies' => [
            ['NAME',         'field', 'NAME'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
        'blog' => [
            ['NAME',         'field', 'NAME'],
            ['PREVIEW_TEXT', 'field', 'PREVIEW_TEXT'],
            ['DETAIL_TEXT',  'field', 'DETAIL_TEXT'],
        ],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $touched = 0;
        foreach ($this->map as $iblockCode => $copies) {
            $iblockId = $this->iblockIdByCode($iblockCode);
            if (!$iblockId) {
                $this->out('Инфоблок "%s" не найден', $iblockCode);
                continue;
            }

            $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

            $select = ['ID'];
            foreach ($copies as [$src, $kind]) {
                if ($kind === 'field') {
                    $select[] = $src;
                } else {
                    $select[$src . '_VALUE'] = $src . '.VALUE';
                }
            }

            $result = $entity::query()->setSelect($select)->exec();
            while ($row = $result->fetch()) {
                $propsToSet = [];
                foreach ($copies as [$src, $kind, $targetSuffix]) {
                    $value = $kind === 'field'
                        ? (string) ($row[$src] ?? '')
                        : (string) ($row[$src . '_VALUE'] ?? '');
                    if ($value === '') {
                        continue;
                    }
                    $propsToSet[$targetSuffix . '_RU'] = $value;
                }
                if ($propsToSet) {
                    \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, $propsToSet);
                    $touched++;
                }
            }
            $this->out('  %s: обработано', $iblockCode);
        }

        $this->outSuccess('Перенесено элементов: %d', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется (старые поля не трогаются)');
    }

    private function iblockIdByCode(string $code): int
    {
        $row = \Bitrix\Iblock\IblockTable::query()
            ->where('CODE', $code)
            ->setSelect(['ID'])
            ->exec()
            ->fetch();
        return (int) ($row['ID'] ?? 0);
    }
}
