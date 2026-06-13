<?php

namespace Sprint\Migration;

/**
 * Переводы → длинный текст.
 *
 * 1) HL «Translations»: UF_VALUE_RU / UF_VALUE_UZ переводятся со string (VARCHAR
 *    255, single-line input) на text (TEXT, multiline textarea). После наката
 *    редакторы могут писать длинные тексты с переносами строк и HTML-тегами
 *    типа <br> прямо в значении перевода.
 *
 * 2) Обновляются два конкретных перевода под обновлённую вёрстку:
 *    - home.app.description — был обрезан, дописан полностью «...энергопотребления
 *      в одном приложении.»
 *    - footer.description   — после «официальный»/«rasmiy» добавлен <br> для
 *      переноса в подвале.
 *
 * Изменение SQL-типа делается через прямой ALTER TABLE (через
 * \Bitrix\Main\Application::getConnection()) — Bitrix-хелперы для HL UserField
 * не умеют менять column type без re-create поля.
 */
class Version20260608000002 extends Version
{
    protected $description = "Translations UF_VALUE_RU/UZ → TEXT + апдейт home.app.description / footer.description";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'home.app.description' => [
            'ru' => 'Меняйте температуру, режим, таймер и скорость вентиляции из любой точки мира. Удобное управление и контроль энергопотребления в одном приложении.',
            'uz' => "Harorat, rejim, taymer va ventilyator tezligini dunyoning istalgan nuqtasidan o'zgartiring. Bitta ilovada qulay boshqaruv va energiya iste'molini nazorat qilish.",
        ],
        'footer.description'   => [
            'ru' => 'My Gree Group — официальный<br>дистрибьютор Gree в Узбекистане',
            'uz' => "My Gree Group — O'zbekistondagi<br>Gree rasmiy distribyutori",
        ],
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
        $tableName = $hl['TABLE_NAME'];

        // 1. ALTER TABLE — VARCHAR(255) → TEXT для обоих языковых колонок.
        $conn = \Bitrix\Main\Application::getConnection();
        $helperSql = $conn->getSqlHelper();
        $table = $helperSql->quote($tableName);
        foreach (['UF_VALUE_RU', 'UF_VALUE_UZ'] as $col) {
            $colQuoted = $helperSql->quote($col);
            $conn->queryExecute("ALTER TABLE {$table} MODIFY {$colQuoted} TEXT NULL");
        }
        $this->outSuccess('SQL-тип UF_VALUE_RU/UZ → TEXT');

        // 2. Поправить настройки UserField — увеличить ROWS, чтобы в админке
        //    рисовалась textarea, а не однострочный input. UserFieldTable::update
        //    бросает NotImplementedException — Bitrix требует API \CUserTypeEntity.
        $cuf = new \CUserTypeEntity();
        foreach (['UF_VALUE_RU', 'UF_VALUE_UZ'] as $field) {
            $fieldRow = \Bitrix\Main\UserFieldTable::getList([
                'filter' => ['=ENTITY_ID' => 'HLBLOCK_' . $hlblockId, '=FIELD_NAME' => $field],
                'select' => ['ID', 'SETTINGS'],
            ])->fetch();
            if (!$fieldRow) {
                continue;
            }
            $settings = is_array($fieldRow['SETTINGS']) ? $fieldRow['SETTINGS'] : [];
            $settings['ROWS'] = 10;
            $settings['SIZE'] = 80;
            $cuf->Update((int) $fieldRow['ID'], ['SETTINGS' => $settings]);
        }
        $this->outSuccess('UserField settings: ROWS=10, SIZE=80');

        // 3. Upsert значений переводов.
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
        $this->outSuccess('Переводы upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла: тип TEXT обратной совместимости с VARCHAR(255) не нарушает, '
            . 'а апдейт текста — контентная правка');
    }
}
