<?php

namespace Sprint\Migration;

/**
 * Расширяет инфоблок products свойствами для страницы детальной карточки:
 *
 *   Простые (не переводятся):                        SKU, MODEL, ENERGY_CLASS, REFRIGERANT,
 *                                                     IN_STOCK
 *   С парами _RU/_EN (единицы / форматы локали):     COOLING_POWER, HEATING_POWER, NOISE,
 *                                                     INDOOR_DIMENSIONS, OUTDOOR_DIMENSIONS,
 *                                                     INDOOR_WEIGHT, OUTDOOR_WEIGHT,
 *                                                     WARRANTY_TEXT, KIT_TEXT, INSTALLATION_TEXT
 *   Списки:                                          FUNCTIONS (multi-S — коды функций,
 *                                                     переводятся через HL Translations
 *                                                     по ключу function.<code>)
 *   Файлы:                                           GALLERY (multi-F — доп. изображения)
 */
class Version20260516000003 extends Version
{
    protected $description = "Свойства детальной страницы товара (specs, gallery, functions, stock)";

    /** @var array<int, array{suffix: string, label: string, rows?: int}> */
    private array $localizedFields = [
        ['suffix' => 'COOLING_POWER',      'label' => 'Мощность охлаждения'],
        ['suffix' => 'HEATING_POWER',      'label' => 'Мощность обогрева'],
        ['suffix' => 'NOISE',              'label' => 'Уровень шума'],
        ['suffix' => 'INDOOR_DIMENSIONS',  'label' => 'Габариты внутреннего блока'],
        ['suffix' => 'OUTDOOR_DIMENSIONS', 'label' => 'Габариты наружного блока'],
        ['suffix' => 'INDOOR_WEIGHT',      'label' => 'Вес внутреннего блока'],
        ['suffix' => 'OUTDOOR_WEIGHT',     'label' => 'Вес наружного блока'],
        ['suffix' => 'WARRANTY_TEXT',      'label' => 'Гарантия (текст)',       'rows' => 8],
        ['suffix' => 'KIT_TEXT',           'label' => 'Комплектация (текст)',   'rows' => 6],
        ['suffix' => 'INSTALLATION_TEXT',  'label' => 'Установка (текст)',      'rows' => 6],
    ];

    public function up(): void
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');

        if (!$iblockId) {
            $this->outError('Инфоблок products не найден');
            return;
        }

        // ─── Untranslated single-value strings ────────────────────────────
        $singles = [
            ['NAME' => 'Артикул',                       'CODE' => 'SKU',           'TYPE' => 'S', 'SORT' => 700],
            ['NAME' => 'Модель',                        'CODE' => 'MODEL',         'TYPE' => 'S', 'SORT' => 710],
            ['NAME' => 'Класс энергоэффективности',     'CODE' => 'ENERGY_CLASS',  'TYPE' => 'S', 'SORT' => 720],
            ['NAME' => 'Хладагент',                     'CODE' => 'REFRIGERANT',   'TYPE' => 'S', 'SORT' => 730],
            ['NAME' => 'В наличии (Y/N)',               'CODE' => 'IN_STOCK',      'TYPE' => 'S', 'SORT' => 740],
        ];
        foreach ($singles as $p) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME'          => $p['NAME'],
                'CODE'          => $p['CODE'],
                'PROPERTY_TYPE' => $p['TYPE'],
                'SORT'          => (string) $p['SORT'],
            ]);
        }

        // ─── Translated pairs _RU / _EN ───────────────────────────────────
        $sort = 800;
        foreach ($this->localizedFields as $field) {
            foreach (['RU', 'EN'] as $lang) {
                $helper->Iblock()->saveProperty($iblockId, [
                    'NAME'          => sprintf('%s (%s)', $field['label'], $lang),
                    'CODE'          => $field['suffix'] . '_' . $lang,
                    'PROPERTY_TYPE' => 'S',
                    'ROW_COUNT'     => (string) ($field['rows'] ?? 1),
                    'SORT'          => (string) $sort,
                ]);
                $sort += 10;
            }
        }

        // ─── Functions (multi-string of codes — translated via HL) ────────
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Функции',
            'CODE'          => 'FUNCTIONS',
            'PROPERTY_TYPE' => 'S',
            'MULTIPLE'      => 'Y',
            'SORT'          => '900',
            'HINT'          => 'Коды функций (wifi, 130v, energy-saving, turbo, silent, eco, smart-home, ai)',
        ]);

        // ─── Gallery (multi-file) ─────────────────────────────────────────
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Галерея',
            'CODE'          => 'GALLERY',
            'PROPERTY_TYPE' => 'F',
            'MULTIPLE'      => 'Y',
            'SORT'          => '910',
        ]);

        $this->outSuccess('Свойства детальной добавлены в products');
    }

    public function down(): void
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$iblockId) {
            return;
        }

        $codes = [
            'SKU', 'MODEL', 'ENERGY_CLASS', 'REFRIGERANT', 'IN_STOCK',
            'FUNCTIONS', 'GALLERY',
        ];
        foreach ($this->localizedFields as $field) {
            $codes[] = $field['suffix'] . '_RU';
            $codes[] = $field['suffix'] . '_EN';
        }

        foreach ($codes as $code) {
            $helper->Iblock()->deletePropertyIfExists($iblockId, $code);
        }

        $this->outSuccess('Свойства детальной удалены');
    }
}
