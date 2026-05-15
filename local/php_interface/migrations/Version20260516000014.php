<?php

namespace Sprint\Migration;

/**
 * Галерея товара: 5 копий → 10. Swiper в product.js лочит навигацию,
 * когда количество слайдов помещается в видимую область целиком —
 * у эталона (dist/product.html) в галерее 10 картинок, поэтому стрелки
 * активные. У нас было 5, Swiper добавлял `swiper-button-disabled
 * swiper-button-lock` и кнопки переставали кликаться.
 *
 * Записываем 10 копий продуктовой картинки — SetPropertyValuesEx с multi-F
 * полностью заменяет содержимое.
 */
class Version20260516000014 extends Version
{
    protected $description = "Галерея товара 10 картинок (был 5) — Swiper-nav разлок";

    private const GALLERY_SIZE = 10;

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$productsId) {
            $this->outError('Iblock products не найден');
            return;
        }

        $imagePath = __DIR__ . '/images/product.png';
        if (!is_file($imagePath)) {
            $this->outError('Не найдена картинка: %s', $imagePath);
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($productsId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID', 'CODE'])->exec();

        $touched = 0;
        while ($row = $rows->fetch()) {
            // Build fresh array of file-arrays — every call to MakeFileArray
            // returns a new tmp_name reference, so they get stored as distinct
            // file_ids.
            $files = [];
            for ($i = 0; $i < self::GALLERY_SIZE; $i++) {
                $files[] = \CFile::MakeFileArray($imagePath);
            }

            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $productsId, [
                'GALLERY' => $files,
            ]);
            $touched++;
        }

        $this->outSuccess('Галерея переписана у %d товаров (по %d картинок)', $touched, self::GALLERY_SIZE);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
