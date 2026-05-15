<?php

namespace Sprint\Migration;

/**
 * Раскрывает существующие товары `products` в торговые предложения
 * `products_offers`. Один offer на каждый цвет товара. Берём текущие
 * значения PRICE, AREA, IN_STOCK, COLORS из товара + переносим spec'и
 * (cooling/heating/noise/dimensions/weight) которые сейчас живут на товаре.
 *
 * Старые поля на `products` пока не удаляем — чтобы каталог продолжал
 * работать до выкатки кода (Phase G сделает чистку).
 *
 * Зависит от Version20260516000006 (создание iblock offers).
 */
class Version20260516000007 extends Version
{
    protected $description = "Создание offers из существующих товаров";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        $offersId   = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$productsId || !$offersId) {
            $this->outError('Iblocks products / products_offers не найдены');
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($productsId)->getEntityDataClass();

        $result = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(array_merge(
                ['ID', 'NAME', 'CODE'],
                [
                    'PRICE_VALUE'        => 'PRICE.VALUE',
                    'AREA_VALUE'         => 'AREA.VALUE',
                    'IN_STOCK_VALUE'     => 'IN_STOCK.VALUE',
                    'COLOR_XML'          => 'COLORS.ITEM.XML_ID',
                    // specs (will be set on offers; same value for every color of the same model)
                    'COOLING_POWER_RU_VALUE'      => 'COOLING_POWER_RU.VALUE',
                    'COOLING_POWER_EN_VALUE'      => 'COOLING_POWER_EN.VALUE',
                    'HEATING_POWER_RU_VALUE'      => 'HEATING_POWER_RU.VALUE',
                    'HEATING_POWER_EN_VALUE'      => 'HEATING_POWER_EN.VALUE',
                    'NOISE_RU_VALUE'              => 'NOISE_RU.VALUE',
                    'NOISE_EN_VALUE'              => 'NOISE_EN.VALUE',
                    'INDOOR_DIMENSIONS_RU_VALUE'  => 'INDOOR_DIMENSIONS_RU.VALUE',
                    'INDOOR_DIMENSIONS_EN_VALUE'  => 'INDOOR_DIMENSIONS_EN.VALUE',
                    'OUTDOOR_DIMENSIONS_RU_VALUE' => 'OUTDOOR_DIMENSIONS_RU.VALUE',
                    'OUTDOOR_DIMENSIONS_EN_VALUE' => 'OUTDOOR_DIMENSIONS_EN.VALUE',
                    'INDOOR_WEIGHT_RU_VALUE'      => 'INDOOR_WEIGHT_RU.VALUE',
                    'INDOOR_WEIGHT_EN_VALUE'      => 'INDOOR_WEIGHT_EN.VALUE',
                    'OUTDOOR_WEIGHT_RU_VALUE'     => 'OUTDOOR_WEIGHT_RU.VALUE',
                    'OUTDOOR_WEIGHT_EN_VALUE'     => 'OUTDOOR_WEIGHT_EN.VALUE',
                ],
            ))
            ->exec();

        // Multi-value COLORS gives a separate row per color → group everything
        // back together so we can build one offer per (product × color).
        $byProduct = [];
        while ($row = $result->fetch()) {
            $pid = (int) $row['ID'];
            $color = (string) ($row['COLOR_XML'] ?? '');

            $byProduct[$pid]['name']  = (string) $row['NAME'];
            $byProduct[$pid]['code']  = (string) $row['CODE'];
            $byProduct[$pid]['price'] = (int) ($row['PRICE_VALUE'] ?? 0);
            $byProduct[$pid]['area']  = (int) ($row['AREA_VALUE'] ?? 0);
            $byProduct[$pid]['stock'] = (string) ($row['IN_STOCK_VALUE'] ?? '');
            $byProduct[$pid]['specs'] = [
                'COOLING_POWER_RU'      => (string) ($row['COOLING_POWER_RU_VALUE'] ?? ''),
                'COOLING_POWER_EN'      => (string) ($row['COOLING_POWER_EN_VALUE'] ?? ''),
                'HEATING_POWER_RU'      => (string) ($row['HEATING_POWER_RU_VALUE'] ?? ''),
                'HEATING_POWER_EN'      => (string) ($row['HEATING_POWER_EN_VALUE'] ?? ''),
                'NOISE_RU'              => (string) ($row['NOISE_RU_VALUE'] ?? ''),
                'NOISE_EN'              => (string) ($row['NOISE_EN_VALUE'] ?? ''),
                'INDOOR_DIMENSIONS_RU'  => (string) ($row['INDOOR_DIMENSIONS_RU_VALUE'] ?? ''),
                'INDOOR_DIMENSIONS_EN'  => (string) ($row['INDOOR_DIMENSIONS_EN_VALUE'] ?? ''),
                'OUTDOOR_DIMENSIONS_RU' => (string) ($row['OUTDOOR_DIMENSIONS_RU_VALUE'] ?? ''),
                'OUTDOOR_DIMENSIONS_EN' => (string) ($row['OUTDOOR_DIMENSIONS_EN_VALUE'] ?? ''),
                'INDOOR_WEIGHT_RU'      => (string) ($row['INDOOR_WEIGHT_RU_VALUE'] ?? ''),
                'INDOOR_WEIGHT_EN'      => (string) ($row['INDOOR_WEIGHT_EN_VALUE'] ?? ''),
                'OUTDOOR_WEIGHT_RU'     => (string) ($row['OUTDOOR_WEIGHT_RU_VALUE'] ?? ''),
                'OUTDOOR_WEIGHT_EN'     => (string) ($row['OUTDOOR_WEIGHT_EN_VALUE'] ?? ''),
            ];
            $byProduct[$pid]['colors'] ??= [];
            if ($color !== '' && !in_array($color, $byProduct[$pid]['colors'], true)) {
                $byProduct[$pid]['colors'][] = $color;
            }
        }

        $created = 0;
        foreach ($byProduct as $pid => $info) {
            $colors = $info['colors'] ?: ['white']; // fallback: single white offer if no colors
            foreach ($colors as $color) {
                $offerCode = $info['code'] . '-' . $color;
                $offerName = $info['name'] . ' / ' . $color;

                $fields = [
                    'NAME'   => $offerName,
                    'CODE'   => $offerCode,
                    'ACTIVE' => 'Y',
                    'SORT'   => 500,
                ];
                $props = [
                    'CML2_LINK' => $pid,
                    'PRICE'     => $info['price'],
                    'AREA'      => $info['area'],
                    'COLOR'     => $color,
                    'IN_STOCK'  => $info['stock'] !== '' ? $info['stock'] : 'Y',
                ];
                foreach ($info['specs'] as $code => $value) {
                    if ($value !== '') {
                        $props[$code] = $value;
                    }
                }

                $id = $helper->Iblock()->saveElement($offersId, $fields, $props);
                if ($id) {
                    $created++;
                }
            }
        }

        $this->outSuccess('Создано предложений: %d', $created);
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $offersId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$offersId) {
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->exec();

        $count = 0;
        while ($row = $rows->fetch()) {
            \CIBlockElement::Delete((int) $row['ID']);
            $count++;
        }

        $this->outSuccess('Удалено предложений: %d', $count);
    }
}
