<?php

namespace Sprint\Migration;

/**
 * Iblock `products_offers`: добавляем свойство GALLERY (file, multiple) —
 * галерея ТП. На детальной товара слайдер картинок теперь привязан к
 * выбранному торговому предложению (offer), а не к самому товару — при смене
 * цвета/мощности картинки в карусели меняются.
 *
 * Старая галерея на продукте (`products.GALLERY`) остаётся как fallback,
 * если у ТП своя пуста.
 *
 * Идемпотентно: saveProperty по CODE.
 */
class Version20260610000001 extends Version
{
    protected $description = "products_offers: GALLERY (file, multiple) — галерея торгового предложения";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $offersId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$offersId) {
            $this->outError('Iblock products_offers не найден');
            return;
        }

        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'Галерея ТП',
            'CODE'          => 'GALLERY',
            'PROPERTY_TYPE' => 'F',
            'MULTIPLE'      => 'Y',
            'SORT'          => '650',
            'HINT'          => 'Картинки этого торгового предложения. На детальной товара показываются в слайдере при выборе соответствующего цвета/мощности.',
            'FILE_TYPE'     => 'jpg, jpeg, gif, png, webp',
        ]);

        \CIBlock::clearIblockTagCache($offersId);
        \Bitrix\Iblock\IblockTable::cleanCache();

        $this->outSuccess('products_offers.GALLERY добавлено');
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $offersId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if ($offersId) {
            $helper->Iblock()->deletePropertyIfExists($offersId, 'GALLERY');
        }
        $this->outSuccess('products_offers.GALLERY удалено');
    }
}
