<?php

namespace Sprint\Migration;

/**
 * Инфоблоки главной страницы:
 *   home_slider        — слайдер hero
 *   home_gree_cards    — карточки «Почему выбирают Gree»
 *   home_gree_stats    — числовая статистика Gree
 *   home_app_features  — фичи приложения GREE+
 *   home_technologies  — технологии для вашего комфорта
 */
class Version20260514000005 extends Version
{
    protected $description = "Структура инфоблоков главной страницы";

    public function up()
    {
        $helper = $this->getHelperManager();

        // ---- Тип: Главная ----
        $helper->Iblock()->saveIblockType([
            'ID'   => 'home',
            'LANG' => [
                'ru' => ['NAME' => 'Главная', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
                'en' => ['NAME' => 'Home',    'SECTION_NAME' => 'Sections', 'ELEMENT_NAME' => 'Elements'],
            ],
        ]);

        // ---- Слайдер ----
        $sliderId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Слайдер (главная)',
            'CODE'           => 'home_slider',
            'API_CODE'       => 'HomeSlider',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'home',
            'SORT'           => 100,
        ]);
        $helper->Iblock()->saveIblockFields($sliderId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);
        $helper->Iblock()->saveProperty($sliderId, [
            'NAME' => 'Описание (HTML)',  'CODE' => 'SUBTITLE',     'PROPERTY_TYPE' => 'S', 'SORT' => '100', 'ROW_COUNT' => '3',
        ]);
        $helper->Iblock()->saveProperty($sliderId, [
            'NAME' => 'Текст кнопки',     'CODE' => 'BUTTON_TEXT',  'PROPERTY_TYPE' => 'S', 'SORT' => '200',
        ]);
        $helper->Iblock()->saveProperty($sliderId, [
            'NAME' => 'URL кнопки',       'CODE' => 'BUTTON_URL',   'PROPERTY_TYPE' => 'S', 'SORT' => '300',
        ]);
        $this->outSuccess('Инфоблок "Слайдер" id=%d', $sliderId);

        // ---- Карточки «Почему Gree» ----
        $greeCardsId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Карточки «Почему Gree» (главная)',
            'CODE'           => 'home_gree_cards',
            'API_CODE'       => 'HomeGreeCards',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'home',
            'SORT'           => 200,
        ]);
        $helper->Iblock()->saveIblockFields($greeCardsId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);
        $helper->Iblock()->saveProperty($greeCardsId, [
            'NAME' => 'Код иконки', 'CODE' => 'ICON_CODE', 'PROPERTY_TYPE' => 'S', 'SORT' => '100',
        ]);
        $this->outSuccess('Инфоблок "Карточки Gree" id=%d', $greeCardsId);

        // ---- Статистика Gree ----
        $greeStatsId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Статистика Gree (главная)',
            'CODE'           => 'home_gree_stats',
            'API_CODE'       => 'HomeGreeStats',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'home',
            'SORT'           => 300,
        ]);
        $helper->Iblock()->saveIblockFields($greeStatsId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);
        $helper->Iblock()->saveProperty($greeStatsId, [
            'NAME' => 'Префикс числа',  'CODE' => 'NUMBER_PREFIX', 'PROPERTY_TYPE' => 'S', 'SORT' => '100',
        ]);
        $helper->Iblock()->saveProperty($greeStatsId, [
            'NAME' => 'Число',          'CODE' => 'NUMBER_VALUE',  'PROPERTY_TYPE' => 'N', 'SORT' => '200',
        ]);
        $helper->Iblock()->saveProperty($greeStatsId, [
            'NAME' => 'Суффикс числа',  'CODE' => 'NUMBER_SUFFIX', 'PROPERTY_TYPE' => 'S', 'SORT' => '300',
        ]);
        $this->outSuccess('Инфоблок "Статистика Gree" id=%d', $greeStatsId);

        // ---- Фичи приложения ----
        $appFeaturesId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Фичи приложения GREE+ (главная)',
            'CODE'           => 'home_app_features',
            'API_CODE'       => 'HomeAppFeatures',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'home',
            'SORT'           => 400,
        ]);
        $helper->Iblock()->saveIblockFields($appFeaturesId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);
        $helper->Iblock()->saveProperty($appFeaturesId, [
            'NAME' => 'Код иконки', 'CODE' => 'ICON_CODE', 'PROPERTY_TYPE' => 'S', 'SORT' => '100',
        ]);
        $this->outSuccess('Инфоблок "Фичи приложения" id=%d', $appFeaturesId);

        // ---- Технологии ----
        $technologiesId = $helper->Iblock()->saveIblock([
            'NAME'           => 'Технологии (главная)',
            'CODE'           => 'home_technologies',
            'API_CODE'       => 'HomeTechnologies',
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'home',
            'SORT'           => 500,
        ]);
        $helper->Iblock()->saveIblockFields($technologiesId, [
            'CODE' => ['DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'UNIQUE' => 'Y']],
        ]);
        // Картинка — стандартное поле PREVIEW_PICTURE, дополнительных свойств нет
        $this->outSuccess('Инфоблок "Технологии" id=%d', $technologiesId);
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        foreach (['home_technologies', 'home_app_features', 'home_gree_stats', 'home_gree_cards', 'home_slider'] as $code) {
            $helper->Iblock()->deleteIblockIfExists($code);
            $this->out('Инфоблок "%s" удалён', $code);
        }

        $this->outSuccess('Инфоблоки главной удалены');
    }
}
