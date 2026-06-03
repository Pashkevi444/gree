<?php

namespace Sprint\Migration;

/**
 * Сиды контента для contacts_channels + contacts_addresses
 * (структура — Version20260603000009).
 *
 * Координаты — примерные центры локаций по точкам Ташкента, поправит
 * контент-менеджер из админки. Картинки берём из dist/images/.
 */
class Version20260603000010 extends Version
{
    protected $description = "Сиды contacts: каналы + адреса (RU+UZ + фото + координаты)";

    private string $imagesDir;

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $this->imagesDir = __DIR__ . '/../../../dist/images';

        $this->seedChannels();
        $this->seedAddresses();

        $this->outSuccess('Контакты засеяны');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется (данные уйдут с iblock\'ами)');
    }

    private function seedChannels(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('contacts_channels');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'orders-telegram', 'sort' => 100, 'icon' => 'telegram',
                'name_ru'    => 'Приём заказов',
                'name_uz'    => 'Buyurtmalar qabuli',
                'desc_ru'    => '@Gree_5',
                'desc_uz'    => '@Gree_5',
                'btn_ru'     => 'Написать в телеграм',
                'btn_uz'     => 'Telegramda yozish',
                'button_url' => 'https://t.me/Gree_5',
                'lat' => '', 'lon' => '',
            ],
            [
                'code' => 'office', 'sort' => 200, 'icon' => 'office',
                'name_ru'    => 'Наш офис',
                'name_uz'    => 'Bizning ofis',
                'desc_ru'    => 'с 9:00 до 18:00 +998 71 500 00 00',
                'desc_uz'    => '9:00 dan 18:00 gacha +998 71 500 00 00',
                'btn_ru'     => 'Показать на карте',
                'btn_uz'     => 'Xaritada ko\'rsatish',
                'button_url' => '',
                'lat' => '41.29047', 'lon' => '69.23534',
            ],
            [
                'code' => 'service-center', 'sort' => 300, 'icon' => 'tool',
                'name_ru'    => 'Сервисный центр',
                'name_uz'    => 'Servis markazi',
                'desc_ru'    => '+998 71 500 00 00',
                'desc_uz'    => '+998 71 500 00 00',
                'btn_ru'     => 'Показать на карте',
                'btn_uz'     => 'Xaritada ko\'rsatish',
                'button_url' => '',
                'lat' => '41.30531', 'lon' => '69.27523',
            ],
            [
                'code' => 'email', 'sort' => 400, 'icon' => 'mail',
                'name_ru'    => 'Электронная почта',
                'name_uz'    => 'Elektron pochta',
                'desc_ru'    => 'mygree.uz@mail.ru',
                'desc_uz'    => 'mygree.uz@mail.ru',
                'btn_ru'     => 'Написать на почту',
                'btn_uz'     => 'Pochtaga yozish',
                'button_url' => 'mailto:mygree.uz@mail.ru',
                'lat' => '', 'lon' => '',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'         => $it['name_ru'],
                'NAME_UZ'         => $it['name_uz'],
                'DESCRIPTION_RU'  => $it['desc_ru'],
                'DESCRIPTION_UZ'  => $it['desc_uz'],
                'BUTTON_LABEL_RU' => $it['btn_ru'],
                'BUTTON_LABEL_UZ' => $it['btn_uz'],
                'BUTTON_URL'      => $it['button_url'],
                'ICON_CODE'       => $it['icon'],
                'LATITUDE'        => $it['lat'],
                'LONGITUDE'       => $it['lon'],
            ]);
        }
        $this->out('  channels: %d', count($items));
    }

    private function seedAddresses(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('contacts_addresses');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'mahtumkuli-119', 'sort' => 100,
                'image' => 'e9f9cd68149046c070d7118cac0316da93c63495.png',
                'name_ru' => 'Ташкент, Махтумкули, д. 119',
                'name_uz' => 'Toshkent, Maxtumquli, 119-uy',
                'schedule_ru' => 'Ежедневно с 09:00 до 18:00, перерыв с 13:00 до 14:00',
                'schedule_uz' => 'Har kuni 09:00 dan 18:00 gacha, tushlik 13:00 dan 14:00 gacha',
                'phones' => ['+998 71 500 00 00', '+998 91 809 09 09', '+998 95 700 10 07'],
                'lat' => '41.29047', 'lon' => '69.23534',
            ],
            [
                'code' => 'zulfiyaxonum-21', 'sort' => 200,
                'image' => '3497d38d38093f77160aa8e08fc68c105426682c.png',
                'name_ru' => 'Ташкент, Зульфияханум, д. 21',
                'name_uz' => 'Toshkent, Zulfiyaxonim, 21-uy',
                'schedule_ru' => 'пн-пт с 10:00 до 18:00, перерыв с 13:00 до 14:00',
                'schedule_uz' => 'du-ju 10:00 dan 18:00 gacha, tushlik 13:00 dan 14:00 gacha',
                'phones' => ['+998 974 72 07 07'],
                'lat' => '41.30531', 'lon' => '69.27523',
            ],
            [
                'code' => 'bunyodkor-156a-e137', 'sort' => 300,
                'image' => '6e936128b30f94df053188a1448d666d29afd640.png',
                'name_ru' => 'Ташкент, просп. Бунёдкор, 156А Абу Сахий Е-137',
                'name_uz' => 'Toshkent, Bunyodkor shoh ko\'chasi, 156A Abu Saxiy E-137',
                'schedule_ru' => 'Ежедневно с 09:00 до 18:00',
                'schedule_uz' => 'Har kuni 09:00 dan 18:00 gacha',
                'phones' => ['+998 974 72 07 07'],
                'lat' => '41.26284', 'lon' => '69.20762',
            ],
            [
                'code' => 'bunyodkor-156-1-s4', 'sort' => 400,
                'image' => '51a595ce02c454ef34a14ef3fbc13aae8fcc6b6c.png',
                'name_ru' => 'Ташкент, просп. Бунёдкор, 156/1 Абу Сахий S-4',
                'name_uz' => 'Toshkent, Bunyodkor shoh ko\'chasi, 156/1 Abu Saxiy S-4',
                'schedule_ru' => 'Ежедневно с 09:00 до 18:00',
                'schedule_uz' => 'Har kuni 09:00 dan 18:00 gacha',
                'phones' => ['+998 974 72 07 07'],
                'lat' => '41.26310', 'lon' => '69.20830',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'      => $it['name_ru'],
                'NAME_UZ'      => $it['name_uz'],
                'SCHEDULE_RU'  => $it['schedule_ru'],
                'SCHEDULE_UZ'  => $it['schedule_uz'],
                'PHONES'       => $it['phones'],
                'IMAGE'        => $this->fileArray($it['image']),
                'LATITUDE'     => $it['lat'],
                'LONGITUDE'    => $it['lon'],
            ]);
        }
        $this->out('  addresses: %d', count($items));
    }

    private function fileArray(string $filename): array
    {
        $path = $this->imagesDir . '/' . $filename;
        if (!is_file($path)) {
            $this->out('    WARN image not found: %s', $path);
            return [];
        }
        return \CFile::MakeFileArray($path);
    }
}
