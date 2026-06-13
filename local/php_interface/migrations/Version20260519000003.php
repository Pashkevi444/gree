<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;

/**
 * Заливает узбекский (latin O'zbek tili) контент в свойства _UZ, созданные
 * Version20260519000001/000002 (рейминг _EN→_UZ).
 *
 * Покрывается:
 *   - iblock элементы: products, brands, home_*, brand_*, blog — все *_UZ
 *     свойства (NAME_UZ, PREVIEW_TEXT_UZ, DETAIL_TEXT_UZ, COOLING_POWER_UZ
 *     и т.д.). Поиск элемента по CODE.
 *   - iblock секции menu: UF_LABEL_UZ по section CODE.
 *   - HL «Translations»: UF_VALUE_UZ по UF_CODE.
 *   - HL «Seo»: UF_*_UZ по UF_PAGE_CODE.
 *
 * ⚠ TODO (контент-менеджер): переводы автогенерированы на основе RU/EN
 * исходников. Перед боевым выкатом нужна вычитка носителем — поправлять можно
 * прямо в админке Bitrix, перенакат миграции не требуется.
 */
class Version20260519000003 extends Version
{
    protected $description = "Заливка узбекского (latin) контента в _UZ свойства";

    public function up(): void
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');

        $touchedIblock = $this->seedIblockElements();
        $touchedProductsCommon = $this->seedProductsCommon();
        $touchedOffers = $this->seedProductOffers();
        $touchedSections = $this->seedMenuSections();
        $touchedTranslations = $this->seedTranslations();
        $touchedSeo = $this->seedSeo();
        $touchedBlog = $this->seedBlogArticles();

        $this->outSuccess(
            'Залито UZ: iblock-элементов %d, общих product-полей %d, offers %d, menu-секций %d, переводов %d, SEO %d, блог-статей %d',
            $touchedIblock, $touchedProductsCommon, $touchedOffers,
            $touchedSections, $touchedTranslations, $touchedSeo, $touchedBlog,
        );
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется — данные останутся в _UZ, контент-менеджер правит из админки');
    }

    // ── iblock элементы ──────────────────────────────────────────────────────

    private function seedIblockElements(): int
    {
        $touched = 0;
        foreach ($this->iblockSeeds() as $iblockCode => $elements) {
            $iblockId = $this->iblockIdByCode($iblockCode);
            if (!$iblockId) {
                continue;
            }
            $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

            foreach ($elements as $elementCode => $props) {
                $row = $entity::query()
                    ->where('CODE', $elementCode)
                    ->setSelect(['ID'])
                    ->exec()
                    ->fetch();
                if (!$row) {
                    continue;
                }
                \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, $props);
                $touched++;
            }
        }
        return $touched;
    }

    /**
     * Общий для всех товаров узбекский текст (warranty/kit/installation) —
     * шаблон, не зависящий от модели, поэтому пишется одной заливкой.
     */
    private function seedProductsCommon(): int
    {
        $iblockId = $this->iblockIdByCode('products');
        if (!$iblockId) {
            return 0;
        }

        $props = [
            'WARRANTY_TEXT_UZ'     => '<p>Invertor kompressoriga 10 yillik kafolat. Elektron komponentlarga 3 yil. Servis xizmati va sarflanadigan materiallarga 1 yil.</p>',
            'KIT_TEXT_UZ'          => '<p>Komplektatsiya: ichki blok, tashqi blok, bloklararo kabel, o\'rnatish plitasi, batareyali infraqizil pult, mahsulot pasporti, kafolat talon.</p>',
            'INSTALLATION_TEXT_UZ' => '<p>O\'rnatishni Gree servis markazining sertifikatlangan mutaxassislari amalga oshiradi. Standart o\'rnatish — 3 soat, jumladan 4 m gacha trassa, burg\'ulash, vakuumlash va ishga tushirish.</p>',
        ];

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID'])->exec();
        $touched = 0;
        while ($row = $rows->fetch()) {
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, $props);
            $touched++;
        }
        return $touched;
    }

    private function seedProductOffers(): int
    {
        $iblockId = $this->iblockIdByCode('products_offers');
        if (!$iblockId) {
            return 0;
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $rows = $entity::query()
            ->setSelect(['ID', 'AREA_VALUE' => 'AREA.VALUE'])
            ->exec();

        $touched = 0;
        while ($row = $rows->fetch()) {
            \CIBlockElement::SetPropertyValuesEx(
                (int) $row['ID'],
                $iblockId,
                $this->offerSpecsByArea((int) ($row['AREA_VALUE'] ?? 0)),
            );
            $touched++;
        }
        return $touched;
    }

    private function seedMenuSections(): int
    {
        $iblockId = $this->iblockIdByCode('menu');
        if (!$iblockId) {
            return 0;
        }

        $sec = new \CIBlockSection();
        $touched = 0;
        foreach ($this->menuSeeds() as $code => $label) {
            $row = SectionTable::query()
                ->where('IBLOCK_ID', $iblockId)
                ->where('CODE', $code)
                ->setSelect(['ID'])
                ->exec()
                ->fetch();
            if (!$row) {
                continue;
            }
            $sec->Update((int) $row['ID'], ['UF_LABEL_UZ' => $label]);
            $touched++;
        }
        return $touched;
    }

    // ── HL «Translations» ────────────────────────────────────────────────────

    private function seedTranslations(): int
    {
        $hlblockId = $this->hlblockIdByName('Translations');
        if (!$hlblockId) {
            return 0;
        }
        $dataClass = HighloadBlockTable::compileEntity(HighloadBlockTable::getById($hlblockId)->fetch())->getDataClass();

        $touched = 0;
        foreach ($this->translationSeeds() as $code => $valueUz) {
            $row = $dataClass::query()->where('UF_CODE', $code)->setSelect(['ID'])->exec()->fetch();
            if ($row) {
                $dataClass::update((int) $row['ID'], ['UF_VALUE_UZ' => $valueUz]);
            } else {
                // Новые ключи (например header.lang.uz), которых не было в исторических сидерах.
                $dataClass::add(['UF_CODE' => $code, 'UF_VALUE_RU' => '', 'UF_VALUE_UZ' => $valueUz]);
            }
            $touched++;
        }
        return $touched;
    }

    // ── HL «Seo» ─────────────────────────────────────────────────────────────

    private function seedSeo(): int
    {
        $hlblockId = $this->hlblockIdByName('Seo');
        if (!$hlblockId) {
            return 0;
        }
        $dataClass = HighloadBlockTable::compileEntity(HighloadBlockTable::getById($hlblockId)->fetch())->getDataClass();

        $touched = 0;
        foreach ($this->seoSeeds() as $pageCode => $fields) {
            $row = $dataClass::query()->where('UF_PAGE_CODE', $pageCode)->setSelect(['ID'])->exec()->fetch();
            if (!$row) {
                continue;
            }
            $dataClass::update((int) $row['ID'], $fields);
            $touched++;
        }
        return $touched;
    }

    // ── blog ─────────────────────────────────────────────────────────────────

    private function seedBlogArticles(): int
    {
        $iblockId = $this->iblockIdByCode('blog');
        if (!$iblockId) {
            return 0;
        }
        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

        $touched = 0;
        foreach ($this->blogSeeds() as $code => $props) {
            $row = $entity::query()->where('CODE', $code)->setSelect(['ID'])->exec()->fetch();
            if (!$row) {
                continue;
            }
            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, $props);
            $touched++;
        }
        return $touched;
    }

    /** @return array<string, array<string, string>> CODE → [NAME_UZ, PREVIEW_TEXT_UZ, DETAIL_TEXT_UZ] */
    private function blogSeeds(): array
    {
        return [
            'kak-vybrat-konditsioner' => [
                'NAME_UZ'         => 'Kvartira uchun konditsioner qanday tanlanadi: to\'liq qo\'llanma',
                'PREVIEW_TEXT_UZ' => 'Konditsioner tanlashda nimaga e\'tibor berishni tushuntiramiz: quvvat, xona maydoni, o\'rnatish turi va asosiy texnologiyalar.',
                'DETAIL_TEXT_UZ'  => '<h2>Quvvat va maydon</h2><p>Asosiy parametr — sovutish quvvati. Asosiy hisob: 10 m² ga 1 kVt.</p>',
            ],
            'invertor-preimushhestva' => [
                'NAME_UZ'         => 'Invertorli konditsioner: haqiqiy afzalligi nimada',
                'PREVIEW_TEXT_UZ' => 'Nima uchun invertorli konditsionerlar standartga aylandi va invertor uchun qo\'shimcha to\'lash arziydimi.',
                'DETAIL_TEXT_UZ'  => '<h2>Invertor qanday ishlaydi</h2><p>Invertor kompressor aylanish tezligini silliq tartibga soladi — avtomobildagi gaz pedali kabi.</p>',
            ],
            'obsluzhivanie-konditsionera' => [
                'NAME_UZ'         => 'Konditsionerga texnik xizmat ko\'rsatish: qachon va qanday',
                'PREVIEW_TEXT_UZ' => 'Muntazam xizmat ko\'rsatish konditsioner xizmat muddatini uzaytiradi va samaradorligini saqlaydi.',
                'DETAIL_TEXT_UZ'  => '<h2>O\'zingiz nima qila olasiz</h2><p>Filtrlarni har 2–4 haftada tozalang.</p>',
            ],
            'top-5-konditsionerov-2026' => [
                'NAME_UZ'         => '2026 yilning eng yaxshi 5 konditsioneri: bizning tanlovimiz',
                'PREVIEW_TEXT_UZ' => '2026 yilning eng yaxshi modellarini narx/sifat nisbatiga ko\'ra reytingda joylashtirdik.',
                'DETAIL_TEXT_UZ'  => '<h2>1. Gree BORA X 07</h2><p>O\'z-o\'zini tozalash funksiyasiga ega flagman seriyasi.</p>',
            ],
            'gree-novaya-liniya-invertor' => [
                'NAME_UZ'         => 'Gree invertorli split-tizimlar liniyasini kengaytiradi',
                'PREVIEW_TEXT_UZ' => 'Yaxshilangan energiya samaradorligi va sokin ish bilan yangi modellarni taqdim etamiz.',
                'DETAIL_TEXT_UZ'  => '<p>2026 yil may oyida bozorda 6 ta yangi Gree invertor konditsioner modeli paydo bo\'ldi.</p>',
            ],
            'gree-5-let-garantii' => [
                'NAME_UZ'         => 'Asosiy komponentlarga 5 yillik kafolat',
                'PREVIEW_TEXT_UZ' => 'Texnikamiz sifatiga ishonamiz va kengaytirilgan kafolat beramiz.',
                'DETAIL_TEXT_UZ'  => '<p>Kengaytirilgan kafolat kompressor va invertor modulini qamrab oladi.</p>',
            ],
            'gree-na-climate-world-2026' => [
                'NAME_UZ'         => 'Gree Climate World 2026 ko\'rgazmasida',
                'PREVIEW_TEXT_UZ' => 'Iqlim jihozlari xalqaro ko\'rgazmasida ishtirokimiz natijalarini sarhisob qilamiz.',
                'DETAIL_TEXT_UZ'  => '<p>Gree stendi minglab tashrif buyuruvchilarni va o\'nlab shartnomalarni jamladi.</p>',
            ],
            'gree-otkrytie-magazina-tashkent' => [
                'NAME_UZ'         => 'Toshkentda firma do\'koni ochilishi',
                'PREVIEW_TEXT_UZ' => 'O\'zbekistondagi birinchi rasmiy Gree do\'koni — endi yaqiningizda.',
                'DETAIL_TEXT_UZ'  => '<p>Do\'kon manzili: Toshkent, Amir Temur shoh ko\'chasi, 1.</p>',
            ],
        ];
    }

    // ── lookups ──────────────────────────────────────────────────────────────

    private function iblockIdByCode(string $code): int
    {
        $row = IblockTable::query()->where('CODE', $code)->setSelect(['ID'])->exec()->fetch();
        return (int) ($row['ID'] ?? 0);
    }

    private function hlblockIdByName(string $name): int
    {
        $row = HighloadBlockTable::query()->where('NAME', $name)->setSelect(['ID'])->exec()->fetch();
        return (int) ($row['ID'] ?? 0);
    }

    // ── узбекский контент ────────────────────────────────────────────────────

    /**
     * @return array<string, array<string, array<string, string>>>
     * iblock CODE → [element CODE → [PROP_UZ_code => value]]
     */
    private function iblockSeeds(): array
    {
        return [
            'products' => [
                'gree-bora-x-07' => [
                    'NAME_UZ' => 'Gree BORA X 07',
                    'PREVIEW_TEXT_UZ' => 'Devorga o\'rnatiladigan invertor konditsioneri, 7000 BTU. 20 m² gacha xonalar uchun. Shovqin darajasi 20 dB dan. Energiya sinfi A++.',
                    'DETAIL_TEXT_UZ' => '<p>Gree BORA X — devorga o\'rnatiladigan invertor konditsionerlarining flagman seriyasi. G-Tech texnologiyasi maksimal samaradorlik va minimal shovqinni ta\'minlaydi.</p><p>O\'rnatilgan Cold Catalyst havo tozalagichi bakteriyalar, viruslar va hidlarni yo\'q qiladi.</p>',
                ],
                'gree-bora-x-09' => [
                    'NAME_UZ' => 'Gree BORA X 09',
                    'PREVIEW_TEXT_UZ' => 'Devorga o\'rnatiladigan invertor konditsioneri, 9000 BTU. 25 m² gacha xonalar uchun. GREE+ ilovasi orqali Wi-Fi boshqaruv.',
                    'DETAIL_TEXT_UZ' => '<p>BORA X 09 — yotoq xonalari va kichik mehmonxonalar uchun optimal tanlov. GREE+ mobil ilovasi orqali boshqariladi, Alice va Google Home bilan ishlaydi.</p>',
                ],
                'gree-pular-12' => [
                    'NAME_UZ' => 'Gree Pular 12',
                    'PREVIEW_TEXT_UZ' => 'Devorga o\'rnatiladigan invertor konditsioneri, 12000 BTU. 35 m² gacha xonalar uchun. O\'rnatilgan bug\'lantirgichni o\'z-o\'zidan tozalash.',
                    'DETAIL_TEXT_UZ' => '<p>Pular seriyasi yuqori unumdorlikni intellektual o\'z-o\'zidan tozalash bilan birlashtiradi. Har 8 soat ishlashdan keyin bug\'lantirgich avtomatik tarzda muzlatiladi va quritiladi.</p>',
                ],
                'gree-lomo-dc-09' => [
                    'NAME_UZ' => 'Gree Lomo DC 09',
                    'PREVIEW_TEXT_UZ' => 'Nafis korpusdagi devorga o\'rnatiladigan invertor konditsioneri, 9000 BTU. 25 m² gacha. Shampan rang.',
                    'DETAIL_TEXT_UZ' => '<p>Gree Lomo DC — estetikani qadrlovchilar uchun dizayn seriyasi. Silliq chiziqlar va shampan rang har qanday interyerga mos keladi. Ichida Gree invertor texnologiyalarining to\'liq stek.</p>',
                ],
                'gree-hansol-iii-07' => [
                    'NAME_UZ' => 'Gree Hansol III 07',
                    'PREVIEW_TEXT_UZ' => 'Byudjetli devorga o\'rnatiladigan konditsioner, 7000 BTU, invertorsiz. 20 m² gacha. Maqbul narxdagi ishonchli yechim.',
                    'DETAIL_TEXT_UZ' => '<p>Gree Hansol III — ortiqcha to\'lashni xohlamaganlar uchun isbotlangan ishonchli seriya. Oddiy boshqaruv, standart sovutish/isitish, 3 yil kafolat.</p>',
                ],
                'gree-free-match-12' => [
                    'NAME_UZ' => 'Gree Free Match 12',
                    'PREVIEW_TEXT_UZ' => 'Ustun shaklidagi invertor konditsioneri, 12000 BTU. 35 m² gacha. Do\'konlar, ofislar, katta mehmonxonalar uchun.',
                    'DETAIL_TEXT_UZ' => '<p>Free Match — devorga o\'rnatish imkonsiz yoki kerak bo\'lmagan joylar uchun pol/shipiga o\'rnatiladigan seriya. Polga yoki shiftga o\'rnatiladi. Kuchli Gree G-Tech invertor kompressori.</p>',
                ],
                'gree-gwh18agd' => [
                    'NAME_UZ' => 'Gree GWH18AGD-K3DNA',
                    'PREVIEW_TEXT_UZ' => 'Ustun shaklidagi konditsioner, 18000 BTU. 50 m² gacha. Katta savdo va ofis xonalari uchun.',
                    'DETAIL_TEXT_UZ' => '<p>Katta ochiq xonalar uchun kuchli pol/shifti konditsioner. Ikki yo\'nalishli havo oqimi — yuqori va pastga. O\'rnatilgan drenaj nasosi.</p>',
                ],
                'gree-vir09hp' => [
                    'NAME_UZ' => 'Gree VIR09HP115V1B',
                    'PREVIEW_TEXT_UZ' => 'Sanoat konditsioneri, 9000 BTU. 25 m² gacha. Server xonalari va ishlab chiqarish ob\'ektlari uchun.',
                    'DETAIL_TEXT_UZ' => '<p>Gree VIR sanoat seriyasi sutkasiga 24 soat ishlash uchun yaratilgan. Mustahkamlangan korpus, IP54 chang/namlik himoyasi, ishchi diapazoni -40 °C dan +55 °C gacha.</p>',
                ],
                // Per-product тексты (WARRANTY/KIT/INSTALLATION) — общий стандартный
                // паттерн, проще не повторять per-element. Заполняется в seedProductCommon() ниже.
            ],
            'home_slider' => [
                'slide-main' => [
                    'NAME_UZ' => 'O\'zbekiston uchun mukammal konditsionerlar',
                    'SUBTITLE_UZ' => '<span>+50 °C</span> da sovutadi va <span>−30 °C</span> da isitadi',
                    'BUTTON_TEXT_UZ' => 'Konditsioner tanlash',
                ],
                'slide-about' => [
                    'NAME_UZ' => 'Gree — konditsioner ishlab chiqaruvchi jahon lideri',
                    'SUBTITLE_UZ' => 'Yiliga <span>60 millondan ortiq</span> qurilma — sifat va tajriba kafolati',
                    'BUTTON_TEXT_UZ' => 'Brend haqida bilib oling',
                ],
            ],
            'home_gree_cards' => [
                'guarantee'      => ['NAME_UZ' => 'Kafolat',          'PREVIEW_TEXT_UZ' => 'Konditsioner invertoriga 10 yillik kafolat'],
                'delivery'       => ['NAME_UZ' => 'Yetkazib berish',  'PREVIEW_TEXT_UZ' => 'Shahar bo\'ylab bepul yetkazib berish'],
                'installment'    => ['NAME_UZ' => 'Bo\'lib to\'lash', 'PREVIEW_TEXT_UZ' => 'Komfortni hozir oling, keyin to\'lang'],
                'service-center' => ['NAME_UZ' => 'Servis markazi',   'PREVIEW_TEXT_UZ' => 'O\'z servis markazi — barcha masalalarni tezda hal qilamiz'],
            ],
            'home_gree_stats' => [
                'stat-world-first'  => ['NAME_UZ' => 'Dunyoda №1', 'PREVIEW_TEXT_UZ' => '2024 yilda split-tizimlar ishlab chiqarish bo\'yicha', 'NUMBER_PREFIX_UZ' => '№', 'NUMBER_SUFFIX_UZ' => 'dunyoda'],
                'stat-technologies' => ['NAME_UZ' => '46 texnologiya', 'PREVIEW_TEXT_UZ' => 'Boshqa brendlar o\'z konditsionerlarida foydalanmoqda', 'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => 'texnologiya'],
                'stat-factories'    => ['NAME_UZ' => '18 zavod',    'PREVIEW_TEXT_UZ' => 'Butun dunyo bo\'ylab, shuningdek 1411 laboratoriya',  'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => 'zavod'],
            ],
            'home_app_features' => [
                'remote-control' => ['NAME_UZ' => 'Istalgan joydan boshqaring',  'PREVIEW_TEXT_UZ' => 'Konditsioneringizni uyda, ofisda yoki yo\'lda boshqaring'],
                'energy-saving'  => ['NAME_UZ' => 'Energiya tejash',             'PREVIEW_TEXT_UZ' => 'Konditsionerni faqat haqiqatan kerak bo\'lganda yoqing'],
            ],
            'home_technologies' => [
                'tech-extreme'   => ['NAME_UZ' => 'Ekstremal sharoitlarda ishlash', 'PREVIEW_TEXT_UZ' => '130 V tarmoq va −30 °C dan +53 °C gacha haroratlarda barqaror ishlash.'],
                'tech-smart'     => ['NAME_UZ' => 'Intellektual boshqaruv',         'PREVIEW_TEXT_UZ' => 'Wi-Fi modul, ovozli yordamchilar va to\'liq nazorat uchun GREE+ ilovasi.'],
                'tech-selfclean' => ['NAME_UZ' => 'O\'z-o\'zini tozalash tizimi',   'PREVIEW_TEXT_UZ' => 'Har 8 soat ishlashdan keyin bug\'lantirgichni avtomatik muzlatish va quritish.'],
                'tech-inverter'  => ['NAME_UZ' => 'Invertor texnologiyasi',         'PREVIEW_TEXT_UZ' => 'Quvvatni silliq modulyatsiyalash shovqin va energiya iste\'molini 40% ga kamaytiradi.'],
                'tech-ifeel'     => ['NAME_UZ' => 'I-FEEL funksiyasi',              'PREVIEW_TEXT_UZ' => 'Haroratni faqat ichki blok yonida emas, balki sizning yoningizda o\'lchaydi.'],
                'tech-ionizer'   => ['NAME_UZ' => 'Havo ionizatsiyasi',             'PREVIEW_TEXT_UZ' => 'Cold Plasma bakteriyalar va viruslarni neytrallashtiradi, havoni toza saqlaydi.'],
            ],
            'brand_history' => [
                'history' => [
                    'NAME_UZ' => 'Brend tarixi',
                    'DETAIL_TEXT_UZ' => '<p>GREE tarixi 1991 yilda boshlangan, ikki kompaniya — Guanxiong Plastic Company va Haili Air Conditioner Factory — janubiy Xitoyning Chjuxay shahridagi Gree Air Conditioner Factory ga birlashganida.</p>'
                        . '<p>Kompaniya ichki bozor uchun deraza konditsionerlarini ishlab chiqaradigan bitta zavoddan boshlangan. Dastlab 200 xodim yiliga 20 000 dan kam qurilma ishlab chiqarardi.</p>'
                        . '<p>Bugun GREE da 90 000 dan ortiq xodim, jumladan 16 000 R&D mutaxassisi va 30 000 dan ortiq texnik ishlaydi.</p>'
                        . '<p>GREE — Xitoyning eng yirik va dunyodagi eng yirik konditsioner ishlab chiqaruvchilaridan biri.</p>',
                ],
            ],
            'brand_why_gree' => [
                'why-gree' => [
                    'NAME_UZ' => 'Nima uchun Gree ni tanlashadi',
                    'PREVIEW_TEXT_UZ' => 'Gree — o\'z texnologiyalari, qat\'iy sifat nazorati va har qanday foydalanish stsenariysi uchun yechimlarga ega bo\'lgan konditsioner ishlab chiqarish bo\'yicha jahon lideri.',
                    'BUTTON_TEXT_UZ' => 'Gree haqida ko\'proq bilib oling',
                ],
            ],
            'brand_gree_cards' => [
                'guarantee'      => ['NAME_UZ' => 'Kafolat',          'PREVIEW_TEXT_UZ' => 'Konditsioner invertoriga 10 yillik kafolat'],
                'delivery'       => ['NAME_UZ' => 'Yetkazib berish',  'PREVIEW_TEXT_UZ' => 'Shahar bo\'ylab bepul yetkazib berish'],
                'installment'    => ['NAME_UZ' => 'Bo\'lib to\'lash', 'PREVIEW_TEXT_UZ' => 'Komfortni hozir oling, keyin to\'lang'],
                'service-center' => ['NAME_UZ' => 'Servis markazi',   'PREVIEW_TEXT_UZ' => 'O\'z servis markazi — barcha masalalarni tezda hal qilamiz'],
            ],
            'brand_gree_stats' => [
                'stat-clients'   => ['NAME_UZ' => '500M mijoz',     'PREVIEW_TEXT_UZ' => 'Mamnun mijozlar', 'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => ''],
                'stat-factories' => ['NAME_UZ' => '18 zavod',       'PREVIEW_TEXT_UZ' => 'Zavodlar',        'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => ''],
                'stat-labs'      => ['NAME_UZ' => '1411 laboratoriya', 'PREVIEW_TEXT_UZ' => 'Laboratoriyalar', 'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => ''],
                'stat-engineers' => ['NAME_UZ' => '16000 muhandis', 'PREVIEW_TEXT_UZ' => 'Muhandislar',     'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => ''],
            ],
            'brand_about_cards' => [
                'achievements' => ['NAME_UZ' => 'Yutuqlar',         'DETAIL_TEXT_UZ' => 'GREE — konditsioner ishlab chiqarish bo\'yicha jahon lideri. O\'z tadqiqotlari, minglab patentlar va butun dunyo bo\'ylab millionlab mamnun mijozlar. Biz Gree qurilmalarini hamda dunyo brendlari uchun OEM mahsulotlarini ishlab chiqaramiz.'],
                'mission'      => ['NAME_UZ' => 'Missiya',          'DETAIL_TEXT_UZ' => 'Biz hayotni qulayroq, tozaroq va sokinroq qiladigan aqlli iqlim yechimlarini yaratamiz. GREE — kunlik shovqinsiz, qizib ketishsiz, murosasiz sizning foydangizga ishlaydigan texnologiya. Ishonadigan iqlim.'],
                'quality'      => ['NAME_UZ' => 'Sifat nazorati',   'DETAIL_TEXT_UZ' => 'Birinchi vintdan oxirgi testgacha — har bir GREE konditsioneri ko\'p bosqichli sifat nazoratidan o\'tadi. Biz autsorsing qilmaymiz — barcha yig\'ish va ishlab chiqish bevosita bizning nazoratimiz ostida. Bu sifat kafolati.'],
                'innovations'  => ['NAME_UZ' => 'Innovatsiyalar',   'DETAIL_TEXT_UZ' => 'GREE butun dunyoda 152 R&D markazga ega. Biz ertangi kunning texnologiyalarini yaratamiz: aqlli invertordan havo tozalash tizimlarigacha. Har bir model chuqur muhandislik natijasi, oddiy yig\'ish emas.'],
            ],
            'brand_technologies' => [
                'tech-smps'    => ['NAME_UZ' => 'Innovatsion SMPS transformator', 'DETAIL_TEXT_UZ' => 'Innovatsion SMPS kommutatsiyali transformator kuchlanish o\'zgarishlarida konditsionerni barqaror saqlaydi, energiya iste\'molini kamaytiradi va umumiy ishonchlilikni oshiradi. Samarali va uzoq muddatli iqlim jihozlari uchun zamonaviy yechim.'],
                'tech-silence' => ['NAME_UZ' => 'Past shovqin darajasi',          'DETAIL_TEXT_UZ' => 'Invertor texnologiyasi va ventilyator dizaynining optimallashtirilishi tufayli Gree konditsionerlari deyarli sokin ishlaydi. Bezovta qiluvchi shovqinsiz qulaylik — yotoq xonalari, bolalar xonalari va ofislar uchun ideal.'],
                'tech-ifeel'   => ['NAME_UZ' => 'I-FEEL funksiyasi',              'DETAIL_TEXT_UZ' => 'Simsiz pultdagi harorat sensori yoningizdagi havo haroratini o\'lchaydi va ichki blokga uzatadi. Konditsioner aniq joyingizga e\'tibor qaratadi, qurilma o\'rnatilgan joyga emas.'],
                'tech-night'   => ['NAME_UZ' => 'Qulay tungi rejim',              'DETAIL_TEXT_UZ' => 'Tungi rejim shovqinni avtomatik kamaytiradi va haroratni yumshoq tartibga soladi, optimal uyqu sharoitlarini yaratadi. Sakrashlarsiz, shamolsiz, qizib ketishsiz — butun tun davomida qulaylik va chuqur dam olish.'],
            ],
            'brands' => [
                'gree' => [
                    'NAME_UZ' => 'Gree',
                    'DETAIL_TEXT_UZ' => '<p>Gree — konditsioner ishlab chiqarish bo\'yicha jahon lideri. 1991 yildan beri biz millionlab uy va ofislarni iqlim bilan ta\'minlaymiz.</p>',
                ],
            ],
            // Каталожные карточки (отдельный iblock catalog_gree_cards из 20260516000001)
            'catalog_gree_cards' => [
                'guarantee'      => ['NAME_UZ' => 'Kafolat',          'PREVIEW_TEXT_UZ' => 'Konditsioner invertoriga 10 yillik kafolat'],
                'delivery'       => ['NAME_UZ' => 'Yetkazib berish',  'PREVIEW_TEXT_UZ' => 'Shahar bo\'ylab bepul yetkazib berish'],
                'installment'    => ['NAME_UZ' => 'Bo\'lib to\'lash', 'PREVIEW_TEXT_UZ' => 'Komfortni hozir oling, keyin to\'lang'],
                'service-center' => ['NAME_UZ' => 'Servis markazi',   'PREVIEW_TEXT_UZ' => 'O\'z servis markazi — barcha masalalarni tezda hal qilamiz'],
            ],
            'catalog_gree_stats' => [
                'stat-world-first'  => ['NAME_UZ' => 'Dunyoda №1',    'PREVIEW_TEXT_UZ' => '2024 yilda split-tizimlar ishlab chiqarish bo\'yicha', 'NUMBER_PREFIX_UZ' => '№', 'NUMBER_SUFFIX_UZ' => 'dunyoda'],
                'stat-technologies' => ['NAME_UZ' => '46 texnologiya', 'PREVIEW_TEXT_UZ' => 'Boshqa brendlar o\'z konditsionerlarida foydalanmoqda', 'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => 'texnologiya'],
                'stat-factories'    => ['NAME_UZ' => '18 zavod',       'PREVIEW_TEXT_UZ' => 'Butun dunyo bo\'ylab, shuningdek 1411 laboratoriya', 'NUMBER_PREFIX_UZ' => '', 'NUMBER_SUFFIX_UZ' => 'zavod'],
            ],
        ];
    }

    /** @return array<string, string> section CODE → UF_LABEL_UZ */
    private function menuSeeds(): array
    {
        return [
            'catalog'             => 'Katalog',
            'catalog-wall'        => 'Devorga o\'rnatiladigan konditsionerlar',
            'catalog-column'      => 'Ustun konditsionerlar',
            'catalog-industrial'  => 'Sanoat konditsionerlari',
            'brand'               => 'Brend haqida',
            'brand-news'          => 'Yangiliklar',
            'brand-tips'          => 'Maslahatlar',
            'help'                => 'Yordam',
            'help-payment'        => 'To\'lov',
            'help-delivery'       => 'Yetkazib berish',
            'help-exchange'       => 'Almashtirish',
            'help-return'         => 'Qaytarish',
            'help-service'        => 'Servis markazi',
            'buy'                 => 'Qayerdan sotib olish',
            'partners'            => 'Hamkorlarga',
            'contacts'            => 'Kontaktlar',
        ];
    }

    /** @return array<string, string> UF_CODE → UF_VALUE_UZ */
    private function translationSeeds(): array
    {
        return [
            // header — language switch
            'header.lang.ru' => 'Rus',
            'header.lang.en' => 'O\'zb', // ключ оставляем как есть (en) — историческое имя; значение уже узбекское
            'header.lang.uz' => 'O\'zb',

            // header nav
            'header.catalog'         => 'Katalog',
            'header.nav.brand'       => 'Brend haqida',
            'header.nav.help'        => 'Yordam',
            'header.nav.buy'         => 'Qayerdan sotib olish',
            'header.nav.partners'    => 'Hamkorlarga',
            'header.nav.contacts'    => 'Kontaktlar',
            'header.cart'            => 'Savatcha',

            // footer
            'footer.description'     => 'My Gree Group — O\'zbekistondagi Gree rasmiy distribyutori',
            'footer.col.catalog'     => 'Katalog',
            'footer.nav.wall'        => 'Devorga o\'rnatiladigan',
            'footer.nav.column'      => 'Ustun',
            'footer.nav.industrial'  => 'Sanoat',
            'footer.col.company'     => 'Kompaniya',
            'footer.nav.about'       => 'Brend haqida',
            'footer.nav.payment'     => 'To\'lov',
            'footer.nav.delivery'    => 'Yetkazib berish',
            'footer.nav.exchange'    => 'Almashtirish',
            'footer.nav.return'      => 'Qaytarish',
            'footer.nav.service'     => 'Servis markazi',

            // breadcrumbs
            'breadcrumbs.home'       => 'Bosh sahifa',
            'breadcrumbs.catalog'    => 'Katalog',
            'breadcrumbs.wall'       => 'Devorga o\'rnatiladigan konditsionerlar',
            'breadcrumbs.cart'       => 'Savatcha',
            'breadcrumbs.order'      => 'Buyurtma rasmiylashtirish',

            // 404
            '404.title'              => 'Sahifa topilmadi',
            '404.description'        => 'Siz so\'ragan sahifa mavjud emas yoki o\'chirilgan.',
            '404.home_link'          => 'Bosh sahifaga',

            // catalog
            'catalog.title'              => 'Gree devorga o\'rnatiladigan konditsionerlar katalogi',
            'catalog.description'        => 'Uyingiz yoki ofisingizda qulay iqlim uchun zamonaviy devorga o\'rnatiladigan konditsionerlar.',
            'catalog.filters'            => 'Filtrlar',
            'catalog.filter.type'        => 'Turi',
            'catalog.filter.price'       => 'Narx',
            'catalog.filter.area'        => 'Maydon',
            'catalog.filter.inverter'    => 'Invertorli dvigatel',
            'catalog.filter.bestseller'  => 'Eng ko\'p sotilgan',
            'catalog.filter.color'       => 'Rang',
            'catalog.filter.refrigerant' => 'Sovutuvchi turi',
            'catalog.filter.functions'   => 'Funksiyalar',
            'catalog.filter.yes'         => 'Ha',
            'catalog.filter.no'          => 'Yo\'q',
            'catalog.reset'              => 'Hammasini tozalash',
            'catalog.found'              => ':count ta model topildi',
            'catalog.area.up_to'         => ':area m² gacha',
            'catalog.sort.popular'       => 'Mashhurligi bo\'yicha',
            'catalog.sort.price_asc'     => 'Narx (o\'sish bo\'yicha)',
            'catalog.sort.price_desc'    => 'Narx (kamayish bo\'yicha)',

            // catalog landing pages
            'catalog.section.wall.title'         => 'Gree devorga o\'rnatiladigan konditsionerlar katalogi',
            'catalog.section.wall.description'   => 'Uyingiz yoki ofisingizda qulay iqlim uchun zamonaviy devorga o\'rnatiladigan konditsionerlar.',
            'catalog.section.column.title'       => 'Gree ustun konditsionerlar katalogi',
            'catalog.section.column.description' => 'Do\'konlar, ofislar va katta mehmonxonalar uchun ustun konditsionerlari — 200 m² gacha qoplash.',
            'catalog.section.industrial.title'   => 'Gree sanoat konditsionerlari katalogi',
            'catalog.section.industrial.description' => 'Har qanday maydon va murakkablikdagi xonalarning sanoat iqlim nazorati.',

            // function checkboxes
            'function.wifi'           => 'Wi-Fi',
            'function.130v'           => '130V dan ishlash',
            'function.energy_saving'  => 'Energiya tejash',
            'function.energy-saving'  => 'Energiya tejash',
            'function.turbo'          => 'Turbo rejim',
            'function.silent'         => 'Sokin rejim',
            'function.eco'            => 'Eko rejim',
            'function.smart_home'     => 'Aqlli uy',
            'function.smart-home'     => 'Aqlli uy',
            'function.ai'             => 'Sun\'iy intellekt',

            // product types
            'product.type.wall'       => 'Devorga o\'rnatiladigan',
            'product.type.column'     => 'Ustun',
            'product.type.industrial' => 'Sanoat',
            'product.types.wall'       => 'Devorga o\'rnatiladigan',
            'product.types.column'     => 'Ustun',
            'product.types.industrial' => 'Sanoat',

            // product card / details
            'product.bestseller'  => 'Eng ko\'p sotilgan',
            'product.area'        => 'Maydon — :area m²',
            'product.price_from'  => ':price UZS dan',
            'product.details'     => 'Batafsil',
            'product.add_to_cart' => 'Savatchaga qo\'shish',
            'product.article'     => 'Artikul',
            'product.in_stock'    => 'Mavjud',
            'product.model'       => 'Model',
            'product.color'       => 'Rang',
            'product.power_area'  => 'Quvvat (qoplash maydoni)',
            'product.price'       => 'Narx',
            'product.help'        => 'Yordam kerakmi',
            'product.area_unit'   => ':area m² gacha',
            'product.not_found.title' => 'Mahsulot topilmadi',
            'product.not_found.back'  => 'Katalogga qaytish',
            'product.tab.specs'        => 'Texnik xususiyatlar',
            'product.tab.functions'    => 'Funksiyalar',
            'product.tab.kit'          => 'Komplektatsiya',
            'product.tab.warranty'     => 'Kafolat',
            'product.tab.installation' => 'O\'rnatish',

            // spec labels
            'spec.type'                 => 'Turi',
            'spec.power_area'           => 'Quvvat (qoplash maydoni)',
            'spec.cooling_power'        => 'Sovutish quvvati',
            'spec.heating_power'        => 'Isitish quvvati',
            'spec.energy_class'         => 'Energiya samaradorligi sinfi',
            'spec.noise'                => 'Shovqin darajasi (ichki blok)',
            'spec.area'                 => 'Xona maydoni',
            'spec.indoor_dimensions'    => 'Ichki blok o\'lchamlari',
            'spec.outdoor_dimensions'   => 'Tashqi blok o\'lchamlari',
            'spec.indoor_weight'        => 'Ichki blok vazni',
            'spec.outdoor_weight'       => 'Tashqi blok vazni',
            'spec.refrigerant'          => 'Sovutuvchi',
            'spec.inverter'             => 'Invertor',
            'spec.inverter.yes'         => 'Ha',
            'spec.inverter.no'          => 'Yo\'q',
            'spec.kit'                  => 'Komplektatsiya',
            'spec.specs'                => 'Texnik xususiyatlar',
            'spec.installation'         => 'O\'rnatish',

            // colors
            'color.white'     => 'Oq',
            'color.silver'    => 'Kumush rang',
            'color.black'     => 'Qora',
            'color.champagne' => 'Shampan',

            // home page
            'home.anchor.wall'                  => 'Devorga o\'rnatiladigan',
            'home.anchor.column'                => 'Ustun',
            'home.anchor.industrial'            => 'Sanoat',
            'home.anchor.wall.range'            => '80 m² gacha',
            'home.anchor.column.range'          => '200 m² gacha',
            'home.anchor.industrial.range'      => '100 m² dan',
            'home.tech.description'             => 'Gree konditsionerlarining asosiy afzalliklari',
            'home.section.wall.title'           => 'Devorga o\'rnatiladigan konditsionerlar',
            'home.section.wall.desc'            => '80 m² gacha bo\'lgan maydonlar uchun mos',
            'home.section.column.title'         => 'Ustun konditsionerlar',
            'home.section.column.desc'          => '200 m² gacha bo\'lgan maydonlar uchun mos',
            'home.section.industrial.title'     => 'Sanoat konditsionerlari',
            'home.section.industrial.desc'      => 'Har qanday maydon va murakkablikdagi xonalarning iqlim nazorati — individual loyiha bo\'yicha',
            'home.section.viewAll'              => 'Barcha modellarni ko\'rish',
            'home.gree.title'                   => 'Nima uchun Gree ni tanlashadi',
            'home.gree.description'             => 'Gree — o\'z texnologiyalari, qat\'iy sifat nazorati va har qanday foydalanish stsenariysi uchun yechimlarga ega bo\'lgan konditsioner ishlab chiqarish bo\'yicha jahon lideri.',
            'home.gree.cta'                     => 'Gree haqida ko\'proq bilib oling',
            'home.app.title'                    => 'Konditsioneringizni smartfondan boshqaring',
            'home.app.description'              => 'Harorat, rejim, taymer va ventilyator tezligini dunyoning istalgan nuqtasidan o\'zgartiring. Qulay boshqaruv va nazorat.',
            'home.tech.title'                   => 'Sizning qulayligingiz uchun texnologiyalar',
            'brand.about.title'                 => 'Gree kompaniyasi haqida',
            'home.tips.title'                   => 'Uyingiz uchun foydali maslahatlar',
            'home.tips.subtitle'                => 'Gree mutaxassislaridan foydali maslahatlar va yangiliklar',
            'home.help.title'                   => 'Yordam kerakmi',

            // gree cards
            'gree.card.warranty.title'      => 'Kafolat',
            'gree.card.warranty.desc'       => 'Texnikamiz sifatiga ishonamiz va kengaytirilgan kafolat beramiz',
            'gree.card.delivery.title'      => 'Yetkazib berish',
            'gree.card.delivery.desc'       => 'Shahar bo\'ylab bepul yetkazib berish',
            'gree.card.installment.title'   => 'Bo\'lib to\'lash',
            'gree.card.installment.desc'    => 'Komfortni hozir oling, keyin to\'lang',
            'gree.card.service.title'       => 'Servis markazi',
            'gree.card.service.desc'        => 'O\'z servis markazi — barcha masalalarni tezda hal qilamiz',

            // stats
            'home.stat.world_1'           => 'Dunyoda №1',
            'home.stat.world_1.desc'      => '2024 yilda split-tizimlar ishlab chiqarish bo\'yicha',
            'home.stat.tech'              => '46 texnologiya',
            'home.stat.tech.desc'         => 'Boshqa brendlar o\'z konditsionerlarida foydalanmoqda',
            'home.stat.factories'         => '18 zavod',
            'home.stat.factories.desc'    => 'Butun dunyo bo\'ylab, shuningdek 1411 laboratoriya',

            // blog
            'blog.title'              => 'Foydali maslahatlar',
            'blog.read_more'          => 'Batafsil',
            'blog.show_more'          => 'Ko\'proq ko\'rsatish',
            'blog.news'               => 'Yangiliklar',
            'blog.section'            => 'Blog',
            'blog.hero.title'         => 'Gree blog',
            'blog.hero.description'   => 'Uyingiz uchun Gree mutaxassislaridan foydali maslahatlar va yangiliklar.',
            'blog.reading_minutes'    => ':minutes daq. o\'qish',

            // cart
            'cart.title'                  => 'Savatcha',
            'cart.empty'                  => 'Savatchangiz bo\'sh',
            'cart.checkout'               => 'Rasmiylashtirish',
            'cart.item.color'             => 'Rang',
            'cart.item.area'              => 'Qoplash maydoni',
            'cart.item.qty'               => 'Miqdor',
            'cart.item.area_unit'         => ':area m² gacha',
            'cart.summary.title'          => 'Buyurtmangiz',
            'cart.summary.items'          => ':count ta mahsulot',
            'cart.summary.delivery'       => 'Yetkazib berish',
            'cart.summary.delivery_free'  => 'Bepul',
            'cart.summary.total'          => 'Jami',

            // order
            'order.title'                          => 'Buyurtma rasmiylashtirish',
            'order.contacts.title'                 => 'Kontaktlar',
            'order.contacts.name'                  => 'Ism',
            'order.contacts.name_placeholder'      => 'Ismingizni kiriting',
            'order.contacts.phone'                 => 'Telefon raqami',
            'order.contacts.phone_placeholder'     => '+998 98 123 45 67',
            'order.contacts.telegram'              => 'Telegram',
            'order.contacts.telegram_placeholder'  => '@telegram_nikingiz',
            'order.delivery.title'                 => 'Yetkazib berish',
            'order.delivery.note'                  => 'Toshkent bo\'ylab podyezdgacha bepul yetkazib berish (1 kun). O\'zbekistonning boshqa shaharlari uchun menejer buyurtmani tasdiqlashda muddat va narxni aniqlaydi.',
            'order.delivery.city'                  => 'Shahar',
            'order.delivery.city.tashkent'         => 'Toshkent',
            'order.delivery.street'                => 'Yetkazib berish manzili',
            'order.delivery.street_placeholder'    => 'Ko\'cha nomini kiriting',
            'order.delivery.house'                 => 'Uy',
            'order.delivery.house_placeholder'     => 'Uy raqami',
            'order.delivery.apartment'             => 'Kvartira',
            'order.delivery.apartment_placeholder' => 'Bo\'lsa kiriting — ixtiyoriy',
            'order.delivery.comment'               => 'Izoh',
            'order.delivery.comment_placeholder'   => 'Menejer uchun izoh qoldiring — ixtiyoriy',
            'order.payment.title'                  => 'To\'lov usuli',
            'order.payment.card'                   => 'Karta bilan to\'lov (Humo, Uzcard, Visa, MasterCard)',
            'order.payment.uzum_bank'              => 'Uzum Bank dan bo\'lib to\'lash',
            'order.payment.anor_bank'              => 'Anorbank dan bo\'lib to\'lash',
            'order.sidebar.title'                  => 'Buyurtmangiz',
            'order.sidebar.total'                  => 'Jami',
            'order.sidebar.submit'                 => 'To\'lovga o\'tish',
            'order.error.empty_cart'               => 'Savatchangiz bo\'sh — rasmiylashtirishdan oldin mahsulot qo\'shing.',
            'order.error.invalid'                  => 'Formaning to\'g\'ri to\'ldirilganligini tekshiring.',
            'order.error.network'                  => 'Ulanish xatosi. Qayta urinib ko\'ring.',
            'order_success.title'                  => 'Buyurtmangiz uchun rahmat!',
            'order_success.description'            => 'Menejerimiz 15 daqiqa ichida (ish soatlarida) tafsilotlarni aniqlashtirish uchun siz bilan bog\'lanadi.',
            'order_success.cta'                    => 'Bosh sahifaga qaytish',
            'order_success.signature'              => 'Sizning Gree',
        ];
    }

    /** @return array<string, array<string, string>> page CODE → массив UF_*_UZ полей */
    private function seoSeeds(): array
    {
        return [
            'home' => [
                'UF_TITLE_UZ'          => 'Gree O\'zbekiston — +50 dan −30 °C gacha iqlim uchun mukammal konditsionerlar',
                'UF_DESCRIPTION_UZ'    => 'Dunyodagi har uchinchi konditsioner — Gree. 2024 yilda split-tizimlar ishlab chiqarish bo\'yicha №1 brend. O\'zbekiston bo\'ylab yetkazib berish, invertorga 10 yillik kafolat, o\'z servis markazi.',
                'UF_KEYWORDS_UZ'       => 'gree, konditsioner, split-tizim, invertor, o\'zbekiston, toshkent, sovutish, isitish',
                'UF_OG_TITLE_UZ'       => 'O\'zbekiston uchun mukammal Gree konditsionerlari',
                'UF_OG_DESCRIPTION_UZ' => '+50 °C da sovutadi, −30 °C da isitadi. Invertorga 10 yillik kafolat.',
            ],
            'catalog' => [
                'UF_TITLE_UZ'          => 'Gree split-tizimlar katalogi — devorga, ustun, sanoat',
                'UF_DESCRIPTION_UZ'    => 'Devorga o\'rnatiladigan (80 m² gacha), ustun (200 m² gacha) va sanoat Gree konditsionerlari. Narx, quvvat va rang bo\'yicha filtr. O\'zbekiston bo\'ylab yetkazib berish.',
                'UF_KEYWORDS_UZ'       => 'gree katalog, split-tizim, devorga, ustun, sanoat, konditsioner',
                'UF_OG_TITLE_UZ'       => 'Gree split-tizimlar katalogi',
                'UF_OG_DESCRIPTION_UZ' => 'Devorga, ustun va sanoat modellari — har qanday xona maydoni uchun tanlov.',
            ],
            'catalog-nastennie' => [
                'UF_TITLE_UZ'          => 'Gree devorga o\'rnatiladigan konditsionerlar katalogi — 80 m² gacha',
                'UF_DESCRIPTION_UZ'    => 'Kvartira va ofislar uchun Gree devorga o\'rnatiladigan split-tizimlari, 80 m² gacha. Invertor, Wi-Fi, o\'z-o\'zini tozalash. Invertorga 10 yillik kafolat, O\'zbekiston bo\'ylab bepul yetkazib berish.',
                'UF_KEYWORDS_UZ'       => 'devorga konditsioner, gree, split-tizim, invertor, wi-fi, o\'z-o\'zini tozalash',
                'UF_OG_TITLE_UZ'       => 'Gree devorga o\'rnatiladigan konditsionerlar',
                'UF_OG_DESCRIPTION_UZ' => '80 m² gacha bo\'lgan maydonlar uchun. Invertor, sokin rejim, o\'z-o\'zini tozalash.',
            ],
            'catalog-kolonnye' => [
                'UF_TITLE_UZ'          => 'Gree ustun konditsionerlar katalogi — 200 m² gacha',
                'UF_DESCRIPTION_UZ'    => '200 m² gacha xonalar uchun Gree pol-shifti ustun konditsionerlari. Savdo zallari, restoranlar, konferentsiya xonalari. O\'zbekiston bo\'ylab bepul yetkazib berish.',
                'UF_KEYWORDS_UZ'       => 'ustun konditsioner, pol-shifti, gree, tijorat',
                'UF_OG_TITLE_UZ'       => 'Gree ustun konditsionerlar',
                'UF_OG_DESCRIPTION_UZ' => '200 m² gacha xonalar uchun — savdo, HoReCa, ofislar.',
            ],
            'catalog-promyshlennye' => [
                'UF_TITLE_UZ'          => 'Gree sanoat konditsionerlari katalogi — VRF, chillerlar',
                'UF_DESCRIPTION_UZ'    => 'Gree sanoat konditsionerlari — har qanday maydon va murakkablikdagi xonalarning iqlim nazorati, individual loyiha bo\'yicha. VRF, chillerlar, precision tizimlar.',
                'UF_KEYWORDS_UZ'       => 'sanoat konditsioner, vrf, chiller, gree, hvac, iqlim nazorati',
                'UF_OG_TITLE_UZ'       => 'Gree sanoat konditsionerlari',
                'UF_OG_DESCRIPTION_UZ' => 'Har qanday murakkablikdagi iqlim nazorati individual loyiha bo\'yicha.',
            ],
            'brand' => [
                'UF_TITLE_UZ'          => 'Gree brendi haqida — 2024 yilda split-tizimlar ishlab chiqarish bo\'yicha dunyoda №1',
                'UF_DESCRIPTION_UZ'    => 'Gree 1991 yildan beri: butun dunyo bo\'ylab 18 zavod, 1411 laboratoriya, 46 yetakchi texnologiya, 90 000 xodim. Dunyodagi har uchinchi konditsioner — Gree.',
                'UF_KEYWORDS_UZ'       => 'gree, brend, tarix, ishlab chiqarish, texnologiyalar, 1991',
                'UF_OG_TITLE_UZ'       => 'Gree brendi haqida',
                'UF_OG_DESCRIPTION_UZ' => 'Iqlim jihozlarining jahon lideri. 1991 — bugun.',
            ],
            'blog' => [
                'UF_TITLE_UZ'          => 'Gree blog — uyingiz uchun foydali maslahatlar va yangiliklar',
                'UF_DESCRIPTION_UZ'    => 'Uyingiz uchun Gree mutaxassislaridan foydali maslahatlar va yangiliklar. Konditsionerlarni tanlash, o\'rnatish, xizmat ko\'rsatish. Kompaniya va bozor yangiliklari.',
                'UF_KEYWORDS_UZ'       => 'gree, blog, maslahatlar, yangiliklar, konditsioner, xizmat ko\'rsatish',
                'UF_OG_TITLE_UZ'       => 'Gree blog',
                'UF_OG_DESCRIPTION_UZ' => 'Uyingiz uchun Gree mutaxassislaridan foydali maslahatlar va yangiliklar.',
            ],
            'cart' => [
                'UF_TITLE_UZ'          => 'Savatcha — Gree O\'zbekiston',
                'UF_DESCRIPTION_UZ'    => 'Gree konditsionerlar buyurtmangiz. Toshkent bo\'ylab bepul yetkazib berish, Humo/Uzcard/Visa/MasterCard karta to\'lovi, ANORBANK va UZUM bo\'lib to\'lash.',
                'UF_KEYWORDS_UZ'       => 'savatcha, buyurtma, gree, yetkazib berish, bo\'lib to\'lash, toshkent',
                'UF_OG_TITLE_UZ'       => 'Savatcha',
                'UF_OG_DESCRIPTION_UZ' => '',
            ],
            'order' => [
                'UF_TITLE_UZ'          => 'Buyurtma rasmiylashtirish — Gree O\'zbekiston',
                'UF_DESCRIPTION_UZ'    => 'Yetkazib berish ma\'lumotlarini to\'ldiring va to\'lov usulini tanlang. Toshkent bo\'ylab bepul yetkazib berish. ANORBANK yoki UZUM dan bo\'lib to\'lash.',
                'UF_KEYWORDS_UZ'       => 'buyurtma rasmiylashtirish, gree, yetkazib berish, to\'lov, toshkent, o\'zbekiston',
                'UF_OG_TITLE_UZ'       => 'Gree buyurtma rasmiylashtirish',
                'UF_OG_DESCRIPTION_UZ' => '',
            ],
            'order_success' => [
                'UF_TITLE_UZ'          => 'Buyurtma qabul qilindi — Gree O\'zbekiston',
                'UF_DESCRIPTION_UZ'    => 'Rahmat! Menejerimiz ish soatlarida 15 daqiqa ichida siz bilan bog\'lanadi.',
                'UF_KEYWORDS_UZ'       => 'buyurtma qabul qilindi, gree, rahmat',
                'UF_OG_TITLE_UZ'       => 'Buyurtmangiz uchun rahmat!',
                'UF_OG_DESCRIPTION_UZ' => '',
            ],
        ];
    }

    /**
     * Узбекские спеки для оффера, зависящие от площади (копируем формат из
     * Version20260516000011 — там идёт RU/EN, мы заменяем EN на UZ).
     *
     * @return array<string, string>
     */
    private function offerSpecsByArea(int $area): array
    {
        $cool = max(2.2, round($area * 0.11, 1));
        $heat = max(2.5, round($area * 0.12, 1));

        $indoorDims = match (true) {
            $area <= 25 => '740 × 285 × 187 mm',
            $area <= 50 => '960 × 327 × 230 mm',
            default     => '1101 × 327 × 249 mm',
        };
        $outdoorDims = match (true) {
            $area <= 25 => '720 × 495 × 270 mm',
            $area <= 50 => '848 × 540 × 320 mm',
            default     => '958 × 660 × 402 mm',
        };
        $indoorWeight = match (true) {
            $area <= 25 => '9 kg',
            $area <= 50 => '12 kg',
            default     => '15 kg',
        };
        $outdoorWeight = match (true) {
            $area <= 25 => '23 kg',
            $area <= 50 => '32 kg',
            default     => '43 kg',
        };
        $noise = match (true) {
            $area <= 25 => '20-42 dB',
            $area <= 50 => '22-45 dB',
            default     => '24-48 dB',
        };

        return [
            'COOLING_POWER_UZ'      => $cool . ' kVt',
            'HEATING_POWER_UZ'      => $heat . ' kVt',
            'NOISE_UZ'              => $noise,
            'INDOOR_DIMENSIONS_UZ'  => $indoorDims,
            'OUTDOOR_DIMENSIONS_UZ' => $outdoorDims,
            'INDOOR_WEIGHT_UZ'      => $indoorWeight,
            'OUTDOOR_WEIGHT_UZ'     => $outdoorWeight,
        ];
    }
}
