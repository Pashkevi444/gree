<?php

namespace Sprint\Migration;

/**
 * Iblock `footer_menu` — структура меню в футере. По образцу `menu`
 * (Version20260516000015):
 *
 *  - Корневые секции = заголовки колонок футера (UF_URL пустой, рендерится
 *    как заголовок без ссылки).
 *  - Вложенные секции = ссылки внутри колонки.
 *  - Элементы инфоблока не используются.
 *
 * UF-поля секции:
 *   UF_LABEL_RU — текст по-русски (обязательное)
 *   UF_LABEL_UZ — текст по-узбекски
 *   UF_URL      — URL пункта; пусто = заголовок колонки.
 */
class Version20260603000006 extends Version
{
    protected $description = "Iblock 'footer_menu' + UF-поля секций";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Меню футера',
            'CODE'            => 'footer_menu',
            'API_CODE'        => 'FooterMenu',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'content',
            'SORT'            => 60,
        ]);

        $helper->Iblock()->saveIblockFields($iblockId, [
            'CODE'        => [
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

        $entityId = 'IBLOCK_' . $iblockId . '_SECTION';

        $helper->UserTypeEntity()->addUserTypeEntitiesIfNotExists($entityId, [
            [
                'FIELD_NAME'        => 'UF_LABEL_RU',
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'Y',
                'SHOW_FILTER'       => 'E',
                'SORT'              => 100,
                'EDIT_FORM_LABEL'   => ['ru' => 'Название (RU)'],
                'LIST_COLUMN_LABEL' => ['ru' => 'RU'],
            ],
            [
                'FIELD_NAME'        => 'UF_LABEL_UZ',
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'N',
                'SORT'              => 110,
                'EDIT_FORM_LABEL'   => ['ru' => 'Название (UZ)'],
                'LIST_COLUMN_LABEL' => ['ru' => 'UZ'],
            ],
            [
                'FIELD_NAME'        => 'UF_URL',
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'N',
                'SORT'              => 120,
                'EDIT_FORM_LABEL'   => ['ru' => 'URL'],
                'LIST_COLUMN_LABEL' => ['ru' => 'URL'],
                'SETTINGS'          => ['SIZE' => 60],
            ],
        ]);

        $this->outSuccess('Iblock footer_menu id=%d + UF-поля секций', $iblockId);
    }

    public function down(): void
    {
        $this->getHelperManager()->Iblock()->deleteIblockIfExists('footer_menu');
        $this->outSuccess('Iblock footer_menu удалён');
    }
}
