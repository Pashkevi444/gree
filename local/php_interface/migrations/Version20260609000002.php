<?php

namespace Sprint\Migration;

/**
 * Переводы под мобильное бургер-меню (header.php → .header-hamburger-menu):
 *   - language.drawer.title — заголовок sub-drawer-а «Язык сайта».
 *   - language.full.<loc>   — полное название локали («Русский», «Узбекский»),
 *     показывается в кнопке-открывалке drawer-а и в самом списке выбора. Существующий
 *     header.lang.<loc> хранит короткий лейбл («Рус», «Узб») для top-bar — для drawer
 *     короткого мало, нужен длинный.
 *
 * Идемпотентно: upsert по UF_CODE.
 */
class Version20260609000002 extends Version
{
    protected $description = "Переводы для language-drawer мобильного бургер-меню";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'language.drawer.title' => ['ru' => 'Язык сайта',  'uz' => 'Sayt tili'],
        'language.full.ru'      => ['ru' => 'Русский',     'uz' => 'Ruscha'],
        'language.full.uz'      => ['ru' => 'Узбекский',   'uz' => "O'zbekcha"],
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
        $this->outSuccess('Переводы language.* upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — без переводов в drawer будут пустые лейблы');
    }
}
