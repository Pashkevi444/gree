<?php

namespace Sprint\Migration;

/**
 * Создаёт два инфоблока секции «Почему выбирают Gree» на странице каталога:
 *   • catalog_gree_cards  — 4 карточки преимуществ
 *   • catalog_gree_stats  — 3 статистики
 *
 * Все текстовые поля сразу с парными свойствами _RU / _EN (на сайте два языка).
 */
class Version20260516000001 extends Version
{
    protected $description = "Инфоблоки секции 'Почему выбирают Gree' для каталога";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        // ─── catalog_gree_cards ───────────────────────────────────────────
        $cardsId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Преимущества (каталог)',
            'CODE'           => 'catalog_gree_cards',
            'API_CODE'       => 'CatalogGreeCards',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'catalog',
            'SORT'           => 500,
        ]);

        $helper->Iblock()->saveIblockFields($cardsId, [
            'CODE'        => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'], 'IS_REQUIRED' => 'N'],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);

        $sort = 100;
        foreach ($this->textFields([
            'NAME'         => ['label' => 'Заголовок',    'rows' => 1],
            'PREVIEW_TEXT' => ['label' => 'Описание',     'rows' => 3],
        ]) as $prop) {
            $prop['SORT'] = (string) $sort;
            $helper->Iblock()->saveProperty($cardsId, $prop);
            $sort += 10;
        }
        $helper->Iblock()->saveProperty($cardsId, [
            'NAME'          => 'Код иконки',
            'CODE'          => 'ICON_CODE',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => '300',
        ]);
        $this->outSuccess('Инфоблок catalog_gree_cards id=%d', $cardsId);

        // ─── catalog_gree_stats ───────────────────────────────────────────
        $statsId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Статистика (каталог)',
            'CODE'           => 'catalog_gree_stats',
            'API_CODE'       => 'CatalogGreeStats',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'catalog',
            'SORT'           => 510,
        ]);

        $helper->Iblock()->saveIblockFields($statsId, [
            'CODE'        => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'], 'IS_REQUIRED' => 'N'],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);

        $sort = 100;
        foreach ($this->textFields([
            'NAME'           => ['label' => 'Заголовок',     'rows' => 1],
            'PREVIEW_TEXT'   => ['label' => 'Описание',      'rows' => 3],
            'NUMBER_PREFIX'  => ['label' => 'Префикс числа', 'rows' => 1],
            'NUMBER_SUFFIX'  => ['label' => 'Суффикс числа', 'rows' => 1],
        ]) as $prop) {
            $prop['SORT'] = (string) $sort;
            $helper->Iblock()->saveProperty($statsId, $prop);
            $sort += 10;
        }
        $helper->Iblock()->saveProperty($statsId, [
            'NAME'          => 'Число',
            'CODE'          => 'NUMBER_VALUE',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => '500',
        ]);
        $this->outSuccess('Инфоблок catalog_gree_stats id=%d', $statsId);
    }

    /**
     * Разворачивает карту [suffix => ['label' => …, 'rows' => …]] в плоский список
     * параметров для saveProperty() — для каждого ключа создаются ДВА свойства
     * с суффиксами _RU и _EN. Сайт двуязычный, поэтому каждое текстовое поле
     * сразу получает обе локали.
     *
     * @return array<int, array<string, string>>
     */
    private function textFields(array $map): array
    {
        $out = [];
        foreach ($map as $suffix => $meta) {
            foreach (['RU', 'EN'] as $lang) {
                $out[] = [
                    'NAME'          => sprintf('%s (%s)', $meta['label'], $lang),
                    'CODE'          => $suffix . '_' . $lang,
                    'PROPERTY_TYPE' => 'S',
                    'ROW_COUNT'     => (string) ($meta['rows'] ?? 1),
                ];
            }
        }
        return $out;
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('catalog_gree_stats');
        $helper->Iblock()->deleteIblockIfExists('catalog_gree_cards');
        $this->outSuccess('Инфоблоки catalog_gree_* удалены');
    }
}
