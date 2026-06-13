<?php

namespace Sprint\Migration;

/**
 * Сидит структуру меню в iblock `footer_menu`:
 *
 *   footer-catalog            (заголовок «Каталог»)
 *     ├── footer-catalog-wall          → /catalog/nastennie/
 *     ├── footer-catalog-column        → /catalog/kolonnye/
 *     └── footer-catalog-industrial    → /catalog/promyshlennye/
 *   footer-company            (заголовок «Компания»)
 *     ├── footer-about-brand           → /brand/gree/
 *     ├── footer-payment               → /help/#payment
 *     ├── footer-delivery              → /help/#delivery
 *     ├── footer-exchange              → /help/#exchange
 *     ├── footer-return                → /help/#refund
 *     └── footer-service               → /help/#service
 */
class Version20260603000007 extends Version
{
    protected $description = "Структура меню футера";

    /**
     * @var array<int, array{code: string, ru: string, uz: string, url: string, children?: array}>
     */
    private array $tree = [
        [
            'code' => 'footer-catalog', 'ru' => 'Каталог', 'uz' => 'Katalog', 'url' => '',
            'children' => [
                ['code' => 'footer-catalog-wall',       'ru' => 'Настенные',    'uz' => 'Devorga o\'rnatiladigan', 'url' => '/catalog/nastennie/'],
                ['code' => 'footer-catalog-column',     'ru' => 'Колонные',     'uz' => 'Ustun',                   'url' => '/catalog/kolonnye/'],
                ['code' => 'footer-catalog-industrial', 'ru' => 'Промышленные', 'uz' => 'Sanoat',                  'url' => '/catalog/promyshlennye/'],
            ],
        ],
        [
            'code' => 'footer-company', 'ru' => 'Компания', 'uz' => 'Kompaniya', 'url' => '',
            'children' => [
                ['code' => 'footer-about-brand', 'ru' => 'О бренде',         'uz' => 'Brend haqida',     'url' => '/brand/gree/'],
                ['code' => 'footer-payment',     'ru' => 'Оплата',           'uz' => 'To\'lov',          'url' => '/help/#payment'],
                ['code' => 'footer-delivery',    'ru' => 'Доставка',         'uz' => 'Yetkazib berish',  'url' => '/help/#delivery'],
                ['code' => 'footer-exchange',    'ru' => 'Обмен',            'uz' => 'Almashtirish',     'url' => '/help/#exchange'],
                ['code' => 'footer-return',      'ru' => 'Возврат',          'uz' => 'Qaytarish',        'url' => '/help/#refund'],
                ['code' => 'footer-service',     'ru' => 'Сервисный центр',  'uz' => 'Servis markazi',   'url' => '/help/#service'],
            ],
        ],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('footer_menu');
        if (!$iblockId) {
            $this->outError('Iblock footer_menu не найден');
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
                'UF_LABEL_UZ'       => $top['uz'],
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
                    'UF_LABEL_UZ'       => $child['uz'],
                    'UF_URL'            => $child['url'],
                ]);
                $childSort += 100;
            }
            $sort += 100;
        }

        \CIBlock::clearIblockTagCache($iblockId);
        $this->outSuccess('Footer-меню заполнено');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('footer_menu');
        if (!$iblockId) {
            return;
        }
        foreach ($this->tree as $top) {
            foreach ($top['children'] ?? [] as $child) {
                $helper->Iblock()->deleteSectionIfExists($iblockId, $child['code']);
            }
            $helper->Iblock()->deleteSectionIfExists($iblockId, $top['code']);
        }
        $this->outSuccess('Footer-меню очищено');
    }
}
