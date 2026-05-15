<?php

namespace Sprint\Migration;

/**
 * Сидит UI-переводы для разводящих страниц секций каталога:
 *   /catalog/nastennie/      → Настенные кондиционеры
 *   /catalog/kolonnye/       → Колонные кондиционеры
 *   /catalog/promyshlennye/  → Промышленные кондиционеры
 *
 * Проверяет существующие коды чтоб не дублировать.
 */
class Version20260516000009 extends Version
{
    protected $description = "Переводы для разводящих /catalog/{section}/";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'catalog.section.wall.title' => [
            'ru' => 'Каталог настенных кондиционеров Gree',
            'en' => 'Gree wall-mounted air conditioners catalog',
        ],
        'catalog.section.wall.description' => [
            'ru' => 'Современные настенные кондиционеры для комфортного климата в вашем доме или офисе.',
            'en' => 'Modern wall-mounted air conditioners for a comfortable climate in your home or office.',
        ],
        'catalog.section.column.title' => [
            'ru' => 'Каталог колонных кондиционеров Gree',
            'en' => 'Gree column air conditioners catalog',
        ],
        'catalog.section.column.description' => [
            'ru' => 'Колонные кондиционеры для магазинов, офисов и больших гостиных — площади до 200 м².',
            'en' => 'Column air conditioners for stores, offices and large living rooms — coverage up to 200 m².',
        ],
        'catalog.section.industrial.title' => [
            'ru' => 'Каталог промышленных кондиционеров Gree',
            'en' => 'Gree industrial air conditioners catalog',
        ],
        'catalog.section.industrial.description' => [
            'ru' => 'Промышленный климат-контроль помещений любых площадей и сложности.',
            'en' => 'Industrial climate control for spaces of any size and complexity.',
        ],
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
