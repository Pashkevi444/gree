<?php

namespace Sprint\Migration;

/**
 * Тип инфоблока «where_to_buy» + 3 iblock внутри:
 *
 *   where_to_buy_locations — точки продаж (фото, адрес, расписание,
 *                            телефоны, lat/lon для «Показать на карте»)
 *   where_to_buy_partners  — карусель партнёров (логотипы)
 *   where_to_buy_chains    — сетка магазинов-партнёров (логотипы)
 *
 * Тексты — парные _RU/_UZ. Координаты в locations используются JS-обработчиком
 * футер-iframe карты (data-show-on-map="lat,lon" — см. footer.php).
 */
class Version20260604000002 extends Version
{
    protected $description = "Тип iblock 'where_to_buy' + 3 iblock (locations/partners/chains)";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID'        => 'where_to_buy',
            'SECTIONS'  => 'N',
            'IN_RSS'    => 'N',
            'SORT'      => 800,
            'LANG'      => [
                'ru' => ['NAME' => 'Где купить', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
            ],
        ]);

        $this->createLocations();
        $this->createPartners();
        $this->createChains();

        $this->outSuccess('Тип «where_to_buy» и 3 iblock\'а созданы');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        foreach (['where_to_buy_locations', 'where_to_buy_partners', 'where_to_buy_chains'] as $code) {
            $helper->Iblock()->deleteIblockIfExists($code);
        }
        try {
            $helper->Iblock()->deleteIblockTypeIfExists('where_to_buy');
        } catch (\Throwable) {
            // тип удалится только когда пуст
        }
        $this->outSuccess('Where-to-buy iblock\'и удалены');
    }

    private function createLocations(): void
    {
        $id = $this->makeIblock('where_to_buy_locations', 'WhereToBuyLocations', 'Точки продаж', 100);

        $this->addLocalized($id, 'NAME', 'Адрес', 100);
        $this->addLocalized($id, 'SCHEDULE', 'Расписание', 200, 2);

        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Телефоны',
            'CODE'          => 'PHONES',
            'PROPERTY_TYPE' => 'S',
            'MULTIPLE'      => 'Y',
            'SORT'          => '300',
            'HINT'          => 'По одному номеру на значение',
        ]);

        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Фото',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '400',
        ]);

        $this->addPlain($id, 'LATITUDE',  'Широта',  500);
        $this->addPlain($id, 'LONGITUDE', 'Долгота', 510);
    }

    private function createPartners(): void
    {
        $id = $this->makeIblock('where_to_buy_partners', 'WhereToBuyPartners', 'Партнёры (карусель)', 200);
        $this->addLocalized($id, 'NAME', 'Название (для alt)', 100);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Логотип',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '200',
        ]);
    }

    private function createChains(): void
    {
        $id = $this->makeIblock('where_to_buy_chains', 'WhereToBuyChains', 'Сети магазинов-партнёров', 300);
        $this->addLocalized($id, 'NAME', 'Название (для alt)', 100);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Логотип',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '200',
        ]);
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function makeIblock(string $code, string $apiCode, string $name, int $sort): int
    {
        $helper = $this->getHelperManager();
        $id = $helper->Iblock()->saveIblock([
            'NAME'           => $name,
            'CODE'           => $code,
            'API_CODE'       => $apiCode,
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'where_to_buy',
            'SORT'           => $sort,
        ]);
        $helper->Iblock()->saveIblockFields($id, [
            'CODE' => [
                'DEFAULT_VALUE' => [
                    'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L',
                    'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y',
                ],
                'IS_REQUIRED' => 'N',
            ],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);
        return (int) $id;
    }

    private function addLocalized(int $iblockId, string $base, string $label, int $sortBase, int $rows = 1): void
    {
        $helper = $this->getHelperManager();
        foreach (['RU', 'UZ'] as $i => $lang) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME'          => sprintf('%s (%s)', $label, $lang),
                'CODE'          => $base . '_' . $lang,
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT'     => (string) $rows,
                'SORT'          => (string) ($sortBase + $i * 10),
            ]);
        }
    }

    private function addPlain(int $iblockId, string $code, string $name, int $sort): void
    {
        $this->getHelperManager()->Iblock()->saveProperty($iblockId, [
            'NAME'          => $name,
            'CODE'          => $code,
            'PROPERTY_TYPE' => 'S',
            'SORT'          => (string) $sort,
        ]);
    }
}
