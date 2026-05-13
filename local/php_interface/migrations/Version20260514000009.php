<?php

namespace Sprint\Migration;

class Version20260514000009 extends Version
{
    protected $description = "Инфоблоки страницы бренда: тип, структура, свойства";

    public function up()
    {
        $helper = $this->getHelperManager();

        // Тип инфоблоков
        $helper->Iblock()->saveIblockType([
            'ID'       => 'brand',
            'LANG'     => [
                'ru' => ['NAME' => 'Страница бренда', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
                'en' => ['NAME' => 'Brand page',      'SECTION_NAME' => 'Sections', 'ELEMENT_NAME' => 'Elements'],
            ],
        ]);
        $this->outSuccess('Тип инфоблоков "brand" создан');

        $this->createBrandHistory($helper);
        $this->createBrandWhyGree($helper);
        $this->createBrandGreeCards($helper);
        $this->createBrandGreeStats($helper);
        $this->createBrandAboutCards($helper);
        $this->createBrandTechnologies($helper);
    }

    private function createBrandHistory(HelperManager $helper): void
    {
        $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_history',
            'API_CODE'       => 'BrandHistory',
            'NAME'           => 'Бренд: История бренда',
            'ACTIVE'         => 'Y',
            'SORT'           => 100,
        ]);
        $this->outSuccess('Инфоблок BrandHistory создан');
    }

    private function createBrandWhyGree(HelperManager $helper): void
    {
        $iblockId = $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_why_gree',
            'API_CODE'       => 'BrandWhyGree',
            'NAME'           => 'Бренд: Почему выбирают Gree',
            'ACTIVE'         => 'Y',
            'SORT'           => 200,
        ]);

        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'BUTTON_TEXT',
            'NAME'          => 'Текст кнопки',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => 100,
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'BUTTON_URL',
            'NAME'          => 'Ссылка кнопки',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => 200,
        ]);
        $this->outSuccess('Инфоблок BrandWhyGree создан');
    }

    private function createBrandGreeCards(HelperManager $helper): void
    {
        $iblockId = $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_gree_cards',
            'API_CODE'       => 'BrandGreeCards',
            'NAME'           => 'Бренд: Карточки преимуществ',
            'ACTIVE'         => 'Y',
            'SORT'           => 300,
        ]);

        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'ICON_CODE',
            'NAME'          => 'Код иконки',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => 100,
        ]);
        $this->outSuccess('Инфоблок BrandGreeCards создан');
    }

    private function createBrandGreeStats(HelperManager $helper): void
    {
        $iblockId = $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_gree_stats',
            'API_CODE'       => 'BrandGreeStats',
            'NAME'           => 'Бренд: Статистика (4 колонки)',
            'ACTIVE'         => 'Y',
            'SORT'           => 400,
        ]);

        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'NUMBER_PREFIX',
            'NAME'          => 'Префикс числа',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => 100,
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'NUMBER_VALUE',
            'NAME'          => 'Число',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => 200,
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE'          => 'NUMBER_SUFFIX',
            'NAME'          => 'Суффикс числа',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => 300,
        ]);
        $this->outSuccess('Инфоблок BrandGreeStats создан');
    }

    private function createBrandAboutCards(HelperManager $helper): void
    {
        $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_about_cards',
            'API_CODE'       => 'BrandAboutCards',
            'NAME'           => 'Бренд: О компании (карточки)',
            'ACTIVE'         => 'Y',
            'SORT'           => 500,
        ]);
        $this->outSuccess('Инфоблок BrandAboutCards создан');
    }

    private function createBrandTechnologies(HelperManager $helper): void
    {
        $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'brand',
            'LID'            => ['s1'],
            'CODE'           => 'brand_technologies',
            'API_CODE'       => 'BrandTechnologies',
            'NAME'           => 'Бренд: Технологии',
            'ACTIVE'         => 'Y',
            'SORT'           => 600,
        ]);
        $this->outSuccess('Инфоблок BrandTechnologies создан');
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        foreach (['brand_history', 'brand_why_gree', 'brand_gree_cards', 'brand_gree_stats', 'brand_about_cards', 'brand_technologies'] as $code) {
            $helper->Iblock()->deleteIblockIfExists($code);
            $this->out('Инфоблок "%s" удалён', $code);
        }

        $helper->Iblock()->deleteIblockTypeIfExists('brand');
        $this->outSuccess('Тип инфоблоков "brand" удалён');
    }
}
