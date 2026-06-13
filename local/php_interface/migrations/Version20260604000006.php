<?php

namespace Sprint\Migration;

/**
 * Сиды страницы /partners/:
 *   - 3 элемента partners_b2b
 *   - 4 элемента partners_how_it_works
 *   - 5 элементов partners_companies_trust (логотипы из dist/images/)
 *   - SEO page_code='partners'
 *   - UI-переводы: hero, секция benefits (5 карточек), модалка партнёра, breadcrumb
 */
class Version20260604000006 extends Version
{
    protected $description = "Сиды /partners/: iblocks + benefits translations + SEO";

    private string $imagesDir;

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $this->imagesDir = __DIR__ . '/../../../dist/images';

        $this->seedB2b();
        $this->seedHowItWorks();
        $this->seedCompanies();
        $this->seedSeo();
        $this->seedTranslations();
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    private function seedB2b(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('partners_b2b');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'large-company', 'sort' => 100, 'step' => 1,
                'name_ru' => 'Крупная компания',
                'name_uz' => 'Yirik kompaniya',
                'desc_ru' => 'Предлагайте скидки сотрудникам',
                'desc_uz' => 'Xodimlaringizga chegirmalar taklif qiling',
            ],
            [
                'code' => 'developer', 'sort' => 200, 'step' => 2,
                'name_ru' => 'Застройщик',
                'name_uz' => 'Quruvchi',
                'desc_ru' => 'Добавляйте бонус для новых жильцов',
                'desc_uz' => 'Yangi yashovchilar uchun bonus qo\'shing',
            ],
            [
                'code' => 'agent', 'sort' => 300, 'step' => 3,
                'name_ru' => 'Агент или сеть',
                'name_uz' => 'Agent yoki tarmoq',
                'desc_ru' => 'Получайте комиссию за продажи',
                'desc_uz' => 'Sotuvlar uchun komissiya oling',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU' => $it['name_ru'], 'NAME_UZ' => $it['name_uz'],
                'DESCRIPTION_RU' => $it['desc_ru'], 'DESCRIPTION_UZ' => $it['desc_uz'],
                'STEP_NUMBER' => $it['step'],
            ]);
        }
        $this->out('  b2b: %d', count($items));
    }

    private function seedHowItWorks(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('partners_how_it_works');
        if (!$iblockId) {
            return;
        }
        $items = [
            [
                'code' => 'flexible-terms', 'sort' => 100,
                'name_ru' => 'Гибкие условия сотрудничества',
                'name_uz' => 'Hamkorlikning moslashuvchan shartlari',
                'desc_ru' => 'Работаем по разным схемам сотрудничества: риелторы, застройщики, сервисные центры, частные магазины, торговые сети. Предоставляем высокие бонусы и разные бонусные системы',
                'desc_uz' => 'Turli hamkorlik sxemalari bo\'yicha ishlaymiz: rieltorlar, quruvchilar, servis markazlari, xususiy do\'konlar, savdo tarmoqlari. Yuqori bonuslar va turli bonus tizimlarini taqdim etamiz',
                'link_label_ru' => 'Связаться в телеграм',
                'link_label_uz' => 'Telegramda bog\'lanish',
                'link_url' => 'https://t.me/Gree_5',
            ],
            [
                'code' => 'marketing-support', 'sort' => 200,
                'name_ru' => 'Маркетинговая поддержка',
                'name_uz' => 'Marketing yordami',
                'desc_ru' => 'Предоставляем: POS материалы, реклама ваших точек продаж соцсетях бренда, совместные промо акции, наружная реклама в городах вашего присутствия, упоминания в СМИ, и другие маркетинговые активности',
                'desc_uz' => 'Taqdim etamiz: POS materiallari, sizning sotuv nuqtalaringizni brend ijtimoiy tarmoqlarida reklama qilish, qo\'shma promo-aksiyalar, mavjudlik shaharlaringizdagi tashqi reklama, OAVdagi eslatib o\'tish va boshqa marketing faoliyati',
                'link_label_ru' => 'Скачать презентацию для партнёров',
                'link_label_uz' => 'Hamkorlar uchun taqdimotni yuklab olish',
                'link_url' => '/dist/files/partners-presentation.pdf',
            ],
            [
                'code' => 'simple-shipments', 'sort' => 300,
                'name_ru' => 'Простые отгрузки',
                'name_uz' => 'Oddiy yetkazib berishlar',
                'desc_ru' => 'Быстро и без задержек делаем отгрузки товара. У нас собственный склад и оперативная коммуникация.',
                'desc_uz' => 'Mahsulotlarni tez va kechikishsiz jo\'natamiz. Bizda o\'z omborimiz va operativ aloqa bor.',
                'link_label_ru' => '', 'link_label_uz' => '', 'link_url' => '',
            ],
            [
                'code' => 'boost-sales', 'sort' => 400,
                'name_ru' => 'Поднимем продажи',
                'name_uz' => 'Sotuvlarni oshiramiz',
                'desc_ru' => 'Gree - это бренд мирового уровня. Сотрудничество с Gree приведет к вам новых лояльных клиентов и укрепит репутацию вашего бизнеса',
                'desc_uz' => 'Gree — bu jahon darajasidagi brend. Gree bilan hamkorlik sizga yangi sodiq mijozlarni olib keladi va biznesingiz obro\'sini mustahkamlaydi',
                'link_label_ru' => '', 'link_label_uz' => '', 'link_url' => '',
            ],
        ];
        foreach ($items as $it) {
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $it['sort'],
            ], [
                'NAME_RU' => $it['name_ru'], 'NAME_UZ' => $it['name_uz'],
                'DESCRIPTION_RU' => $it['desc_ru'], 'DESCRIPTION_UZ' => $it['desc_uz'],
                'LINK_LABEL_RU' => $it['link_label_ru'], 'LINK_LABEL_UZ' => $it['link_label_uz'],
                'LINK_URL' => $it['link_url'],
            ]);
        }
        $this->out('  how_it_works: %d', count($items));
    }

    private function seedCompanies(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('partners_companies_trust');
        if (!$iblockId) {
            return;
        }
        $items = [
            ['code' => 'company-1', 'image' => '4ae7ef2340902a838c212ce792301334f2ed7050.png', 'name_ru' => 'Компания 1', 'name_uz' => 'Kompaniya 1'],
            ['code' => 'company-2', 'image' => 'b558ec8593dd61ff1f27f609fd7d5f04bd5163b3.png', 'name_ru' => 'Компания 2', 'name_uz' => 'Kompaniya 2'],
            ['code' => 'company-3', 'image' => 'fa75610a5d1a1f878934a1bd91ed0a2d069a7a4b.png', 'name_ru' => 'Компания 3', 'name_uz' => 'Kompaniya 3'],
            ['code' => 'company-4', 'image' => '39d4ff76f6739ab1ba05f11c88ad890a3855f7f8.png', 'name_ru' => 'Компания 4', 'name_uz' => 'Kompaniya 4'],
            ['code' => 'company-5', 'image' => '21c7fb47650eed8f89a75015fd2b1b3b650d8edb.png', 'name_ru' => 'Компания 5', 'name_uz' => 'Kompaniya 5'],
        ];
        $sort = 100;
        foreach ($items as $it) {
            $path = $this->imagesDir . '/' . $it['image'];
            $helper->Iblock()->saveElement($iblockId, [
                'NAME' => $it['name_ru'], 'CODE' => $it['code'], 'ACTIVE' => 'Y', 'SORT' => $sort,
            ], [
                'NAME_RU' => $it['name_ru'], 'NAME_UZ' => $it['name_uz'],
                'IMAGE'   => is_file($path) ? \CFile::MakeFileArray($path) : [],
            ]);
            $sort += 100;
        }
        $this->out('  companies: %d', count($items));
    }

    private function seedSeo(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Seo');
        if (!$hlblockId) {
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $fields = [
            'UF_PAGE_CODE'         => 'partners',
            'UF_TITLE_RU'          => 'Партнёрам Gree Узбекистан — B2B-сотрудничество, бонусы и маркетинговая поддержка',
            'UF_TITLE_UZ'          => 'Gree O\'zbekiston hamkorlariga — B2B hamkorlik, bonuslar va marketing yordami',
            'UF_DESCRIPTION_RU'    => 'Стать партнёром MYGree Group: гибкие условия для застройщиков, торговых сетей, сервисных центров и риелторов. POS-материалы, совместные промо, отгрузки со своего склада.',
            'UF_DESCRIPTION_UZ'    => 'MYGree Group hamkori bo\'lish: quruvchilar, savdo tarmoqlari, servis markazlari va rieltorlar uchun moslashuvchan shartlar. POS-materiallar, qo\'shma promo, o\'z omboridan yetkazib berish.',
            'UF_KEYWORDS_RU'       => 'gree, партнёрство, b2b, дилеры, оптовые продажи, бонусы, маркетинг, узбекистан',
            'UF_KEYWORDS_UZ'       => 'gree, hamkorlik, b2b, dilerlar, ulgurji savdo, bonuslar, marketing, o\'zbekiston',
            'UF_OG_TITLE_RU'       => 'Партнёрам Gree',
            'UF_OG_TITLE_UZ'       => 'Gree hamkorlariga',
            'UF_OG_DESCRIPTION_RU' => 'Присоединяйтесь к партнёрам Gree в Узбекистане — гибкие условия и поддержка от MYGree Group.',
            'UF_OG_DESCRIPTION_UZ' => 'O\'zbekistondagi Gree hamkorlariga qo\'shiling — MYGree Group dan moslashuvchan shartlar va yordam.',
            'UF_OG_IMAGE'          => '',
        ];

        $exists = $dataClass::query()->where('UF_PAGE_CODE', 'partners')->setSelect(['ID'])->exec()->fetch();
        if ($exists) {
            $dataClass::update((int) $exists['ID'], $fields);
        } else {
            $dataClass::add($fields);
        }
        $this->outSuccess('SEO «partners» сохранён');
    }

    private function seedTranslations(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $existing = [];
        foreach ($dataClass::query()->setSelect(['UF_CODE'])->exec() as $row) {
            $existing[] = (string) ($row['UF_CODE'] ?? '');
        }

        $entries = [
            'breadcrumbs.partners' => ['ru' => 'Партнёрам', 'uz' => 'Hamkorlarga'],

            // hero
            'partners.hero.title' => ['ru' => 'Присоединяйтесь к партнёрам Gree', 'uz' => 'Gree hamkorlariga qo\'shiling'],
            'partners.hero.description' => [
                'ru' => 'Компания My Gree Group всегда рада приветствовать новых партнеров',
                'uz' => 'My Gree Group kompaniyasi yangi hamkorlarni kutib olishdan doim mamnun',
            ],
            'partners.hero.cta' => ['ru' => 'Стать партнёром', 'uz' => 'Hamkor bo\'lish'],

            // section titles
            'partners.section.b2b.title' => ['ru' => 'B2B-партнёрство с MYGree Group', 'uz' => 'MYGree Group bilan B2B hamkorlik'],
            'partners.section.b2b.description' => ['ru' => 'Выбирайте удобную модель для сотрудничества', 'uz' => 'Hamkorlik uchun qulay modelni tanlang'],
            'partners.section.how_it_works.title' => ['ru' => 'Как работает партнёрство', 'uz' => 'Hamkorlik qanday ishlaydi'],
            'partners.section.benefits.title' => ['ru' => 'Почему легко продавать кондиционеры Gree?', 'uz' => 'Nima uchun Gree konditsionerlarini sotish oson?'],
            'partners.section.companies.title' => ['ru' => 'Компании, которые нам доверяют', 'uz' => 'Bizga ishonadigan kompaniyalar'],

            // benefits cards (5 шт.) — задаём здесь, в blade @foreach по статическому массиву кодов
            'partners.benefits.climate.title' => ['ru' => 'Идеально для Узбекистана', 'uz' => 'O\'zbekiston uchun ideal'],
            'partners.benefits.climate.description' => ['ru' => 'Работает при температурах -30 до +50', 'uz' => '-30 dan +50 gacha haroratlarda ishlaydi'],
            'partners.benefits.service.title' => ['ru' => 'Сервисные центры', 'uz' => 'Servis markazlari'],
            'partners.benefits.service.description' => ['ru' => 'Работают по всей республике', 'uz' => 'Butun respublika bo\'ylab ishlaydi'],
            'partners.benefits.brand.title' => ['ru' => 'Сильный бренд', 'uz' => 'Kuchli brend'],
            'partners.benefits.brand.description' => ['ru' => 'Каждый третий кондиционер в мире — это Gree', 'uz' => 'Dunyodagi har uchinchi konditsioner — Gree'],
            'partners.benefits.range.title' => ['ru' => 'Широкий модельный ряд', 'uz' => 'Keng modellar qatori'],
            'partners.benefits.range.description' => ['ru' => 'На любой бюджет', 'uz' => 'Har qanday byudjet uchun'],
            'partners.benefits.warranty.title' => ['ru' => 'Гарантия', 'uz' => 'Kafolat'],
            'partners.benefits.warranty.description' => ['ru' => 'На каждый кондиционер гарантия до 10 лет', 'uz' => 'Har bir konditsionerga 10 yilgacha kafolat'],

            // popup (партнёрская форма) — переиспользуется и в product feedback тоже
            'partner.popup.title' => ['ru' => 'Стать партнёром Gree', 'uz' => 'Gree hamkori bo\'lish'],
            'partner.popup.subtitle' => ['ru' => 'Оставьте контакты — менеджер MYGree Group свяжется с вами', 'uz' => 'Kontaktlaringizni qoldiring — MYGree Group menejeri siz bilan bog\'lanadi'],
            'partner.popup.company' => ['ru' => 'Компания', 'uz' => 'Kompaniya'],
            'partner.popup.company_placeholder' => ['ru' => 'Название вашей компании', 'uz' => 'Kompaniyangiz nomi'],
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
        $this->outSuccess('Переводы partners: +%d / %d', $added, count($entries));
    }
}
