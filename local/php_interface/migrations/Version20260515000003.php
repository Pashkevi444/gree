<?php

namespace Sprint\Migration;

/**
 * Для каждого инфоблока с переводимым контентом создаём ПАРНЫЕ строковые свойства:
 *   <CODE>_RU — русский текст
 *   <CODE>_EN — английский текст
 *
 * Стандартные поля NAME, PREVIEW_TEXT, DETAIL_TEXT СОХРАНЯЮТСЯ (Bitrix требует NAME),
 * но репозиторий перестаёт их читать — выводимый текст всегда из <CODE>_<LOCALE>
 * свойства. Поля выступают только как служебная метка для админки/URL.
 *
 * После применения нужно прогнать миграцию переноса данных (Version20260515000004),
 * которая скопирует значения из старых полей/свойств в новые _RU свойства.
 */
class Version20260515000003 extends Version
{
    protected $description = "Парные _RU/_EN свойства для всех текстов инфоблоков";

    private array $iblocks = [
        'products' => [
            ['suffix' => 'NAME',         'label' => 'Название',          'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Краткое описание',  'rows' => 4],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Описание',          'rows' => 10],
        ],
        'brands' => [
            ['suffix' => 'NAME',         'label' => 'Название',          'rows' => 1],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Описание',          'rows' => 10],
        ],
        'home_slider' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 2],
            ['suffix' => 'SUBTITLE',     'label' => 'Подзаголовок',      'rows' => 3],
            ['suffix' => 'BUTTON_TEXT',  'label' => 'Текст кнопки',      'rows' => 1],
        ],
        'home_gree_cards' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Описание',          'rows' => 3],
        ],
        'home_gree_stats' => [
            ['suffix' => 'NAME',           'label' => 'Заголовок',       'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT',   'label' => 'Описание',        'rows' => 3],
            ['suffix' => 'NUMBER_PREFIX',  'label' => 'Префикс числа',   'rows' => 1],
            ['suffix' => 'NUMBER_SUFFIX',  'label' => 'Суффикс числа',   'rows' => 1],
        ],
        'home_app_features' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Описание',          'rows' => 3],
        ],
        'home_technologies' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Описание',          'rows' => 3],
        ],
        'brand_history' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Текст',             'rows' => 12],
        ],
        'brand_why_gree' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Описание',          'rows' => 4],
            ['suffix' => 'BUTTON_TEXT',  'label' => 'Текст кнопки',      'rows' => 1],
        ],
        'brand_gree_cards' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Описание',          'rows' => 3],
        ],
        'brand_gree_stats' => [
            ['suffix' => 'NAME',           'label' => 'Заголовок',       'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT',   'label' => 'Описание',        'rows' => 3],
            ['suffix' => 'NUMBER_PREFIX',  'label' => 'Префикс числа',   'rows' => 1],
            ['suffix' => 'NUMBER_SUFFIX',  'label' => 'Суффикс числа',   'rows' => 1],
        ],
        'brand_about_cards' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Текст',             'rows' => 8],
        ],
        'brand_technologies' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Текст',             'rows' => 8],
        ],
        'blog' => [
            ['suffix' => 'NAME',         'label' => 'Заголовок',         'rows' => 1],
            ['suffix' => 'PREVIEW_TEXT', 'label' => 'Анонс',             'rows' => 4],
            ['suffix' => 'DETAIL_TEXT',  'label' => 'Статья',            'rows' => 12],
        ],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $createdTotal = 0;

        foreach ($this->iblocks as $iblockCode => $fields) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($iblockCode);
            if (!$iblockId) {
                $this->out('Инфоблок "%s" не найден, пропущен', $iblockCode);
                continue;
            }

            $sort = 1000;
            foreach ($fields as $field) {
                foreach (['RU', 'EN'] as $lang) {
                    $helper->Iblock()->saveProperty($iblockId, [
                        'NAME'          => sprintf('%s (%s)', $field['label'], $lang),
                        'CODE'          => $field['suffix'] . '_' . $lang,
                        'PROPERTY_TYPE' => 'S',
                        'ROW_COUNT'     => (string) ($field['rows'] ?? 1),
                        'SORT'          => (string) $sort,
                    ]);
                    $sort += 10;
                    $createdTotal++;
                }
            }
            $this->out('  %s: добавлено свойств %d', $iblockCode, count($fields) * 2);
        }

        $this->outSuccess('Парные RU/EN свойства добавлены, всего: %d', $createdTotal);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();

        foreach ($this->iblocks as $iblockCode => $fields) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($iblockCode);
            if (!$iblockId) {
                continue;
            }
            foreach ($fields as $field) {
                foreach (['RU', 'EN'] as $lang) {
                    $helper->Iblock()->deletePropertyIfExists($iblockId, $field['suffix'] . '_' . $lang);
                }
            }
        }
        $this->outSuccess('Парные _RU/_EN свойства удалены');
    }
}
