<?php

namespace Sprint\Migration;

/**
 * Расширяет инфоблок `blog`:
 *   - CATEGORY (L: tips/news) — для двух секций на странице блога
 *   - NAME_RU/NAME_EN, PREVIEW_TEXT_RU/EN, DETAIL_TEXT_RU/EN — текстовые пары
 *     под мультиязычность сайта (см. CLAUDE.md, текущие языки: ru, en).
 *
 * Картинка статьи живёт в стандартном поле PREVIEW_PICTURE — Bitrix умеет
 * хранить файл напрямую, отдельное свойство IMAGE не нужно.
 *
 * Дополнительно: saveIblockFields() задаёт CODE с авто-транслитерацией и
 * отключает ACTIVE_FROM / ACTIVE_TO / XML_ID / TAGS — стандарт CLAUDE.md.
 */
class Version20260516000017 extends Version
{
    protected $description = "Блог: CATEGORY + RU/EN пары текстовых полей";

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('blog');
        if (!$iblockId) {
            $this->outError('Iblock blog не найден');
            return;
        }

        $helper->Iblock()->saveIblockFields($iblockId, [
            'CODE' => [
                'DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L',
                                    'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'],
                'IS_REQUIRED' => 'N',
            ],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);

        $helper->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Категория',
            'CODE'          => 'CATEGORY',
            'PROPERTY_TYPE' => 'L',
            'IS_REQUIRED'   => 'Y',
            'SORT'          => 50,
            'VALUES'        => [
                ['VALUE' => 'Советы', 'XML_ID' => 'tips', 'DEF' => 'Y', 'SORT' => 10],
                ['VALUE' => 'Новости', 'XML_ID' => 'news', 'DEF' => 'N', 'SORT' => 20],
            ],
        ]);

        $stringPairs = [
            ['CODE' => 'NAME_RU',         'NAME' => 'Название (RU)',         'SORT' => 110],
            ['CODE' => 'NAME_EN',         'NAME' => 'Название (EN)',         'SORT' => 120],
            ['CODE' => 'PREVIEW_TEXT_RU', 'NAME' => 'Краткое описание (RU)', 'SORT' => 210],
            ['CODE' => 'PREVIEW_TEXT_EN', 'NAME' => 'Краткое описание (EN)', 'SORT' => 220],
            ['CODE' => 'DETAIL_TEXT_RU',  'NAME' => 'Текст статьи (RU)',     'SORT' => 310],
            ['CODE' => 'DETAIL_TEXT_EN',  'NAME' => 'Текст статьи (EN)',     'SORT' => 320],
        ];

        foreach ($stringPairs as $p) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME'          => $p['NAME'],
                'CODE'          => $p['CODE'],
                'PROPERTY_TYPE' => 'S',
                'SORT'          => $p['SORT'],
                'ROW_COUNT'     => str_contains($p['CODE'], 'DETAIL') ? 10 : 3,
            ]);
        }

        $this->outSuccess('Блог: добавлены CATEGORY + RU/EN пары');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('blog');
        if (!$iblockId) {
            return;
        }
        foreach (['CATEGORY', 'NAME_RU', 'NAME_EN', 'PREVIEW_TEXT_RU', 'PREVIEW_TEXT_EN', 'DETAIL_TEXT_RU', 'DETAIL_TEXT_EN'] as $code) {
            $helper->Iblock()->deletePropertyIfExists($iblockId, $code);
        }
        $this->outSuccess('Блог: новые свойства удалены');
    }
}
