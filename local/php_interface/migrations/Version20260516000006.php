<?php

namespace Sprint\Migration;

/**
 * Создаёт iblock `products_offers` — торговые предложения для модели Gree.
 *
 *   products (модель)  1 ──┐
 *                          ├── CML2_LINK ──> products_offers (offer)
 *                          │                    PRICE, AREA, COLOR, IN_STOCK
 *                          │                    + cooling/heating/noise/dimensions/weight
 *
 * У одной модели может быть несколько offers — по комбинациям «цвет × мощность».
 * Технические характеристики, зависящие от мощности (cooling/heating/noise/
 * dimensions/weight), живут на уровне offer'а и переводятся в _RU/_EN.
 *
 * Общие для модели атрибуты (NAME, описания, MODEL, ENERGY_CLASS, REFRIGERANT,
 * WARRANTY/KIT/INSTALLATION, FUNCTIONS) остаются на products.
 */
class Version20260516000006 extends Version
{
    protected $description = "Структура iblock products_offers";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$productsId) {
            $this->outError('Инфоблок products не найден');
            return;
        }

        $offersId = $helper->Iblock()->saveIblock([
            'NAME'            => 'Торговые предложения',
            'CODE'            => 'products_offers',
            'API_CODE'        => 'ProductsOffers',
            'LID'             => ['s1'],
            'IBLOCK_TYPE_ID'  => 'catalog',
            'SORT'            => 250,
        ]);

        $helper->Iblock()->saveIblockFields($offersId, [
            'CODE'        => [
                'DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'],
                'IS_REQUIRED' => 'N',
            ],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);

        // ── Link to parent product ────────────────────────────────────────
        $helper->Iblock()->saveProperty($offersId, [
            'NAME'           => 'Товар',
            'CODE'           => 'CML2_LINK',
            'PROPERTY_TYPE'  => 'E',
            'LINK_IBLOCK_ID' => $productsId,
            'IS_REQUIRED'    => 'Y',
            'SORT'           => '100',
        ]);

        // ── Core offer attrs ──────────────────────────────────────────────
        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'Цена',
            'CODE'          => 'PRICE',
            'PROPERTY_TYPE' => 'N',
            'IS_REQUIRED'   => 'Y',
            'SORT'          => '200',
        ]);

        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'Площадь применения (м²)',
            'CODE'          => 'AREA',
            'PROPERTY_TYPE' => 'N',
            'IS_REQUIRED'   => 'Y',
            'SORT'          => '300',
        ]);

        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'Цвет',
            'CODE'          => 'COLOR',
            'PROPERTY_TYPE' => 'L',
            'MULTIPLE'      => 'N',
            'IS_REQUIRED'   => 'Y',
            'SORT'          => '400',
            'VALUES'        => [
                ['VALUE' => 'Белый',       'XML_ID' => 'white',     'DEF' => 'N', 'SORT' => '10'],
                ['VALUE' => 'Серебристый', 'XML_ID' => 'silver',    'DEF' => 'N', 'SORT' => '20'],
                ['VALUE' => 'Чёрный',      'XML_ID' => 'black',     'DEF' => 'N', 'SORT' => '30'],
                ['VALUE' => 'Шампань',     'XML_ID' => 'champagne', 'DEF' => 'N', 'SORT' => '40'],
            ],
        ]);

        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'В наличии (Y/N)',
            'CODE'          => 'IN_STOCK',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => '500',
        ]);

        // ── Specs that vary by capacity (per-offer, paired RU/EN) ─────────
        $sort = 600;
        foreach ([
            'COOLING_POWER'      => ['label' => 'Мощность охлаждения',          'rows' => 1],
            'HEATING_POWER'      => ['label' => 'Мощность обогрева',            'rows' => 1],
            'NOISE'              => ['label' => 'Уровень шума',                 'rows' => 1],
            'INDOOR_DIMENSIONS'  => ['label' => 'Габариты внутреннего блока',    'rows' => 1],
            'OUTDOOR_DIMENSIONS' => ['label' => 'Габариты наружного блока',      'rows' => 1],
            'INDOOR_WEIGHT'      => ['label' => 'Вес внутреннего блока',         'rows' => 1],
            'OUTDOOR_WEIGHT'     => ['label' => 'Вес наружного блока',           'rows' => 1],
        ] as $suffix => $meta) {
            foreach (['RU', 'EN'] as $lang) {
                $helper->Iblock()->saveProperty($offersId, [
                    'NAME'          => sprintf('%s (%s)', $meta['label'], $lang),
                    'CODE'          => $suffix . '_' . $lang,
                    'PROPERTY_TYPE' => 'S',
                    'ROW_COUNT'     => (string) ($meta['rows'] ?? 1),
                    'SORT'          => (string) $sort,
                ]);
                $sort += 10;
            }
        }

        $this->outSuccess('Iblock products_offers id=%d создан', $offersId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('products_offers');
        $this->outSuccess('Iblock products_offers удалён');
    }
}
