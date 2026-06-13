<?php

namespace Sprint\Migration;

/**
 * Пересидинг тестовой галереи ТП: теперь по 9 фото на каждый ТП (полный пул
 * dist/images со сдвигом по индексу). Старый сид (Version20260610000002) ставил
 * по 3, но фронт-Swiper thumbs стоит на `slidesPerView: 5` — при <5 слайдах
 * thumbs-карусель некорректно рендерится (пустой dock с навигацией без миниатюр).
 *
 * Идемпотентно: при повторном запуске GALLERY перезаписывается.
 */
class Version20260610000004 extends Version
{
    protected $description = "products_offers.GALLERY — пересид по 9 фото на ТП (фикс пустых thumbs)";

    /** @var string[] */
    private array $pool = [
        '11bf4d324216e9be17bf9d02e78c6f0c13831844.png',
        '44d2ccf920c9c9588cb63caa626344e8a61a4e42.png',
        '7a7e2502abf5ebb4684a96f8bcfc153339a5c70f.png',
        '6311bac5bcac86eed9564a7ad5501eda4bbfd3a0.png',
        '9d6a1af34169ceecd352ce6bf8955f4460d32bd6.png',
        '507c01b60af6db2dc4ebf3bd0c8e3dc6832d04d2.png',
        '1034d62d090ccaaef63ee76e1c210a928268f4b0.png',
        '5e957da7f90fa21f6480e94fedfe68e07675086a.png',
        '3497d38d38093f77160aa8e08fc68c105426682c.png',
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $offersId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$offersId) {
            $this->outError('Iblock products_offers не найден');
            return;
        }

        $imagesDir = $_SERVER['DOCUMENT_ROOT'] . '/dist/images';
        $available = [];
        foreach ($this->pool as $name) {
            $path = $imagesDir . '/' . $name;
            if (is_file($path)) {
                $available[] = $path;
            }
        }
        if (!$available) {
            $this->outError('В %s нет ни одного файла из пула', $imagesDir);
            return;
        }
        $poolSize = count($available);

        $entity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->setOrder(['ID' => 'ASC'])->exec();

        $touched = 0;
        $offerIdx = 0;
        while ($row = $rows->fetch()) {
            // Полный пул со сдвигом — каждый ТП получает все картинки, но
            // первой идёт «своя» (offerIdx). Это даёт визуальную разницу
            // при переключении ТП и оставляет ≥5 фото для Swiper thumbs.
            $files = [];
            for ($i = 0; $i < $poolSize; $i++) {
                $files[] = \CFile::MakeFileArray($available[($offerIdx + $i) % $poolSize]);
            }
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $offersId, [
                'GALLERY' => $files,
            ]);
            $touched++;
            $offerIdx++;
        }

        \CIBlock::clearIblockTagCache($offersId);
        \Bitrix\Iblock\IblockTable::cleanCache();

        $this->outSuccess('GALLERY перезаписана: %d ТП × %d фото', $touched, $poolSize);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — это сид-данные');
    }
}
