<?php

namespace Sprint\Migration;

/**
 * Восстанавливает (upsert) переводы для попапа «Нужна помощь?» на детальной
 * товара (partials/feedback-popup.blade.php). Раньше эти ключи лежали в
 * Version20260603000008 — миграция удалена в рамках YAGNI-чистки общего
 * feedback-стека (Version20260604000012). По первой ревизии 0612 заодно
 * сносила и feedback.* — что было ошибкой, popup-форму она оставила без
 * переводов.
 *
 * Идемпотентно: для каждого UF_CODE — update если есть, add если нет.
 */
class Version20260604000013 extends Version
{
    protected $description = "Восстановление переводов feedback.* (попап «Нужна помощь?»)";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'feedback.title'    => ['ru' => 'У вас есть вопросы?',                       'uz' => 'Savollaringiz bormi?'],
        'feedback.subtitle' => ['ru' => 'Мы с радостью поможем вам с выбором',       'uz' => 'Sizga tanlovda yordam berishdan xursand bo\'lamiz'],
        'feedback.name'                    => ['ru' => 'Имя',                  'uz' => 'Ism'],
        'feedback.name_placeholder'        => ['ru' => 'Укажите как вас зовут', 'uz' => 'Ismingizni kiriting'],
        'feedback.phone'                   => ['ru' => 'Номер телефона',        'uz' => 'Telefon raqami'],
        'feedback.phone_placeholder'       => ['ru' => '+998 98 123 45 67',     'uz' => '+998 98 123 45 67'],
        'feedback.submit'                  => ['ru' => 'Отправить',             'uz' => 'Yuborish'],
        'feedback.success.title'           => ['ru' => 'Мы получили вашу заявку', 'uz' => 'Arizangizni qabul qildik'],
        'feedback.success.description'     => [
            'ru' => 'Наш менеджер свяжется с вами для уточнения деталей в течение 15 минут (в рабочее время)',
            'uz' => 'Menejerimiz 15 daqiqa ichida (ish soatlarida) tafsilotlarni aniqlash uchun siz bilan bog\'lanadi',
        ],
        'feedback.success.close'           => ['ru' => 'Закрыть',               'uz' => 'Yopish'],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL «Translations» не найден');
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $upserted = 0;
        foreach ($this->entries as $code => $values) {
            $row = $dataClass::query()->where('UF_CODE', $code)->setSelect(['ID'])->exec()->fetch();
            $fields = [
                'UF_CODE'     => $code,
                'UF_VALUE_RU' => $values['ru'],
                'UF_VALUE_UZ' => $values['uz'],
            ];
            if ($row) {
                $dataClass::update((int) $row['ID'], $fields);
            } else {
                $dataClass::add($fields);
            }
            $upserted++;
        }

        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/hl/');

        $this->outSuccess('Переводы feedback.* восстановлены: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — popup-форма без переводов сломается');
    }
}
