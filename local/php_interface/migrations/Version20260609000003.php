<?php

namespace Sprint\Migration;

/**
 * Перевод `product.types_heading` — заголовок блока с переключателем типов
 * товара (.catalog__title) на детальной товара. Раньше там показывалось
 * имя текущего типа («Настенные»/«Колонные»/...), но фронт по эталону
 * dist/product.html ожидает статичный «Кондиционеры».
 *
 * Идемпотентно: upsert по UF_CODE.
 */
class Version20260609000003 extends Version
{
    protected $description = "Перевод product.types_heading (заголовок блока типов на детальной товара)";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'product.types_heading' => ['ru' => 'Кондиционеры', 'uz' => 'Konditsionerlar'],
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
        $this->outSuccess('Перевод product.types_heading upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — без перевода заголовок блока типов будет пуст');
    }
}
