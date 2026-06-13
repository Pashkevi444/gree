<?php

namespace Sprint\Migration;

/**
 * YAGNI: сносим общий HL «Feedback» и связанный сидер UI-переводов
 * (feedback.*). Общий универсальный feedback-эндпоинт оказался не нужен —
 * для каждой формы заводится отдельный HL (сейчас единственный — HL
 * «CatalogHelpFeedback» под форму «Нужна помощь?» на детальной товара).
 *
 * Идемпотентно: deleteHlblockIfExists / удаление по UF_CODE списка.
 */
class Version20260604000012 extends Version
{
    protected $description = "YAGNI: удаление HL «Feedback» и feedback.* переводов";

    /**
     * @var string[] Ключи partner-попапа в HL Translations, обсолетны после
     * удаления partner-feedback-popup. ВНИМАНИЕ: feedback.* НЕ удалять —
     * они используются попапом «Нужна помощь?» на детальной товара (она
     * переехала на отдельный HL CatalogHelpFeedback, но тексты те же).
     */
    private array $obsoleteTranslationCodes = [
        'partner.popup.title',
        'partner.popup.subtitle',
        'partner.popup.company',
        'partner.popup.company_placeholder',
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $this->getHelperManager()->Hlblock()->deleteHlblockIfExists('Feedback');
        $this->purgeTranslations();
        $this->outSuccess('HL «Feedback» удалён, обсолетные переводы вычищены');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — общий feedback больше не нужен');
    }

    private function purgeTranslations(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            return;
        }
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $deleted = 0;
        foreach ($this->obsoleteTranslationCodes as $code) {
            $rs = $dataClass::query()->where('UF_CODE', $code)->setSelect(['ID'])->exec();
            while ($row = $rs->fetch()) {
                $dataClass::delete((int) $row['ID']);
                $deleted++;
            }
        }
        $this->out('  удалено translations-ключей: %d', $deleted);

        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/hl/');
    }
}
