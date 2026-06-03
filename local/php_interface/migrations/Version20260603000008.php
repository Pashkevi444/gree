<?php

namespace Sprint\Migration;

/**
 * UI-переводы для попапа обратной связи (partials/feedback-popup.blade.php).
 * Попап подключается на странице товара (кнопка «Нужна помощь?») и может
 * быть переиспользован на будущих страницах (contacts/partners).
 *
 * Идемпотентна — добавляет ключи которых ещё нет в HL «Translations».
 */
class Version20260603000008 extends Version
{
    protected $description = "UI-переводы: feedback.* (попап обратной связи)";

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

        $existing = [];
        foreach ($dataClass::query()->setSelect(['UF_CODE'])->exec() as $row) {
            $existing[] = (string) ($row['UF_CODE'] ?? '');
        }

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
        $this->outSuccess('Feedback-переводы: добавлено %d / всего в сидере %d', $added, count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
