<?php

namespace Sprint\Migration;

/**
 * Сиды страницы /where-to-buy/ (RU+UZ + фото + координаты).
 * Картинки из dist/images/. Координаты — те же что у contacts_addresses
 * (адреса физически одни и те же — наши фирменные точки).
 */
class Version20260604000003 extends Version
{
    protected $description = "Сиды where_to_buy: locations + partners + chains";

    private string $imagesDir;

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $this->imagesDir = __DIR__ . '/../../../dist/images';

        $this->seedLocations();
        $this->seedPartners();
        $this->seedChains();

        $this->outSuccess('Where-to-buy контент засеяны');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется (данные уйдут с iblock\'ами)');
    }

    private function seedLocations(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('where_to_buy_locations');
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
        $this->out('  locations: %d', count($items));
    }

    private function seedPartners(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('where_to_buy_partners');
        if (!$iblockId) {
            return;
        }
        $items = [
            ['code' => 'partner-1', 'image' => '4ae7ef2340902a838c212ce792301334f2ed7050.png', 'name_ru' => 'Партнёр 1', 'name_uz' => 'Hamkor 1'],
            ['code' => 'partner-2', 'image' => 'b558ec8593dd61ff1f27f609fd7d5f04bd5163b3.png', 'name_ru' => 'Партнёр 2', 'name_uz' => 'Hamkor 2'],
            ['code' => 'partner-3', 'image' => 'fa75610a5d1a1f878934a1bd91ed0a2d069a7a4b.png', 'name_ru' => 'Партнёр 3', 'name_uz' => 'Hamkor 3'],
            ['code' => 'partner-4', 'image' => '39d4ff76f6739ab1ba05f11c88ad890a3855f7f8.png', 'name_ru' => 'Партнёр 4', 'name_uz' => 'Hamkor 4'],
            ['code' => 'partner-5', 'image' => '21c7fb47650eed8f89a75015fd2b1b3b650d8edb.png', 'name_ru' => 'Партнёр 5', 'name_uz' => 'Hamkor 5'],
        ];
        $sort = 100;
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $sort,
            ], [
                'NAME_RU' => $it['name_ru'],
                'NAME_UZ' => $it['name_uz'],
                'IMAGE'   => $this->fileArray($it['image']),
            ]);
            $sort += 100;
        }
        $this->out('  partners: %d', count($items));
    }

    private function seedChains(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('where_to_buy_chains');
        if (!$iblockId) {
            return;
        }
        $items = [
            ['code' => 'chain-1', 'image' => 'b384171476421fd1d3a9d81858e654fe18b64c7b.png', 'name_ru' => 'Сеть магазинов 1', 'name_uz' => 'Do\'konlar tarmog\'i 1'],
            ['code' => 'chain-2', 'image' => 'd2ef4a0a2f6cd4b8ae6fca43920676b83b7ca2f6.png', 'name_ru' => 'Сеть магазинов 2', 'name_uz' => 'Do\'konlar tarmog\'i 2'],
            ['code' => 'chain-3', 'image' => '6e77d107d1aeae667f2775726743400eb9dea195.png', 'name_ru' => 'Сеть магазинов 3', 'name_uz' => 'Do\'konlar tarmog\'i 3'],
            ['code' => 'chain-4', 'image' => '8ac429aecd98acecccb357e2a594b5c32d5a3e54.png', 'name_ru' => 'Сеть магазинов 4', 'name_uz' => 'Do\'konlar tarmog\'i 4'],
        ];
        $sort = 100;
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $sort,
            ], [
                'NAME_RU' => $it['name_ru'],
                'NAME_UZ' => $it['name_uz'],
                'IMAGE'   => $this->fileArray($it['image']),
            ]);
            $sort += 100;
        }
        $this->out('  chains: %d', count($items));
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
