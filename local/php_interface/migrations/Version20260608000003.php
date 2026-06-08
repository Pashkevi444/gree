<?php

namespace Sprint\Migration;

/**
 * Перевод `catalog.sort.title` для заголовка мобильного drawer-а сортировки
 * и для текста кнопки «Сортировка» в .catalog-header-mobile-buttons.
 *
 * Ключ был добавлен задним числом в Version20260608000001 в массив $entries,
 * но к моменту правки эта миграция уже была накатана на dev — Sprint не
 * перезапускает её. Отдельная мини-миграция, идемпотентная (upsert).
 */
class Version20260608000003 extends Version
{
    protected $description = "Перевод catalog.sort.title (мобильный drawer + кнопка)";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'catalog.sort.title' => ['ru' => 'Сортировка', 'uz' => 'Saralash'],
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

        $this->outSuccess('Переводы catalog.sort.* upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — без перевода drawer/кнопка покажут пустую строку');
    }
}
