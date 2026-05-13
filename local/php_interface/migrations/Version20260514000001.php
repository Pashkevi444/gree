<?php

namespace Sprint\Migration;

class Version20260514000001 extends Version
{
    protected $description = "Создание инфоблоков: бренды, товары, блог";

    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID'   => 'catalog',
            'LANG' => [
                'ru' => ['NAME' => 'Каталог',  'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Товары'],
                'en' => ['NAME' => 'Catalog',  'SECTION_NAME' => 'Sections', 'ELEMENT_NAME' => 'Elements'],
            ],
        ]);

        $helper->Iblock()->saveIblockType([
            'ID'   => 'content',
            'LANG' => [
                'ru' => ['NAME' => 'Контент', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
                'en' => ['NAME' => 'Content', 'SECTION_NAME' => 'Sections', 'ELEMENT_NAME' => 'Elements'],
            ],
        ]);

        // ---- Бренды ----
        $brandsId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Бренды',
            'CODE'            => 'brands',
            'API_CODE'        => 'Brands',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'catalog',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/brand/#ELEMENT_CODE#/',
            'SORT'            => 100,
        ]);

        $helper->Iblock()->saveIblockFields($brandsId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);

        $this->outSuccess('Инфоблок "Бренды" id=%d', $brandsId);

        // ---- Товары ----
        $productsId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Товары',
            'CODE'            => 'products',
            'API_CODE'        => 'Products',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'catalog',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/catalog/#ELEMENT_CODE#/',
            'SORT'            => 200,
        ]);

        $helper->Iblock()->saveIblockFields($productsId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Тип',
            'CODE'          => 'TYPE',
            'PROPERTY_TYPE' => 'L',
            'SORT'          => '100',
            'IS_REQUIRED'   => 'Y',
            'VALUES'        => [
                ['VALUE' => 'Настенный',    'XML_ID' => 'wall',       'DEF' => 'N', 'SORT' => '10'],
                ['VALUE' => 'Колонный',     'XML_ID' => 'column',     'DEF' => 'N', 'SORT' => '20'],
                ['VALUE' => 'Промышленный', 'XML_ID' => 'industrial', 'DEF' => 'N', 'SORT' => '30'],
            ],
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Цена (руб.)',
            'CODE'          => 'PRICE',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => '200',
            'IS_REQUIRED'   => 'Y',
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Площадь (кв.м)',
            'CODE'          => 'AREA',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => '300',
            'IS_REQUIRED'   => 'Y',
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Бестселлер',
            'CODE'          => 'BESTSELLER',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => '400',
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Инверторный',
            'CODE'          => 'INVERTER_MOTOR',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => '500',
        ]);

        $helper->Iblock()->saveProperty($productsId, [
            'NAME'          => 'Цвета',
            'CODE'          => 'COLORS',
            'PROPERTY_TYPE' => 'L',
            'MULTIPLE'      => 'Y',
            'SORT'          => '600',
            'VALUES'        => [
                ['VALUE' => 'Белый',       'XML_ID' => 'white',     'DEF' => 'N', 'SORT' => '10'],
                ['VALUE' => 'Серебристый', 'XML_ID' => 'silver',    'DEF' => 'N', 'SORT' => '20'],
                ['VALUE' => 'Чёрный',      'XML_ID' => 'black',     'DEF' => 'N', 'SORT' => '30'],
                ['VALUE' => 'Шампань',     'XML_ID' => 'champagne', 'DEF' => 'N', 'SORT' => '40'],
            ],
        ]);

        $this->outSuccess('Инфоблок "Товары" id=%d', $productsId);

        // ---- Блог ----
        $blogId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Блог',
            'CODE'            => 'blog',
            'API_CODE'        => 'Blog',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'content',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/blog/#ELEMENT_CODE#/',
            'SORT'            => 300,
        ]);

        $helper->Iblock()->saveIblockFields($blogId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);

        $helper->Iblock()->saveProperty($blogId, [
            'NAME'          => 'Время чтения (мин)',
            'CODE'          => 'READING_TIME',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => '100',
        ]);

        $this->outSuccess('Инфоблок "Блог" id=%d', $blogId);
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $helper->Iblock()->deleteIblockIfExists('blog');
        $this->outSuccess('Инфоблок "Блог" удалён');

        $helper->Iblock()->deleteIblockIfExists('products');
        $this->outSuccess('Инфоблок "Товары" удалён');

        $helper->Iblock()->deleteIblockIfExists('brands');
        $this->outSuccess('Инфоблок "Бренды" удалён');
    }
}
