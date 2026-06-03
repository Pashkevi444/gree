<?php

namespace Sprint\Migration;

/**
 * UI-переводы страницы /help/. Карточки секций (data) живут в инфоблоках
 * (Version20260603000001/000002), а заголовки секций, общие подписи и
 * footer-текст вынесены в HL «Translations» как обычные UI-строки.
 *
 * Идемпотентность: если UF_CODE уже есть — пропускаем (на случай повторного
 * прогона), иначе addElement.
 */
class Version20260603000003 extends Version
{
    protected $description = "UI-переводы для страницы /help/";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        // ── breadcrumbs / page hero ─────────────────────────────────────────
        'breadcrumbs.help' => ['ru' => 'Помощь', 'uz' => 'Yordam'],

        // ── section titles + descriptions ───────────────────────────────────
        'help.section.payment.title'  => ['ru' => 'Способы оплаты', 'uz' => 'To\'lov usullari'],
        'help.section.payment.description' => [
            'ru' => 'Вы можете оплатить наличными, мы принимаем карты Humo, Uzcard, Visa, MasterCard, а также вы можете оформить рассрочку от ANORBANK и UZUM.',
            'uz' => 'Naqd pul bilan to\'lashingiz mumkin, biz Humo, Uzcard, Visa, MasterCard kartalarini qabul qilamiz, shuningdek, ANORBANK va UZUM dan bo\'lib to\'lashni rasmiylashtirishingiz mumkin.',
        ],

        'help.section.delivery.title' => ['ru' => 'Доставка', 'uz' => 'Yetkazib berish'],
        'help.section.delivery.description' => [
            'ru' => 'Бесплатно доставим по Ташкенту, чтобы вы могли начать пользоваться кондиционерами Gree как можно скорее',
            'uz' => 'Gree konditsionerlaridan imkon qadar tezroq foydalanishingiz uchun Toshkent bo\'ylab bepul yetkazib beramiz',
        ],

        'help.section.exchange.title' => ['ru' => 'Как обменять товар', 'uz' => 'Mahsulotni qanday almashtirish'],
        'help.section.exchange.description' => [
            'ru' => 'В случае покупки товара ненадлежащего качества, который не подошел по тем или иным причинам, клиент может сделать обмен в течение 10 дней со дня покупки товара.',
            'uz' => 'Sifatsiz yoki qaysidir sababga ko\'ra mos kelmagan mahsulotni sotib olgan taqdirda, mijoz mahsulot xarid qilingan kundan boshlab 10 kun ichida almashtira oladi.',
        ],

        'help.section.refund.title' => ['ru' => 'Как вернуть товар', 'uz' => 'Mahsulotni qanday qaytarish'],
        'help.section.refund.description' => [
            'ru' => 'В случае покупки товара ненадлежащего качества, который не подошел по тем или иным причинам, клиент может сделать возврат в течение 10 дней со дня покупки товара.',
            'uz' => 'Sifatsiz yoki qaysidir sababga ko\'ra mos kelmagan mahsulotni sotib olgan taqdirda, mijoz mahsulot xarid qilingan kundan boshlab 10 kun ichida qaytara oladi.',
        ],

        'help.section.service.title' => ['ru' => 'Единый сервисный центр по всему Узбекистану', 'uz' => 'O\'zbekiston bo\'ylab yagona servis markazi'],
        'help.section.service.description' => [
            'ru' => 'Высочайший уровень сервиса — это приоритет для нас. Опытные специалисты готовы ответить на любой вопрос по установке, использованию и сервисному обслуживанию.',
            'uz' => 'Eng yuqori darajadagi xizmat — bu biz uchun ustuvor vazifa. Tajribali mutaxassislar o\'rnatish, foydalanish va servis xizmati bo\'yicha har qanday savolga javob berishga tayyor.',
        ],

        // ── footer текст в секции «Как вернуть» ─────────────────────────────
        'help.refund.footer.prefix' => [
            'ru' => 'Также вы можете проконсультироваться по телефону нашего call-центра по номеру',
            'uz' => 'Shuningdek, call-markazimizning telefon raqami orqali maslahat olishingiz mumkin',
        ],
        'help.refund.footer.phone' => ['ru' => '+998 71 500 00 00', 'uz' => '+998 71 500 00 00'],
        'help.refund.footer.suffix' => [
            'ru' => 'Наши операторы ответят на все ваши вопросы и помогут разобраться с ситуацией.',
            'uz' => 'Operatorlarimiz barcha savollaringizga javob berishadi va vaziyatni hal qilishda yordam berishadi.',
        ],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL «Translations» не найден — сначала Version20260515000001');
            return;
        }

        $existing = $this->existingCodes($hlblockId);
        $added = 0;
        foreach ($this->entries as $code => $values) {
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
        $this->outSuccess('UI-переводы help: добавлено %d / всего в сидере %d', $added, count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    /** @return string[] */
    private function existingCodes(int $hlblockId): array
    {
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();
        $codes = [];
        $rows = $dataClass::query()->setSelect(['UF_CODE'])->exec();
        while ($row = $rows->fetch()) {
            $codes[] = (string) ($row['UF_CODE'] ?? '');
        }
        return $codes;
    }
}
