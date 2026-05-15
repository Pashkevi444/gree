<?php

namespace Sprint\Migration;

/**
 * Добивает HL Translations недостающими ключами:
 *
 *   - Коды функций товара через дефис (function.130v, function.energy-saving,
 *     function.smart-home). На сидерах продуктов FUNCTIONS хранятся как
 *     dashed-коды, а в HL изначально оказались underscore-варианты — это
 *     приводило к тому, что Language::t('function.smart-home') возвращал
 *     сам код вместо лейбла.
 *   - Резервные дефис-варианты для остальных функций — чтобы любая форма
 *     кода ('eco' / 'turbo' / 'silent' и т.д. могла иметь dashed-вариант).
 *
 * Идемпотентна: уже существующие ключи не дублируются.
 */
class Version20260516000012 extends Version
{
    protected $description = "Недостающие переводы: function.130v / -dashed варианты";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'function.130v'           => ['ru' => 'Работа от 130V',          'en' => 'Operates from 130V'],
        'function.energy-saving'  => ['ru' => 'Энергосбережение',         'en' => 'Energy saving'],
        'function.smart-home'     => ['ru' => 'Умный дом',                'en' => 'Smart home'],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL Translations не найден');
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
                'UF_VALUE_EN' => $values['en'],
            ]);
            $added++;
        }

        $this->outSuccess('Добавлено переводов: %d / %d', $added, count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    /**
     * @return string[]
     */
    private function existingCodes(int $hlblockId): array
    {
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();

        $codes = [];
        $rows = $dataClass::query()->setSelect(['UF_CODE'])->exec();
        while ($row = $rows->fetch()) {
            $codes[] = (string) ($row['UF_CODE'] ?? '');
        }
        return $codes;
    }
}
