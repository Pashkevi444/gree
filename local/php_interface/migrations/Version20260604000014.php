<?php

namespace Sprint\Migration;

/**
 * Добавляет свойство PHONE на iblock contacts_channels + заполняет его для
 * элементов где это применимо (office, service-center). Раньше телефон лежал
 * внутри DESCRIPTION_RU/_UZ строкой («с 9:00 до 18:00 +998 71 500 00 00») —
 * для шапки/футера сайта нужен чистый номер, чтобы вешать его в tel:-ссылку.
 *
 * Идемпотентно: saveProperty по CODE и SetPropertyValuesEx по идентификатору
 * элемента.
 */
class Version20260604000014 extends Version
{
    protected $description = "contacts_channels: PHONE prop + заполнение для office/service-center";

    /** @var array<string, string> element CODE → значение PHONE */
    private array $phones = [
        'office'         => '+998 71 500 00 00',
        'service-center' => '+998 71 500 00 00',
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

        $helper->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Телефон',
            'CODE'          => 'PHONE',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => '440',
            'HINT'          => 'Чистый номер в формате +998 71 500 00 00 — для tel:-ссылок в шапке/футере',
        ]);

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $touched = 0;
        foreach ($this->phones as $code => $phone) {
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

        $this->outSuccess('PHONE добавлено и заполнено для %d элементов', $touched);
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('contacts_channels');
        if ($iblockId) {
            $helper->Iblock()->deletePropertyIfExists($iblockId, 'PHONE');
        }
        $this->outSuccess('PHONE удалён');
    }
}
