<?php

namespace Sprint\Migration;

/**
 * Удаляет свойство GALLERY из iblock `products`. На уровне товара остаётся
 * только PREVIEW_PICTURE (используется в карточке каталога и корзине). Полная
 * галерея фото живёт на торговом предложении (`products_offers.GALLERY`,
 * см. Version20260610000001) и меняется при выборе цвета/мощности на детальной.
 *
 * Удаление безопасно: ProductDto/ProductRepository больше не читают GALLERY,
 * на странице каталога/корзины используется только image (PREVIEW_PICTURE).
 *
 * Идемпотентно: deletePropertyIfExists — no-op если уже нет.
 */
class Version20260610000003 extends Version
{
    protected $description = "products.GALLERY → удалить (галерея только на ТП)";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$productsId) {
            $this->outError('Iblock products не найден');
            return;
        }

        $helper->Iblock()->deletePropertyIfExists($productsId, 'GALLERY');

        \CIBlock::clearIblockTagCache($productsId);
        \Bitrix\Iblock\IblockTable::cleanCache();

        $this->outSuccess('products.GALLERY удалено');
    }

    public function down(): void
    {
        $this->outSuccess('Откат: вернуть GALLERY на products не имеет смысла — DTO и репо его не читают');
    }
}
