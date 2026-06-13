<?php

namespace Sprint\Migration;

/**
 * Сидинг тестовой галереи для всех ТП (products_offers) — чтобы на детальной
 * товара переключение цвета/мощности было визуально заметным: слайдер должен
 * перерисовываться с разными картинками.
 *
 * Для каждого ТП проставляется 3 картинки из общего пула dist/images, со
 * сдвигом по индексу ТП → у каждого свой уникальный набор. На проде после
 * загрузки реальных фото эту миграцию можно не накатывать (она безопасно
 * перепишет тестовые данные, но реальный контент лучше через админку).
 *
 * Идемпотентно: при повторном запуске старая галерея ТП заменяется.
 */
class Version20260610000002 extends Version
{
    protected $description = "products_offers.GALLERY — тестовые фото (по 3 на ТП, разные у каждого)";

    /** @var string[] Имена файлов в dist/images/ — пул для seed */
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

    /**
     * 9 = размер пула, у каждого ТП — все картинки, но со сдвигом по индексу,
     * чтобы первый кадр у разных ТП отличался (видно эффект переключения).
     * Меньшие значения (3-4) работают визуально, но фронт-Swiper thumbs стоит
     * на slidesPerView=5 — при <5 фото thumbs не отображаются нормально.
     */
    private const PER_OFFER = 9;

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

        $entity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->setOrder(['ID' => 'ASC'])->exec();

        $touched = 0;
        $offerIdx = 0;
        $poolSize = count($available);
        while ($row = $rows->fetch()) {
            // Для каждого ТП — 3 разных файла со сдвигом по индексу:
            //   offer #0 → pool[0..2], offer #1 → pool[1..3] и т.д.
            $files = [];
            for ($i = 0; $i < self::PER_OFFER; $i++) {
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

        $this->outSuccess('GALLERY проставлена для %d ТП (по %d фото, сдвиг по индексу)', $touched, self::PER_OFFER);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — это сид-данные');
    }
}
