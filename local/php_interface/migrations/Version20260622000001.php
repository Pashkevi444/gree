<?php

namespace Sprint\Migration;

/**
 * Синхронизирует значения L-свойства COLOR у iblock `products_offers` с
 * `Gree\Enum\Color`: добавляет gold/blue, переименовывает silver → «Серебряный»,
 * удаляет устаревший champagne (если на нём не висит ни одного ТП).
 *
 * Финальный набор: white, black, gold, blue, silver (в порядке SORT).
 *
 * Идемпотентно: добавляет недостающие, обновляет VALUE/SORT существующих,
 * сносит champagne только когда им никто не пользуется.
 */
class Version20260622000001 extends Version
{
    protected $description = "products_offers.COLOR — финальный набор: white/black/gold/blue/silver";

    /** @var array<string, array{value: string, sort: int}> XML_ID → label+sort */
    private array $target = [
        'white'  => ['value' => 'Белый',      'sort' => 10],
        'black'  => ['value' => 'Чёрный',     'sort' => 20],
        'gold'   => ['value' => 'Золотой',    'sort' => 30],
        'blue'   => ['value' => 'Синий',      'sort' => 40],
        'silver' => ['value' => 'Серебряный', 'sort' => 50],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$iblockId) {
            $this->outError('iblock products_offers не найден');
            return;
        }

        $property = \CIBlockProperty::GetByID('COLOR', $iblockId)->Fetch();
        if (!$property) {
            $this->outError('свойство COLOR не найдено');
            return;
        }
        $propertyId = (int) $property['ID'];

        $enum = new \CIBlockPropertyEnum();

        // 1. Текущие варианты по XML_ID → ID.
        $existing = [];
        $res = \CIBlockPropertyEnum::GetList([], ['PROPERTY_ID' => $propertyId]);
        while ($row = $res->Fetch()) {
            $existing[$row['XML_ID']] = $row;
        }

        // 2. Добавить/обновить целевые.
        $upserted = 0;
        foreach ($this->target as $xmlId => $cfg) {
            $fields = [
                'PROPERTY_ID' => $propertyId,
                'VALUE'       => $cfg['value'],
                'XML_ID'      => $xmlId,
                'SORT'        => $cfg['sort'],
                'DEF'         => 'N',
            ];
            if (isset($existing[$xmlId])) {
                $enum->Update((int) $existing[$xmlId]['ID'], $fields);
            } else {
                $enum->Add($fields);
            }
            $upserted++;
        }

        // 3. Снести устаревшие XML_ID (champagne и любые другие не из target),
        //    только если ими не пользуется ни одно ТП — иначе осиротеют значения.
        foreach ($existing as $xmlId => $row) {
            if (isset($this->target[$xmlId])) {
                continue;
            }
            // arGroupBy=false — иначе GetList возвращает int/string count, а не CIBlockResult.
            $inUse = (int) \CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $iblockId, 'PROPERTY_COLOR' => (int) $row['ID']],
                false,
                false,
                ['ID'],
            )->SelectedRowsCount();
            if ($inUse > 0) {
                $this->out('  оставлен %s — используется в %d ТП', $xmlId, $inUse);
                continue;
            }
            $enum->Delete((int) $row['ID']);
            $this->outSuccess('удалён enum-вариант %s', $xmlId);
        }

        \CIBlock::clearIblockTagCache($iblockId);
        \Bitrix\Iblock\IblockTable::cleanCache();

        $this->outSuccess('COLOR upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — финальный набор цветов проекта');
    }
}
