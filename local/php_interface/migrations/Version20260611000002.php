<?php

namespace Sprint\Migration;

/**
 * Разводит телефоны каналов office / service-center. До этого оба канала имели
 * один номер (+998 71 500 00 00, сид Version20260604000014), и подвал, который
 * собирает телефоны как array_unique([office, service-center]), показывал
 * одну строку вместо двух.
 *
 * Номер сервис-центра тестовый — реальный проставить в админке
 * (Контент → Контакты: каналы → service-center → Телефон).
 *
 * Идемпотентно: SetPropertyValuesEx перезаписывает значение.
 */
class Version20260611000002 extends Version
{
    protected $description = "contacts_channels: отдельный телефон для service-center (в подвале два номера)";

    private const PHONES = [
        'office'         => '+998 71 500 00 00',
        'service-center' => '+998 71 500 00 01',
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('contacts_channels');
        if (!$iblockId) {
            $this->outError('iblock contacts_channels не найден');
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $touched = 0;
        foreach (self::PHONES as $code => $phone) {
            $row = $entity::query()->where('CODE', $code)->setSelect(['ID'])->exec()->fetch();
            if (!$row) {
                $this->out('  %s: элемент не найден', $code);
                continue;
            }
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, ['PHONE' => $phone]);
            $touched++;
        }

        \CIBlock::clearIblockTagCache($iblockId);
        \Bitrix\Iblock\IblockTable::cleanCache();

        $this->outSuccess('Телефоны обновлены для %d каналов', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат контентной правки не требуется');
    }
}
