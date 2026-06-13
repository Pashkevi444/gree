<?php

namespace Sprint\Migration;

/**
 * SEO-запись для страницы /help/ в HL «Seo» (page_code = 'help').
 * RU + UZ, как для всех остальных статических страниц.
 */
class Version20260603000004 extends Version
{
    protected $description = "SEO записи: help";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Seo');
        if (!$hlblockId) {
            $this->outError('HL «Seo» не найден');
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $exists = $dataClass::query()->where('UF_PAGE_CODE', 'help')->setSelect(['ID'])->exec()->fetch();
        $fields = [
            'UF_PAGE_CODE'         => 'help',
            'UF_TITLE_RU'          => 'Помощь — Gree Узбекистан: оплата, доставка, обмен и возврат',
            'UF_TITLE_UZ'          => 'Yordam — Gree O\'zbekiston: to\'lov, yetkazib berish, almashtirish va qaytarish',
            'UF_DESCRIPTION_RU'    => 'Способы оплаты (Humo, Uzcard, Visa, MasterCard, рассрочка ANORBANK/UZUM), бесплатная доставка по Ташкенту, правила обмена и возврата, единый сервисный центр Gree по Узбекистану.',
            'UF_DESCRIPTION_UZ'    => 'To\'lov usullari (Humo, Uzcard, Visa, MasterCard, ANORBANK/UZUM bo\'lib to\'lash), Toshkent bo\'ylab bepul yetkazib berish, almashtirish va qaytarish qoidalari, O\'zbekiston bo\'ylab yagona Gree servis markazi.',
            'UF_KEYWORDS_RU'       => 'gree, помощь, оплата, доставка, обмен, возврат, сервис, гарантия, ташкент, узбекистан',
            'UF_KEYWORDS_UZ'       => 'gree, yordam, to\'lov, yetkazib berish, almashtirish, qaytarish, servis, kafolat, toshkent, o\'zbekiston',
            'UF_OG_TITLE_RU'       => 'Помощь — Gree Узбекистан',
            'UF_OG_TITLE_UZ'       => 'Yordam — Gree O\'zbekiston',
            'UF_OG_DESCRIPTION_RU' => 'Оплата, доставка, обмен, возврат и сервисное обслуживание Gree.',
            'UF_OG_DESCRIPTION_UZ' => 'Gree mahsulotlarini to\'lash, yetkazib berish, almashtirish, qaytarish va servis xizmati.',
            'UF_OG_IMAGE'          => '',
        ];

        if ($exists) {
            $dataClass::update((int) $exists['ID'], $fields);
            $this->outSuccess('SEO «help» обновлён');
        } else {
            $dataClass::add($fields);
            $this->outSuccess('SEO «help» добавлен');
        }
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
