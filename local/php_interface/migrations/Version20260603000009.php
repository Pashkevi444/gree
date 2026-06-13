<?php

namespace Sprint\Migration;

/**
 * Тип инфоблока «contacts» + 2 iblock внутри:
 *
 *   contacts_channels  — карточки «Как с нами связаться» (telegram/офис/сервис/email)
 *     NAME_RU/UZ, DESCRIPTION_RU/UZ, BUTTON_LABEL_RU/UZ,
 *     BUTTON_URL (string, '' = клик скроллит на карту в футере),
 *     ICON_CODE (telegram/office/tool/mail),
 *     LATITUDE/LONGITUDE (string, нужны если BUTTON_URL пуст)
 *
 *   contacts_addresses — карточки физических точек
 *     NAME_RU/UZ (адрес), SCHEDULE_RU/UZ, PHONES (multi-string),
 *     IMAGE (file), LATITUDE/LONGITUDE
 *
 * Координаты на карточке используются JS-обработчиком футер-iframe карты:
 * клик «Показать на карте» → scroll к карте + iframe.src = yandex map-widget
 * URL с этими lat/lon.
 */
class Version20260603000009 extends Version
{
    protected $description = "Тип iblock 'contacts' + iblock channels/addresses";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID'        => 'contacts',
            'SECTIONS'  => 'N',
            'IN_RSS'    => 'N',
            'SORT'      => 700,
            'LANG'      => [
                'ru' => ['NAME' => 'Контакты', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
            ],
        ]);

        $this->createChannels();
        $this->createAddresses();

        $this->outSuccess('Тип «contacts» и 2 iblock\'а созданы');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('contacts_channels');
        $helper->Iblock()->deleteIblockIfExists('contacts_addresses');
        try {
            $helper->Iblock()->deleteIblockTypeIfExists('contacts');
        } catch (\Throwable) {
            // helper может ругаться если внутри типа остались чужие iblock'и
        }
        $this->outSuccess('Iblock\'и контактов удалены');
    }

    private function createChannels(): void
    {
        $id = $this->makeIblock('contacts_channels', 'ContactsChannels', 'Каналы связи', 100);

        $this->addLocalized($id, 'NAME', 'Название', 100);
        $this->addLocalized($id, 'DESCRIPTION', 'Описание', 200, 3);
        $this->addLocalized($id, 'BUTTON_LABEL', 'Текст кнопки', 300);

        $this->addPlain($id, 'BUTTON_URL', 'URL кнопки (пусто = скролл к карте)', 400);
        $this->addPlain($id, 'ICON_CODE',  'Код иконки (telegram/office/tool/mail)', 410);
        $this->addPlain($id, 'LATITUDE',   'Широта (для карты)', 420);
        $this->addPlain($id, 'LONGITUDE',  'Долгота (для карты)', 430);
    }

    private function createAddresses(): void
    {
        $id = $this->makeIblock('contacts_addresses', 'ContactsAddresses', 'Адреса', 200);

        $this->addLocalized($id, 'NAME', 'Адрес', 100);
        $this->addLocalized($id, 'SCHEDULE', 'Расписание', 200, 2);

        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Телефоны',
            'CODE'          => 'PHONES',
            'PROPERTY_TYPE' => 'S',
            'MULTIPLE'      => 'Y',
            'SORT'          => '300',
            'HINT'          => 'По одному номеру на значение, например +998 71 500 00 00',
        ]);

        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Фото',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '400',
        ]);

        $this->addPlain($id, 'LATITUDE',  'Широта',  500);
        $this->addPlain($id, 'LONGITUDE', 'Долгота', 510);
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function makeIblock(string $code, string $apiCode, string $name, int $sort): int
    {
        $helper = $this->getHelperManager();
        $id = $helper->Iblock()->saveIblock([
            'NAME'           => $name,
            'CODE'           => $code,
            'API_CODE'       => $apiCode,
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'contacts',
            'SORT'           => $sort,
        ]);
        $helper->Iblock()->saveIblockFields($id, [
            'CODE' => [
                'DEFAULT_VALUE' => [
                    'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L',
                    'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y',
                ],
                'IS_REQUIRED' => 'N',
            ],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);
        return (int) $id;
    }

    private function addLocalized(int $iblockId, string $base, string $label, int $sortBase, int $rows = 1): void
    {
        $helper = $this->getHelperManager();
        foreach (['RU', 'UZ'] as $i => $lang) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME'          => sprintf('%s (%s)', $label, $lang),
                'CODE'          => $base . '_' . $lang,
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT'     => (string) $rows,
                'SORT'          => (string) ($sortBase + $i * 10),
            ]);
        }
    }

    private function addPlain(int $iblockId, string $code, string $name, int $sort): void
    {
        $this->getHelperManager()->Iblock()->saveProperty($iblockId, [
            'NAME'          => $name,
            'CODE'          => $code,
            'PROPERTY_TYPE' => 'S',
            'SORT'          => (string) $sort,
        ]);
    }
}
