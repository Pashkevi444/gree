<?php

namespace Sprint\Migration;

/**
 * Iblock `menu` — структура хедер-меню сайта.
 *
 *  - Корневые секции (без родителя) = пункты главного меню (Каталог, О бренде,
 *    Помощь, Где купить, Партнёрам, Контакты).
 *  - Вложенные секции = пункты выпадающего popup-меню.
 *  - Сами элементы инфоблока не используются — структура полностью в секциях.
 *
 * UF-поля секции:
 *   UF_LABEL_RU — текст пункта по-русски (обязательное)
 *   UF_LABEL_EN — текст пункта по-английски
 *   UF_URL      — куда ведёт ссылка. Пусто → пункт рендерится как button
 *                 (например главная «Каталог»), и его поведение определяется
 *                 наличием детей-secций (popup-меню).
 */
class Version20260516000015 extends Version
{
    protected $description = "Iblock 'menu' + UF поля на секциях";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Меню сайта',
            'CODE'            => 'menu',
            'API_CODE'        => 'Menu',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'content',
            'SORT'            => 50,
        ]);

        // Sections=Y чтобы можно было строить дерево, элементы не нужны.
        $helper->Iblock()->saveIblockFields($iblockId, [
            'CODE'        => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'], 'IS_REQUIRED' => 'N'],
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
                'EDIT_FORM_LABEL'   => ['ru' => 'Название (RU)', 'en' => 'Label (RU)'],
                'LIST_COLUMN_LABEL' => ['ru' => 'RU', 'en' => 'RU'],
            ],
            [
                'FIELD_NAME'        => 'UF_LABEL_EN',
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'N',
                'SORT'              => 110,
                'EDIT_FORM_LABEL'   => ['ru' => 'Название (EN)', 'en' => 'Label (EN)'],
                'LIST_COLUMN_LABEL' => ['ru' => 'EN', 'en' => 'EN'],
            ],
            [
                'FIELD_NAME'        => 'UF_URL',
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'N',
                'SORT'              => 120,
                'EDIT_FORM_LABEL'   => ['ru' => 'URL', 'en' => 'URL'],
                'LIST_COLUMN_LABEL' => ['ru' => 'URL', 'en' => 'URL'],
                'SETTINGS'          => ['SIZE' => 60],
            ],
        ]);

        $this->outSuccess('Iblock menu id=%d + UF-поля секций', $iblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('menu');
        $this->outSuccess('Iblock menu удалён');
    }
}
