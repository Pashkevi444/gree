<?php

namespace Sprint\Migration;

/**
 * Blog: подменю шапки brand-news/brand-tips переводим на якорные URL
 * /blog/#news / /blog/#advice (раньше оба вели просто на /blog/).
 * Плюс SEO-записи для page_code='blog-advice' / 'blog-news' (новые страницы
 * /blog/advice/ и /blog/news/, полные списки по категориям).
 *
 * Идемпотентна: проверяет CODE секции и UF_PAGE_CODE в HL Seo.
 */
class Version20260604000009 extends Version
{
    protected $description = "Blog: меню-якори + SEO для blog-advice/blog-news";

    /** @var array<string, string> menu section CODE → новый UF_URL */
    private array $menuUrls = [
        'brand-news' => '/blog/#news',
        'brand-tips' => '/blog/#advice',
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $this->fixMenuUrls();
        $this->seedSeo();
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    private function fixMenuUrls(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('menu');
        if (!$iblockId) {
            $this->outError('iblock menu не найден');
            return;
        }
        $sec = new \CIBlockSection();
        $updated = 0;
        foreach ($this->menuUrls as $code => $url) {
            $rs = \CIBlockSection::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $code], false, ['ID', 'UF_URL']);
            $row = $rs->Fetch();
            if (!$row) {
                $this->out('  menu %s: не найден', $code);
                continue;
            }
            if ((string) ($row['UF_URL'] ?? '') === $url) {
                continue;
            }
            $sec->Update((int) $row['ID'], ['UF_URL' => $url]);
            $updated++;
        }
        \CIBlock::clearIblockTagCache($iblockId);
        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Iblock\SectionTable::cleanCache();
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/iblock/');
        $this->outSuccess('Меню: обновлено URL %d', $updated);
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

        $records = [
            [
                'UF_PAGE_CODE'         => 'blog-advice',
                'UF_TITLE_RU'          => 'Полезные советы — Блог Gree Узбекистан',
                'UF_TITLE_UZ'          => 'Foydali maslahatlar — Gree O\'zbekiston blogi',
                'UF_DESCRIPTION_RU'    => 'Гайды по выбору, установке и обслуживанию кондиционеров Gree от наших инженеров.',
                'UF_DESCRIPTION_UZ'    => 'Muhandislarimizdan Gree konditsionerlarini tanlash, o\'rnatish va xizmat ko\'rsatish bo\'yicha qo\'llanmalar.',
                'UF_KEYWORDS_RU'       => 'gree, советы, гайды, кондиционер, установка, обслуживание',
                'UF_KEYWORDS_UZ'       => 'gree, maslahatlar, qo\'llanmalar, konditsioner, o\'rnatish, xizmat ko\'rsatish',
                'UF_OG_TITLE_RU'       => 'Полезные советы Gree',
                'UF_OG_TITLE_UZ'       => 'Gree foydali maslahatlar',
                'UF_OG_DESCRIPTION_RU' => 'Гайды и советы от экспертов Gree.',
                'UF_OG_DESCRIPTION_UZ' => 'Gree mutaxassislaridan qo\'llanmalar va maslahatlar.',
                'UF_OG_IMAGE'          => '',
            ],
            [
                'UF_PAGE_CODE'         => 'blog-news',
                'UF_TITLE_RU'          => 'Новости — Блог Gree Узбекистан',
                'UF_TITLE_UZ'          => 'Yangiliklar — Gree O\'zbekiston blogi',
                'UF_DESCRIPTION_RU'    => 'Новости Gree: новые модели, акции, открытие магазинов, мероприятия.',
                'UF_DESCRIPTION_UZ'    => 'Gree yangiliklari: yangi modellar, aksiyalar, do\'kon ochilishlari, tadbirlar.',
                'UF_KEYWORDS_RU'       => 'gree, новости, акции, события, узбекистан',
                'UF_KEYWORDS_UZ'       => 'gree, yangiliklar, aksiyalar, tadbirlar, o\'zbekiston',
                'UF_OG_TITLE_RU'       => 'Новости Gree',
                'UF_OG_TITLE_UZ'       => 'Gree yangiliklari',
                'UF_OG_DESCRIPTION_RU' => 'Свежие новости Gree Узбекистан.',
                'UF_OG_DESCRIPTION_UZ' => 'Gree O\'zbekiston so\'nggi yangiliklari.',
                'UF_OG_IMAGE'          => '',
            ],
        ];

        foreach ($records as $row) {
            $exists = $dataClass::query()->where('UF_PAGE_CODE', $row['UF_PAGE_CODE'])->setSelect(['ID'])->exec()->fetch();
            if ($exists) {
                $dataClass::update((int) $exists['ID'], $row);
            } else {
                $dataClass::add($row);
            }
        }
        $this->outSuccess('SEO blog-advice/blog-news сохранены');
    }
}
