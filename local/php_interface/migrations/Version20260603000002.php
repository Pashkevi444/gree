<?php

namespace Sprint\Migration;

/**
 * Сиды контента для help-iblock'ов (структура — Version20260603000001).
 *
 * Все тексты — _RU + _UZ. Картинки (логотипы платёжных систем + фон hero)
 * берутся из dist/images/ через CFile::MakeFileArray и копируются Bitrix'ом
 * в /upload/iblock/ при saveElement.
 *
 * Идемпотентность: saveElement в sprint.migration ищет существующий
 * элемент по CODE и обновляет, иначе создаёт.
 */
class Version20260603000002 extends Version
{
    protected $description = "Сиды help-iblock'ов (RU + UZ + картинки)";

    private string $imagesDir;

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $this->imagesDir = __DIR__ . '/../../../dist/images';

        $this->seedPaymentMethods();
        $this->seedDelivery();
        $this->seedExchangeSteps();
        $this->seedRefundSteps();
        $this->seedServiceFeatures();
        $this->seedServiceHero();
        $this->seedServiceCards();

        $this->outSuccess('Help контент засеяны');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется — данные уйдут вместе с iblock\'ами');
    }

    private function seedPaymentMethods(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_payment_methods');
        if (!$iblockId) {
            return;
        }
        $items = [
            ['code' => 'uzcard',      'sort' => 100, 'name_ru' => 'Uzcard',     'name_uz' => 'Uzcard',     'image' => '68c8aef98aa9e7fc0aaa38446e42a9c8ad602fae.png'],
            ['code' => 'humo',        'sort' => 200, 'name_ru' => 'Humo',       'name_uz' => 'Humo',       'image' => '784e09359ee30792bd2a25c9199a875e6d2934af.png'],
            ['code' => 'visa',        'sort' => 300, 'name_ru' => 'Visa',       'name_uz' => 'Visa',       'image' => '12af07a7f544f60b119ba0ea9effb3c21ae1eab6.png'],
            ['code' => 'mastercard',  'sort' => 400, 'name_ru' => 'MasterCard', 'name_uz' => 'MasterCard', 'image' => 'd65277cbaca5d017219103e550804c0b8f7bd800.png'],
            ['code' => 'installment', 'sort' => 500, 'name_ru' => 'Рассрочка',  'name_uz' => 'Bo\'lib to\'lash', 'image' => 'Frame 162.png'],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU' => $it['name_ru'],
                'NAME_UZ' => $it['name_uz'],
                'IMAGE'   => $this->fileArray($it['image']),
            ]);
        }
        $this->out('  payment-methods: %d', count($items));
    }

    private function seedDelivery(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_delivery');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'free-tashkent', 'sort' => 100, 'icon' => 'check',
                'name_ru' => 'Бесплатно по Ташкенту',
                'name_uz' => 'Toshkent bo\'ylab bepul',
                'desc_ru' => 'Срок доставки — до 1 рабочего дня, доставим до подъезда',
                'desc_uz' => 'Yetkazib berish muddati — 1 ish kunigacha, podyezdgacha yetkazamiz',
            ],
            [
                'code' => 'paid-uzbekistan', 'sort' => 200, 'icon' => 'dollar',
                'name_ru' => 'Платная доставка по Узбекистану',
                'name_uz' => 'O\'zbekiston bo\'ylab pullik yetkazib berish',
                'desc_ru' => 'Срок и стоимость доставки по Узбекистану согласовывается индивидуально при заказе. Доставка осуществляется до "пятака" города',
                'desc_uz' => 'Yetkazib berish muddati va narxi buyurtma berishda individual tarzda kelishiladi. Yetkazib berish shahar markazigacha amalga oshiriladi',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'         => $it['name_ru'],
                'NAME_UZ'         => $it['name_uz'],
                'PREVIEW_TEXT_RU' => $it['desc_ru'],
                'PREVIEW_TEXT_UZ' => $it['desc_uz'],
                'ICON_CODE'       => $it['icon'],
            ]);
        }
        $this->out('  delivery: %d', count($items));
    }

    private function seedExchangeSteps(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_exchange_steps');
        if (!$iblockId) {
            return;
        }
        $items = $this->commonSteps([
            'application' => [
                'name_ru' => 'Обратиться в филиал',
                'name_uz' => 'Filialga murojaat qilish',
                'desc_ru' => 'В тот, где совершали покупку кондиционера',
                'desc_uz' => 'Konditsionerni sotib olgan filialga',
            ],
        ]);
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'         => $it['name_ru'],
                'NAME_UZ'         => $it['name_uz'],
                'PREVIEW_TEXT_RU' => $it['desc_ru'],
                'PREVIEW_TEXT_UZ' => $it['desc_uz'],
                'STEP_NUMBER'     => $it['step'],
            ]);
        }
        $this->out('  exchange-steps: %d', count($items));
    }

    private function seedRefundSteps(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_refund_steps');
        if (!$iblockId) {
            return;
        }
        $items = $this->commonSteps([
            'application' => [
                'name_ru' => 'Написать заявление в филиале',
                'name_uz' => 'Filialda ariza yozish',
                'desc_ru' => 'В тот, где совершали покупку кондиционера',
                'desc_uz' => 'Konditsionerni sotib olgan filialga',
            ],
        ]);
        // Третья карточка (проверка) имеет tooltip — добавим.
        $tooltipRu = 'Возврат товара возможен только при сохранении его товарного вида, упаковки и потребительских свойств, наличия всех предметов комплектации и не был в употреблении';
        $tooltipUz = 'Mahsulotni qaytarish faqat uning ko\'rinishi, qadog\'i va iste\'mol xususiyatlari saqlangan, barcha komplekt buyumlari mavjud va foydalanilmagan bo\'lsa mumkin';
        foreach ($items as $it) {
            $props = [
                'NAME_RU'         => $it['name_ru'],
                'NAME_UZ'         => $it['name_uz'],
                'PREVIEW_TEXT_RU' => $it['desc_ru'],
                'PREVIEW_TEXT_UZ' => $it['desc_uz'],
                'STEP_NUMBER'     => $it['step'],
            ];
            if ($it['code'] === 'check') {
                $props['TOOLTIP_RU'] = $tooltipRu;
                $props['TOOLTIP_UZ'] = $tooltipUz;
            }
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], $props);
        }
        $this->out('  refund-steps: %d', count($items));
    }

    private function seedServiceFeatures(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_service_features');
        if (!$iblockId) {
            return;
        }
        $items = [
            ['code' => 'warranty', 'sort' => 100, 'icon' => 'warranty', 'name_ru' => 'Гарантия',              'name_uz' => 'Kafolat'],
            ['code' => 'parts',    'sort' => 200, 'icon' => 'parts',    'name_ru' => 'Оригинальные запчасти', 'name_uz' => 'Original ehtiyot qismlar'],
            ['code' => 'brigades', 'sort' => 300, 'icon' => 'brigades', 'name_ru' => 'Мобильные бригады',     'name_uz' => 'Mobil brigadalar'],
            ['code' => 'support',  'sort' => 400, 'icon' => 'support',  'name_ru' => 'Поддержка',             'name_uz' => 'Qo\'llab-quvvatlash'],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'   => $it['name_ru'],
                'NAME_UZ'   => $it['name_uz'],
                'ICON_CODE' => $it['icon'],
            ]);
        }
        $this->out('  service-features: %d', count($items));
    }

    private function seedServiceHero(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_service_hero');
        if (!$iblockId) {
            return;
        }
        $helper->Iblock()->saveElement($iblockId, [
            'NAME' => 'Надёжный сервис', 'CODE' => 'main', 'ACTIVE' => 'Y', 'SORT' => 100,
        ], [
            'NAME_RU'         => 'Надёжный сервис по всему Узбекистану',
            'NAME_UZ'         => 'O\'zbekiston bo\'ylab ishonchli servis',
            'PREVIEW_TEXT_RU' => 'Единый сервисный центр обеспечивает гарантийное обслуживание по всей стране, а также быстрый ремонт без ожидания поставок из-за склада оригинальных запчастей',
            'PREVIEW_TEXT_UZ' => 'Yagona servis markazi butun mamlakat bo\'ylab kafolat xizmatini ta\'minlaydi, shuningdek, original ehtiyot qismlar omborida tezkor ta\'mir qiladi',
            'BACKGROUND'      => $this->fileArray('img.png'),
        ]);
        $this->out('  service-hero: 1');
    }

    private function seedServiceCards(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('help_service_cards');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'install', 'sort' => 100, 'icon' => 'check',
                'name_ru' => 'Установка',
                'name_uz' => 'O\'rnatish',
                'desc_ru' => 'Установку кондиционеров Gree должны выполнять квалифицированные специалисты. Сервисные инженеры обеспечат корректный монтаж, настройку, проверку работы и покажут как пользоваться функциями устройства.',
                'desc_uz' => 'Gree konditsionerlarini o\'rnatishni malakali mutaxassislar bajarishi kerak. Servis muhandislari to\'g\'ri montaj, sozlash, ish faoliyatini tekshirish va qurilma funksiyalaridan qanday foydalanishni ko\'rsatib beradi.',
            ],
            [
                'code' => 'maintenance', 'sort' => 200, 'icon' => 'check',
                'name_ru' => 'Сервисное обслуживание',
                'name_uz' => 'Servis xizmati',
                'desc_ru' => 'Кондиционеры Gree — это технологии и инженерные решения для комфорта и надёжности. Чтобы сохранить эффективность и безопасность, важно регулярно очищать устройство и проводить гигиеническую обработку.',
                'desc_uz' => 'Gree konditsionerlari — bu qulaylik va ishonchlilik uchun texnologiyalar va muhandislik yechimlari. Samaradorlik va xavfsizlikni saqlash uchun qurilmani muntazam tozalash va gigienik ishlov berish muhimdir.',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU'         => $it['name_ru'],
                'NAME_UZ'         => $it['name_uz'],
                'PREVIEW_TEXT_RU' => $it['desc_ru'],
                'PREVIEW_TEXT_UZ' => $it['desc_uz'],
                'ICON_CODE'       => $it['icon'],
            ]);
        }
        $this->out('  service-cards: %d', count($items));
    }

    /**
     * Общая часть карточек обмена/возврата (первая + третья). Вторая
     * различается, передаём через $middle.
     *
     * @param array<string, array<string, string>> $middle  ['code'=>'application', 'name_ru'=>..., …]
     * @return array<int, array<string, mixed>>
     */
    private function commonSteps(array $middle): array
    {
        $items = [
            [
                'code' => 'docs', 'sort' => 100, 'step' => 1,
                'name_ru' => 'Взять документы и договор',
                'name_uz' => 'Hujjatlar va shartnomani olish',
                'desc_ru' => 'Паспорт, ID карта или водительское удостоверение',
                'desc_uz' => 'Pasport, ID karta yoki haydovchilik guvohnomasi',
            ],
        ];
        foreach ($middle as $_ => $m) {
            $items[] = [
                'code' => 'application', 'sort' => 200, 'step' => 2,
                'name_ru' => $m['name_ru'],
                'name_uz' => $m['name_uz'],
                'desc_ru' => $m['desc_ru'],
                'desc_uz' => $m['desc_uz'],
            ];
        }
        $items[] = [
            'code' => 'check', 'sort' => 300, 'step' => 3,
            'name_ru' => 'Дождаться проверки',
            'name_uz' => 'Tekshiruvni kutish',
            'desc_ru' => 'Специалисты проверят товар и отправят его на ремонт',
            'desc_uz' => 'Mutaxassislar mahsulotni tekshiradi va ta\'mirga jo\'natadi',
        ];
        return $items;
    }

    private function fileArray(string $filename): array
    {
        $path = $this->imagesDir . '/' . $filename;
        if (!is_file($path)) {
            $this->out('    WARN: image not found %s', $path);
            return [];
        }
        return \CFile::MakeFileArray($path);
    }
}
