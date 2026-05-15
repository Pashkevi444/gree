<?php

namespace Sprint\Migration;

/**
 * Чистим инфоблок `products` от свойств, которые после Phase A живут только
 * на уровне торговых предложений. И заодно переводим IN_STOCK на offers
 * в нормальный чекбокс.
 *
 * НА `products` УДАЛЯЮТСЯ:
 *   PRICE, AREA, COLORS, IN_STOCK                         (core offer attrs)
 *   COOLING_POWER_RU/EN, HEATING_POWER_RU/EN,             (capacity-dependent specs)
 *   NOISE_RU/EN, INDOOR_DIMENSIONS_RU/EN,
 *   OUTDOOR_DIMENSIONS_RU/EN, INDOOR_WEIGHT_RU/EN,
 *   OUTDOOR_WEIGHT_RU/EN
 *
 * НА `products` ОСТАЮТСЯ:
 *   NAME_RU/EN, PREVIEW_TEXT_RU/EN, DETAIL_TEXT_RU/EN     (model description)
 *   TYPE, BESTSELLER, INVERTER_MOTOR                       (model markers)
 *   PREVIEW_PICTURE, GALLERY                               (model media)
 *   SKU, MODEL, ENERGY_CLASS, REFRIGERANT                  (model-wide identifiers)
 *   WARRANTY_TEXT_RU/EN, KIT_TEXT_RU/EN, INSTALLATION_TEXT_RU/EN, FUNCTIONS
 *
 * НА `products_offers`:
 *   IN_STOCK пересоздаётся как L-список из одного значения «Да»
 *   (XML_ID=Y, LIST_TYPE=C → чекбокс в админке).
 *   Все существующие offers получают IN_STOCK=Y (после Phase A они приехали
 *   из products либо со значением 'Y', либо пустыми).
 *
 * ВАЖНО: эту миграцию катить ПОСЛЕ выкатки кода Phase B+C+D (ProductRepository
 * теперь читает PRICE/AREA/COLORS из offers, а не из products). Если применить
 * до выкатки кода — каталог упадёт.
 */
class Version20260516000008 extends Version
{
    protected $description = "Чистка products от offer-полей + IN_STOCK как checkbox";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        $offersId   = $helper->Iblock()->getIblockIdIfExists('products_offers');

        // ─── Drop duplicates from products ───────────────────────────────
        if ($productsId) {
            $codes = [
                'PRICE', 'AREA', 'COLORS', 'IN_STOCK',
                'COOLING_POWER_RU', 'COOLING_POWER_EN',
                'HEATING_POWER_RU', 'HEATING_POWER_EN',
                'NOISE_RU', 'NOISE_EN',
                'INDOOR_DIMENSIONS_RU', 'INDOOR_DIMENSIONS_EN',
                'OUTDOOR_DIMENSIONS_RU', 'OUTDOOR_DIMENSIONS_EN',
                'INDOOR_WEIGHT_RU', 'INDOOR_WEIGHT_EN',
                'OUTDOOR_WEIGHT_RU', 'OUTDOOR_WEIGHT_EN',
            ];
            foreach ($codes as $code) {
                $helper->Iblock()->deletePropertyIfExists($productsId, $code);
            }
            $this->out('products: удалено offer-полей — %d', count($codes));
        }

        if (!$offersId) {
            $this->outError('Iblock products_offers не найден');
            return;
        }

        // ─── Recreate IN_STOCK on offers as checkbox ─────────────────────
        $helper->Iblock()->deletePropertyIfExists($offersId, 'IN_STOCK');
        $helper->Iblock()->saveProperty($offersId, [
            'NAME'          => 'В наличии',
            'CODE'          => 'IN_STOCK',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE'     => 'C',                  // C = checkbox в админке, L = dropdown
            'MULTIPLE'      => 'N',
            'SORT'          => '500',
            'VALUES'        => [
                ['VALUE' => 'Да', 'XML_ID' => 'Y', 'DEF' => 'N', 'SORT' => '10'],
            ],
        ]);

        $enumValueId = $this->resolveEnumValueId($offersId, 'IN_STOCK', 'Y');
        if (!$enumValueId) {
            $this->outError('Не получилось получить enum_value_id для IN_STOCK=Y');
            return;
        }

        // Backfill: default «в наличии» = Y для всех существующих offers.
        $entity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->exec();
        $touched = 0;
        while ($row = $rows->fetch()) {
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $offersId, [
                'IN_STOCK' => $enumValueId,
            ]);
            $touched++;
        }
        $this->outSuccess('IN_STOCK как чекбокс. Проставлено у %d offers', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется (потеря данных)');
    }

    private function resolveEnumValueId(int $iblockId, string $propertyCode, string $xmlId): ?int
    {
        $row = \CIBlockProperty::GetList([], [
            'IBLOCK_ID' => $iblockId,
            'CODE'      => $propertyCode,
        ])->Fetch();
        if (!$row) {
            return null;
        }

        $enum = \CIBlockPropertyEnum::GetList([], [
            'PROPERTY_ID' => (int) $row['ID'],
            'XML_ID'      => $xmlId,
        ])->Fetch();
        return $enum ? (int) $enum['ID'] : null;
    }
}
