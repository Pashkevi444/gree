<?php

namespace Sprint\Migration;

/**
 * Переводит флаги модели `BESTSELLER` и `INVERTER_MOTOR` из текстовых
 * Y/N-полей в нормальные чекбоксы (L-список из одного значения «Да»,
 * LIST_TYPE=C → checkbox в админке).
 *
 * Все существующие товары получают обе галочки — для тестового стенда
 * это удобно: все «бестселлеры», у всех «инвертор». В продакшене
 * редактор будет сам управлять чекбоксами в админке.
 *
 * Зависит от Version20260516000008 (паттерн чекбокса IN_STOCK).
 */
class Version20260516000010 extends Version
{
    protected $description = "BESTSELLER и INVERTER_MOTOR → checkbox, заполнить всем Y";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$productsId) {
            $this->outError('Iblock products не найден');
            return;
        }

        foreach (['BESTSELLER', 'INVERTER_MOTOR'] as $code) {
            $label = $code === 'BESTSELLER' ? 'Бестселлер' : 'Инверторный';

            $helper->Iblock()->deletePropertyIfExists($productsId, $code);
            $helper->Iblock()->saveProperty($productsId, [
                'NAME'          => $label,
                'CODE'          => $code,
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE'     => 'C',
                'MULTIPLE'      => 'N',
                'SORT'          => $code === 'BESTSELLER' ? '400' : '500',
                'VALUES'        => [
                    ['VALUE' => 'Да', 'XML_ID' => 'Y', 'DEF' => 'N', 'SORT' => '10'],
                ],
            ]);
            $this->out('  %s пересоздано как чекбокс', $code);
        }

        $bestEnumId = $this->resolveEnumValueId($productsId, 'BESTSELLER', 'Y');
        $invEnumId  = $this->resolveEnumValueId($productsId, 'INVERTER_MOTOR', 'Y');

        if (!$bestEnumId || !$invEnumId) {
            $this->outError('Не получилось получить enum_value_id для чекбоксов');
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($productsId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->exec();

        $touched = 0;
        while ($row = $rows->fetch()) {
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $productsId, [
                'BESTSELLER'     => $bestEnumId,
                'INVERTER_MOTOR' => $invEnumId,
            ]);
            $touched++;
        }
        $this->outSuccess('Обновлено товаров: %d (Y/Y)', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
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
