<?php

namespace Sprint\Migration;

use Bitrix\Iblock\InheritedProperty\IblockTemplates;

/**
 * SEO-шаблоны инфоблоков (Bitrix IPROPERTY templates).
 *
 * Шаблоны живут в b_iblock_iblock_iprop и применяются к ЛЮБОМУ элементу
 * инфоблока (если у элемента нет персональных override). Подстановка
 * {=this.NAME} читает поле NAME элемента, {=this.PROPERTY.CODE} — свойство.
 *
 * Шаблоны заданы по-русски: служебное NAME элемента у нас всегда RU. EN-локаль
 * собирается в контроллере из NAME_EN свойства — IPROPERTY на это не способен
 * (Bitrix не знает про runtime-локаль). Этот слой обеспечивает sensible
 * default для админки + поисковиков на RU-версии.
 */
class Version20260517000006 extends Version
{
    protected $description = "IPROPERTY-шаблоны инфоблоков Products + Blog";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        $blogId = $helper->Iblock()->getIblockIdIfExists('blog');

        if (!$productsId || !$blogId) {
            $this->outError('Не найдены iblock products / blog');
            return;
        }

        // ── Products ─────────────────────────────────────────────────────────
        $iprop = new IblockTemplates($productsId);
        $iprop->set([
            'ELEMENT_PAGE_TITLE'       => '{=this.Name}',
            'ELEMENT_META_TITLE'       => '{=this.Name} — купить в Узбекистане | Gree',
            'ELEMENT_META_KEYWORDS'    => '{=this.Name}, gree, кондиционер, купить, узбекистан',
            'ELEMENT_META_DESCRIPTION' => 'Кондиционер {=this.Name}: характеристики, цены, доставка по Узбекистану. Гарантия Gree 5 лет.',
        ]);
        $this->out('  Products iblock id=%d — IPROPERTY templates set', $productsId);

        // ── Blog ─────────────────────────────────────────────────────────────
        $iprop = new IblockTemplates($blogId);
        $iprop->set([
            'ELEMENT_PAGE_TITLE'       => '{=this.Name}',
            'ELEMENT_META_TITLE'       => '{=this.Name} — Блог Gree',
            'ELEMENT_META_KEYWORDS'    => 'gree, блог, {=this.Name}',
            'ELEMENT_META_DESCRIPTION' => '{=this.PreviewText}',
        ]);
        $this->out('  Blog iblock id=%d — IPROPERTY templates set', $blogId);

        $this->outSuccess('IPROPERTY-шаблоны установлены');
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $helper = $this->getHelperManager();

        foreach (['products', 'blog'] as $code) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($code);
            if (!$iblockId) {
                continue;
            }
            (new IblockTemplates($iblockId))->set([
                'ELEMENT_PAGE_TITLE'       => '',
                'ELEMENT_META_TITLE'       => '',
                'ELEMENT_META_KEYWORDS'    => '',
                'ELEMENT_META_DESCRIPTION' => '',
            ]);
        }
        $this->outSuccess('IPROPERTY-шаблоны очищены');
    }
}
