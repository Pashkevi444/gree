<?php

namespace Sprint\Migration;

/**
 * Фикс значений COLOR на торговых предложениях.
 *
 * Семя Version20260516000007 записывало в L-property `COLOR` строки
 * ('white', 'silver' и т.д.) — а Bitrix L-property хранит enum_value_id
 * (int). В результате COLOR.ITEM.XML_ID возвращал null, и репозиторий
 * не мог собрать список цветов модели для деталки.
 *
 * Эта миграция:
 *   1. Строит map XML_ID → enum_value_id для свойства COLOR offers.
 *   2. Для каждого offer вытаскивает цвет из CODE (`gree-bora-x-07-silver`
 *      → `silver`) и записывает правильный enum_value_id.
 */
class Version20260516000013 extends Version
{
    protected $description = "Фикс COLOR на offers (xml_id → enum_value_id)";

    private const COLOR_XML_IDS = ['white', 'silver', 'black', 'champagne'];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $offersId = $helper->Iblock()->getIblockIdIfExists('products_offers');
        if (!$offersId) {
            $this->outError('Iblock products_offers не найден');
            return;
        }

        $colorPropRow = \CIBlockProperty::GetList([], [
            'IBLOCK_ID' => $offersId,
            'CODE'      => 'COLOR',
        ])->Fetch();
        if (!$colorPropRow) {
            $this->outError('Свойство COLOR не найдено');
            return;
        }

        $enumMap = []; // xml_id → enum_value_id
        $enums = \CIBlockPropertyEnum::GetList([], ['PROPERTY_ID' => (int) $colorPropRow['ID']]);
        while ($enum = $enums->Fetch()) {
            $enumMap[(string) $enum['XML_ID']] = (int) $enum['ID'];
        }
        $this->out('Enum map: ' . json_encode($enumMap, JSON_UNESCAPED_UNICODE));

        $entity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID', 'CODE'])->exec();

        $fixed = 0;
        $missed = 0;
        while ($row = $rows->fetch()) {
            $code = (string) ($row['CODE'] ?? '');
            $color = null;
            foreach (self::COLOR_XML_IDS as $candidate) {
                if (str_ends_with($code, '-' . $candidate)) {
                    $color = $candidate;
                    break;
                }
            }

            if ($color === null) {
                $this->out("  offer #{$row['ID']} ({$code}): не удалось распарсить цвет");
                $missed++;
                continue;
            }
            if (!isset($enumMap[$color])) {
                $this->out("  offer #{$row['ID']} ({$code}): нет enum '$color' в COLOR");
                $missed++;
                continue;
            }

            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $offersId, [
                'COLOR' => $enumMap[$color],
            ]);
            $fixed++;
        }

        $this->outSuccess('Исправлено offers: %d, пропущено: %d', $fixed, $missed);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
