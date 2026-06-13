<?php

namespace Sprint\Migration;

/**
 * SEO-запись для страницы /contacts/ (HL «Seo» / page_code='contacts'), RU + UZ.
 * + UI-переводы для секций / кнопки «Показать на карте» / breadcrumb.
 */
class Version20260603000011 extends Version
{
    protected $description = "Contacts: SEO + UI-переводы (HL Seo + HL Translations)";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $this->seedSeo();
        $this->seedTranslations();
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    private function seedSeo(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Seo');
        if (!$hlblockId) {
            $this->outError('HL «Seo» не найден');
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $exists = $dataClass::query()->where('UF_PAGE_CODE', 'contacts')->setSelect(['ID'])->exec()->fetch();
        $fields = [
            'UF_PAGE_CODE'         => 'contacts',
            'UF_TITLE_RU'          => 'Контакты Gree Узбекистан — офис, сервис, магазины',
            'UF_TITLE_UZ'          => 'Gree O\'zbekiston kontaktlari — ofis, servis, do\'konlar',
            'UF_DESCRIPTION_RU'    => 'Адреса фирменных магазинов Gree в Ташкенте, телефон call-центра +998 71 500 00 00, телеграм для заказов @Gree_5, email mygree.uz@mail.ru, сервисный центр и часы работы.',
            'UF_DESCRIPTION_UZ'    => 'Toshkentdagi Gree firma do\'konlari manzillari, call-markaz telefoni +998 71 500 00 00, buyurtmalar uchun telegram @Gree_5, email mygree.uz@mail.ru, servis markazi va ish vaqti.',
            'UF_KEYWORDS_RU'       => 'gree, контакты, адреса, телефон, телеграм, ташкент, узбекистан, сервис',
            'UF_KEYWORDS_UZ'       => 'gree, kontaktlar, manzillar, telefon, telegram, toshkent, o\'zbekiston, servis',
            'UF_OG_TITLE_RU'       => 'Контакты Gree Узбекистан',
            'UF_OG_TITLE_UZ'       => 'Gree O\'zbekiston kontaktlari',
            'UF_OG_DESCRIPTION_RU' => 'Адреса, телефоны и часы работы фирменных магазинов и сервисного центра Gree.',
            'UF_OG_DESCRIPTION_UZ' => 'Gree firma do\'konlari va servis markazi manzillari, telefonlari va ish vaqti.',
            'UF_OG_IMAGE'          => '',
        ];

        if ($exists) {
            $dataClass::update((int) $exists['ID'], $fields);
            $this->outSuccess('SEO «contacts» обновлён');
        } else {
            $dataClass::add($fields);
            $this->outSuccess('SEO «contacts» добавлен');
        }
    }

    private function seedTranslations(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL «Translations» не найден');
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $existing = [];
        foreach ($dataClass::query()->setSelect(['UF_CODE'])->exec() as $row) {
            $existing[] = (string) ($row['UF_CODE'] ?? '');
        }

        $entries = [
            'breadcrumbs.contacts'              => ['ru' => 'Контакты',          'uz' => 'Kontaktlar'],
            'contacts.section.channels.title'   => ['ru' => 'Как с нами связаться', 'uz' => 'Biz bilan qanday bog\'lanish'],
            'contacts.section.addresses.title'  => ['ru' => 'Адреса',            'uz' => 'Manzillar'],
            'contacts.button.show_map'          => ['ru' => 'Показать на карте', 'uz' => 'Xaritada ko\'rsatish'],
        ];

        $added = 0;
        foreach ($entries as $code => $values) {
            if (in_array($code, $existing, true)) {
                continue;
            }
            $helper->Hlblock()->addElement($hlblockId, [
                'UF_CODE'     => $code,
                'UF_VALUE_RU' => $values['ru'],
                'UF_VALUE_UZ' => $values['uz'],
            ]);
            $added++;
        }
        $this->outSuccess('Переводы contacts: +%d / %d', $added, count($entries));
    }
}
