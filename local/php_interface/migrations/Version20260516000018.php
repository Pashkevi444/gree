<?php

namespace Sprint\Migration;

/**
 * Пересев блога: удаляем все элементы и записываем 8 новых статей
 * (4 «Советы» + 4 «Новости») с PREVIEW_PICTURE и парами RU/EN.
 *
 * 8 = 3 + 3 + 2: первая порция по 3 карточки в каждой секции, после
 * клика «Показать ещё» докатываем оставшийся 1 → проверяем API.
 */
class Version20260516000018 extends Version
{
    protected $description = "Блог: пересев 8 статей с PREVIEW_PICTURE + RU/EN";

    /** @var array<int, array{code: string, image: string, sort: int, date: string, reading: int, category: string,
     *                  name_ru: string, name_en: string, preview_ru: string, preview_en: string,
     *                  detail_ru: string, detail_en: string}>
     */
    private array $articles = [
        // ── Советы ──────────────────────────────────────────────
        [
            'code'     => 'kak-vybrat-konditsioner',
            'image'    => '48de8d6ac080bfc7c9be1642b08ed2cf14105329.png',
            'sort'     => 100,
            'date'     => '14.05.2026',
            'reading'  => 10,
            'category' => 'tips',
            'name_ru'    => 'Как выбрать кондиционер для квартиры: полное руководство',
            'name_en'    => 'How to choose an AC for your apartment: a complete guide',
            'preview_ru' => 'Разбираемся, на что обращать внимание при выборе кондиционера: мощность, площадь помещения, тип установки и ключевые технологии.',
            'preview_en' => 'A breakdown of what to consider when choosing an air conditioner: power, room area, installation type, and key technologies.',
            'detail_ru'  => '<h2>Мощность и площадь</h2><p>Главный параметр при выборе — мощность охлаждения. Базовый расчёт: 1 кВт на 10 кв.м.</p>',
            'detail_en'  => '<h2>Power and area</h2><p>The main parameter is cooling power: about 1 kW per 10 sq.m of room.</p>',
        ],
        [
            'code'     => 'invertor-preimushhestva',
            'image'    => '1034d62d090ccaaef63ee76e1c210a928268f4b0.png',
            'sort'     => 200,
            'date'     => '10.05.2026',
            'reading'  => 7,
            'category' => 'tips',
            'name_ru'    => 'Инверторный кондиционер: в чём реальное преимущество',
            'name_en'    => 'Inverter air conditioner: the real advantage',
            'preview_ru' => 'Почему инверторные кондиционеры стали стандартом и стоит ли переплачивать за инвертор.',
            'preview_en' => 'Why inverter ACs have become the standard, and whether the price premium is worth it.',
            'detail_ru'  => '<h2>Как работает инвертор</h2><p>Инверторный плавно регулирует обороты компрессора — как педаль газа в автомобиле.</p>',
            'detail_en'  => '<h2>How the inverter works</h2><p>Inverter smoothly varies compressor speed — similar to a car throttle pedal.</p>',
        ],
        [
            'code'     => 'obsluzhivanie-konditsionera',
            'image'    => '3adb711bbffa7924dfe31cefd17d931439d4b069.png',
            'sort'     => 300,
            'date'     => '05.05.2026',
            'reading'  => 5,
            'category' => 'tips',
            'name_ru'    => 'Техническое обслуживание кондиционера: когда и как',
            'name_en'    => 'AC maintenance: when and how',
            'preview_ru' => 'Регулярное обслуживание продлевает срок службы кондиционера и сохраняет его эффективность.',
            'preview_en' => 'Regular maintenance extends AC lifespan and preserves efficiency.',
            'detail_ru'  => '<h2>Что делать самостоятельно</h2><p>Чистить фильтры каждые 2–4 недели.</p>',
            'detail_en'  => '<h2>DIY tasks</h2><p>Clean filters every 2–4 weeks.</p>',
        ],
        [
            'code'     => 'top-5-konditsionerov-2026',
            'image'    => '11bf4d324216e9be17bf9d02e78c6f0c13831844.png',
            'sort'     => 400,
            'date'     => '01.05.2026',
            'reading'  => 8,
            'category' => 'tips',
            'name_ru'    => 'Топ-5 кондиционеров 2026 года: наш выбор',
            'name_en'    => 'Top 5 air conditioners of 2026: our picks',
            'preview_ru' => 'Составили рейтинг лучших моделей 2026 года по соотношению цена/качество.',
            'preview_en' => 'A ranking of the best 2026 models by price/quality balance.',
            'detail_ru'  => '<h2>1. Gree BORA X 07</h2><p>Флагманская серия с самоочисткой.</p>',
            'detail_en'  => '<h2>1. Gree BORA X 07</h2><p>The flagship series with self-cleaning.</p>',
        ],
        // ── Новости ─────────────────────────────────────────────
        [
            'code'     => 'gree-novaya-liniya-invertor',
            'image'    => '7a7e2502abf5ebb4684a96f8bcfc153339a5c70f.png',
            'sort'     => 500,
            'date'     => '12.05.2026',
            'reading'  => 4,
            'category' => 'news',
            'name_ru'    => 'Gree расширяет линейку инверторных сплит-систем',
            'name_en'    => 'Gree expands its inverter split-system lineup',
            'preview_ru' => 'Представляем новые модели с улучшенной энергоэффективностью и тихой работой.',
            'preview_en' => 'Introducing new models with improved energy efficiency and quiet operation.',
            'detail_ru'  => '<p>В мае 2026 на рынке появилось 6 новых моделей инверторных кондиционеров Gree.</p>',
            'detail_en'  => '<p>May 2026 brings six new Gree inverter AC models to the market.</p>',
        ],
        [
            'code'     => 'gree-5-let-garantii',
            'image'    => '5e957da7f90fa21f6480e94fedfe68e07675086a.png',
            'sort'     => 600,
            'date'     => '08.05.2026',
            'reading'  => 3,
            'category' => 'news',
            'name_ru'    => '5 лет гарантии на ключевые компоненты',
            'name_en'    => '5-year warranty on key components',
            'preview_ru' => 'Мы уверены в качестве нашей техники и предоставляем расширенную гарантию.',
            'preview_en' => 'We are confident in our hardware and offer an extended warranty.',
            'detail_ru'  => '<p>Расширенная гарантия покрывает компрессор и инверторный модуль.</p>',
            'detail_en'  => '<p>The extended warranty covers the compressor and inverter board.</p>',
        ],
        [
            'code'     => 'gree-na-climate-world-2026',
            'image'    => '507c01b60af6db2dc4ebf3bd0c8e3dc6832d04d2.png',
            'sort'     => 700,
            'date'     => '03.05.2026',
            'reading'  => 5,
            'category' => 'news',
            'name_ru'    => 'Gree на выставке Climate World 2026',
            'name_en'    => 'Gree at Climate World 2026',
            'preview_ru' => 'Подводим итоги участия в международной выставке климатического оборудования.',
            'preview_en' => 'A recap of our participation in the international HVAC trade show.',
            'detail_ru'  => '<p>Стенд Gree собрал тысячи посетителей и десятки контрактов.</p>',
            'detail_en'  => '<p>The Gree booth attracted thousands of visitors and dozens of contracts.</p>',
        ],
        [
            'code'     => 'gree-otkrytie-magazina-tashkent',
            'image'    => '44d2ccf920c9c9588cb63caa626344e8a61a4e42.png',
            'sort'     => 800,
            'date'     => '28.04.2026',
            'reading'  => 2,
            'category' => 'news',
            'name_ru'    => 'Открытие фирменного магазина в Ташкенте',
            'name_en'    => 'Flagship store opening in Tashkent',
            'preview_ru' => 'Первый фирменный магазин Gree в Узбекистане — теперь рядом с вами.',
            'preview_en' => 'The first official Gree store in Uzbekistan — now close to you.',
            'detail_ru'  => '<p>Адрес магазина: Ташкент, проспект Амира Темура, 1.</p>',
            'detail_en'  => '<p>Store address: Tashkent, Amir Temur Avenue, 1.</p>',
        ],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('blog');
        if (!$iblockId) {
            $this->outError('Iblock blog не найден');
            return;
        }

        // 1) Снести все существующие элементы
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->exec();
        $deleted = 0;
        while ($row = $rows->fetch()) {
            if (\CIBlockElement::Delete((int) $row['ID'])) {
                $deleted++;
            }
        }
        $this->out('Удалено старых элементов: %d', $deleted);

        // 2) Засеять новые
        $imagesDir = __DIR__ . '/images/';
        foreach ($this->articles as $a) {
            $imagePath = $imagesDir . $a['image'];
            if (!is_file($imagePath)) {
                $imagePath = __DIR__ . '/../../../dist/images/' . $a['image'];
            }

            $previewPicture = is_file($imagePath) ? \CFile::MakeFileArray($imagePath) : null;
            if (!$previewPicture) {
                $this->out('  WARN: картинка не найдена для %s (%s)', $a['code'], $a['image']);
            }

            $fields = [
                'NAME'             => $a['name_ru'],
                'CODE'             => $a['code'],
                'ACTIVE'           => 'Y',
                'SORT'             => $a['sort'],
                'DATE_ACTIVE_FROM' => $a['date'],
                'PREVIEW_TEXT'     => $a['preview_ru'],
                'DETAIL_TEXT'      => $a['detail_ru'],
            ];
            if ($previewPicture) {
                $fields['PREVIEW_PICTURE'] = $previewPicture;
            }

            $props = [
                'CATEGORY'        => ['VALUE' => $a['category']],
                'READING_TIME'    => $a['reading'],
                'NAME_RU'         => $a['name_ru'],
                'NAME_EN'         => $a['name_en'],
                'PREVIEW_TEXT_RU' => $a['preview_ru'],
                'PREVIEW_TEXT_EN' => $a['preview_en'],
                'DETAIL_TEXT_RU'  => $a['detail_ru'],
                'DETAIL_TEXT_EN'  => $a['detail_en'],
            ];

            $id = $helper->Iblock()->saveElement($iblockId, $fields, $props);

            // CATEGORY как L-property — saveElement не всегда пишет enum.ID.
            // Подстрахуемся: явно проставим через SetPropertyValuesEx с XML_ID.
            $enumRow = \CIBlockPropertyEnum::GetList([], [
                'IBLOCK_ID' => $iblockId,
                'CODE'      => 'CATEGORY',
                'XML_ID'    => $a['category'],
            ])->Fetch();
            if ($enumRow) {
                \CIBlockElement::SetPropertyValuesEx($id, $iblockId, [
                    'CATEGORY' => (int) $enumRow['ID'],
                ]);
            }

            $this->out('  + [%d] %s (%s)', $id, $a['code'], $a['category']);
        }

        $this->outSuccess('Блог пересеян: %d статей', count($this->articles));
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('blog');
        if (!$iblockId) {
            return;
        }
        foreach ($this->articles as $a) {
            $helper->Iblock()->deleteElementIfExists($iblockId, $a['code']);
        }
        $this->outSuccess('Новые статьи блога удалены');
    }
}
