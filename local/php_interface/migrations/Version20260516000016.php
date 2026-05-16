<?php

namespace Sprint\Migration;

/**
 * Сидит структуру хедер-меню в iblock `menu` (секциями).
 *
 *   catalog (button — UF_URL пусто)
 *     ├── catalog-wall      → /catalog/nastennie/
 *     ├── catalog-column    → /catalog/kolonnye/
 *     └── catalog-industrial→ /catalog/promyshlennye/
 *   brand   (link, /brand/gree/)
 *     ├── brand-news        → /blog/
 *     └── brand-tips        → /blog/
 *   help    (link, /help.html)
 *     ├── help-payment, help-delivery, help-exchange, help-return, help-service
 *   buy     (link, /buy.html)
 *   partners(link, /partners.html)
 *   contacts(link, /contacts.html)
 */
class Version20260516000016 extends Version
{
    protected $description = "Структура меню сайта";

    /**
     * @var array<int, array{code: string, ru: string, en: string, url: string, children?: array}>
     */
    private array $tree = [
        [
            'code' => 'catalog', 'ru' => 'Каталог', 'en' => 'Catalog', 'url' => '',
            'children' => [
                ['code' => 'catalog-wall',       'ru' => 'Настенные кондиционеры',   'en' => 'Wall-mounted air conditioners',   'url' => '/catalog/nastennie/'],
                ['code' => 'catalog-column',     'ru' => 'Колонные кондиционеры',     'en' => 'Column air conditioners',          'url' => '/catalog/kolonnye/'],
                ['code' => 'catalog-industrial', 'ru' => 'Промышленные кондиционеры', 'en' => 'Industrial air conditioners',      'url' => '/catalog/promyshlennye/'],
            ],
        ],
        [
            'code' => 'brand', 'ru' => 'О бренде', 'en' => 'About brand', 'url' => '/brand/gree/',
            'children' => [
                ['code' => 'brand-news', 'ru' => 'Новости', 'en' => 'News', 'url' => '/blog/'],
                ['code' => 'brand-tips', 'ru' => 'Советы',  'en' => 'Tips', 'url' => '/blog/'],
            ],
        ],
        [
            'code' => 'help', 'ru' => 'Помощь', 'en' => 'Help', 'url' => '/help.html',
            'children' => [
                ['code' => 'help-payment',  'ru' => 'Оплата',           'en' => 'Payment',         'url' => ''],
                ['code' => 'help-delivery', 'ru' => 'Доставка',         'en' => 'Delivery',        'url' => ''],
                ['code' => 'help-exchange', 'ru' => 'Обмен',            'en' => 'Exchange',        'url' => ''],
                ['code' => 'help-return',   'ru' => 'Возврат',          'en' => 'Return',          'url' => ''],
                ['code' => 'help-service',  'ru' => 'Сервисный центр',  'en' => 'Service center',  'url' => ''],
            ],
        ],
        ['code' => 'buy',      'ru' => 'Где купить', 'en' => 'Where to buy', 'url' => '/buy.html'],
        ['code' => 'partners', 'ru' => 'Партнёрам',  'en' => 'For partners', 'url' => '/partners.html'],
        ['code' => 'contacts', 'ru' => 'Контакты',   'en' => 'Contacts',     'url' => '/contacts.html'],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('menu');
        if (!$iblockId) {
            $this->outError('Iblock menu не найден');
            return;
        }

        $sort = 100;
        foreach ($this->tree as $top) {
            $parentId = $helper->Iblock()->saveSection($iblockId, [
                'NAME'              => $top['ru'],
                'CODE'              => $top['code'],
                'SORT'              => $sort,
                'IBLOCK_SECTION_ID' => false,
                'UF_LABEL_RU'       => $top['ru'],
                'UF_LABEL_EN'       => $top['en'],
                'UF_URL'            => $top['url'],
            ]);
            $this->out('  %s [id=%d]', $top['code'], $parentId);

            $childSort = 100;
            foreach ($top['children'] ?? [] as $child) {
                $helper->Iblock()->saveSection($iblockId, [
                    'NAME'              => $child['ru'],
                    'CODE'              => $child['code'],
                    'SORT'              => $childSort,
                    'IBLOCK_SECTION_ID' => $parentId,
                    'UF_LABEL_RU'       => $child['ru'],
                    'UF_LABEL_EN'       => $child['en'],
                    'UF_URL'            => $child['url'],
                ]);
                $childSort += 100;
            }
            $sort += 100;
        }

        $this->outSuccess('Меню заполнено');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('menu');
        if (!$iblockId) {
            return;
        }
        foreach ($this->tree as $top) {
            foreach ($top['children'] ?? [] as $child) {
                $helper->Iblock()->deleteSectionIfExists($iblockId, $child['code']);
            }
            $helper->Iblock()->deleteSectionIfExists($iblockId, $top['code']);
        }
        $this->outSuccess('Меню очищено');
    }
}
