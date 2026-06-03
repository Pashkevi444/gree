<?php

namespace Sprint\Migration;

/**
 * SEO + UI-переводы для /where-to-buy/.
 * SEO page_code='where-to-buy' (с дефисом, как в URL).
 */
class Version20260604000004 extends Version
{
    protected $description = "Where-to-buy: SEO + UI-переводы";

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

        $exists = $dataClass::query()->where('UF_PAGE_CODE', 'where-to-buy')->setSelect(['ID'])->exec()->fetch();
        $fields = [
            'UF_PAGE_CODE'         => 'where-to-buy',
            'UF_TITLE_RU'          => 'Где купить кондиционеры Gree в Узбекистане — магазины, партнёры, маркетплейсы',
            'UF_TITLE_UZ'          => 'O\'zbekistonda Gree konditsionerlarini qayerdan sotib olish — do\'konlar, hamkorlar, marketpleyslar',
            'UF_DESCRIPTION_RU'    => 'Адреса фирменных шоурумов Gree в Ташкенте, наши партнёры и сети магазинов-партнёров. Бесплатная доставка по Ташкенту, рассрочка ANORBANK и UZUM.',
            'UF_DESCRIPTION_UZ'    => 'Toshkentdagi Gree firma showroomlari manzillari, hamkorlarimiz va hamkor-do\'konlar tarmoqlari. Toshkent bo\'ylab bepul yetkazib berish, ANORBANK va UZUM bo\'lib to\'lash.',
            'UF_KEYWORDS_RU'       => 'gree, где купить, магазины, шоурум, партнёры, ташкент, узбекистан, дилеры',
            'UF_KEYWORDS_UZ'       => 'gree, qayerdan sotib olish, do\'konlar, showroom, hamkorlar, toshkent, o\'zbekiston, dilerlar',
            'UF_OG_TITLE_RU'       => 'Где купить Gree в Узбекистане',
            'UF_OG_TITLE_UZ'       => 'O\'zbekistonda Gree-ni qayerdan sotib olish',
            'UF_OG_DESCRIPTION_RU' => 'Фирменные шоурумы Gree в Ташкенте и сети магазинов-партнёров по Узбекистану.',
            'UF_OG_DESCRIPTION_UZ' => 'Toshkentdagi Gree firma showroomlari va O\'zbekiston bo\'ylab hamkor-do\'konlar tarmoqlari.',
            'UF_OG_IMAGE'          => '',
        ];

        if ($exists) {
            $dataClass::update((int) $exists['ID'], $fields);
            $this->outSuccess('SEO «where-to-buy» обновлён');
        } else {
            $dataClass::add($fields);
            $this->outSuccess('SEO «where-to-buy» добавлен');
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
            'breadcrumbs.where_to_buy' => ['ru' => 'Где купить', 'uz' => 'Qayerdan sotib olish'],

            'where.section.locations.title' => ['ru' => 'Где купить', 'uz' => 'Qayerdan sotib olish'],
            'where.section.locations.description' => [
                'ru' => 'Вы можете приобрести кондиционеры Gree в наших шоурумах, в магазинах-партнёрах, в дилерских центрах и на маркетплейсах.',
                'uz' => 'Gree konditsionerlarini bizning showroomlarimizda, hamkor-do\'konlarda, diler markazlarida va marketpleyslarda sotib olishingiz mumkin.',
            ],
            'where.section.partners.title' => ['ru' => 'Наши партнёры',                  'uz' => 'Bizning hamkorlarimiz'],
            'where.section.chains.title'   => ['ru' => 'Сети магазинов-партнёров',     'uz' => 'Hamkor-do\'konlar tarmoqlari'],
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
        $this->outSuccess('Переводы where-to-buy: +%d / %d', $added, count($entries));
    }
}
