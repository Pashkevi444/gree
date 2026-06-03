<?php

namespace Sprint\Migration;

/**
 * Чистка HL «Translations» после повторных прокатов сидер-миграций:
 *
 *   1. Дедуп по UF_CODE — оставляем строку с min(ID), остальные удаляем.
 *      Если у разных дублей разные значения, мерджим: непустое побеждает,
 *      приоритет — у строки с min(ID).
 *   2. Дозаливаем UF_VALUE_RU для ключей где RU пустое, но UZ не пустое
 *      (актуальный кейс header.lang.uz: создано в Version20260519000003
 *      без RU-значения — на ru-локали выводился сам код вместо «Узб»).
 *   3. Сбрасываем файловый ORM-кэш — иначе TranslatorService будет час
 *      сидеть на старом массиве (setCacheTtl=3600).
 */
class Version20260604000010 extends Version
{
    protected $description = "HL Translations: дедуп + RU-fallback для пустых пар";

    /**
     * Известные пары RU для ключей которые залились без русского значения.
     * Применяется только если UF_VALUE_RU реально пустое.
     *
     * @var array<string, string>
     */
    private array $missingRu = [
        'header.lang.uz' => 'Узб',
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

        // 1. Собрать все строки, сгруппировать по UF_CODE
        $byCode = [];
        $rs = $dataClass::query()
            ->setSelect(['ID', 'UF_CODE', 'UF_VALUE_RU', 'UF_VALUE_UZ'])
            ->setOrder(['ID' => 'ASC'])
            ->exec();
        while ($row = $rs->fetch()) {
            $code = (string) ($row['UF_CODE'] ?? '');
            if ($code === '') {
                continue;
            }
            $byCode[$code][] = $row;
        }

        // 2. Дедуп: для каждого code — собираем merged значение, удаляем лишние
        $dropped = 0;
        $merged  = 0;
        foreach ($byCode as $code => $rows) {
            if (count($rows) === 1) {
                continue;
            }
            $first = array_shift($rows);
            $ru = (string) ($first['UF_VALUE_RU'] ?? '');
            $uz = (string) ($first['UF_VALUE_UZ'] ?? '');
            foreach ($rows as $extra) {
                if ($ru === '') {
                    $ru = (string) ($extra['UF_VALUE_RU'] ?? '');
                }
                if ($uz === '') {
                    $uz = (string) ($extra['UF_VALUE_UZ'] ?? '');
                }
                $dataClass::delete((int) $extra['ID']);
                $dropped++;
            }
            $dataClass::update((int) $first['ID'], [
                'UF_VALUE_RU' => $ru,
                'UF_VALUE_UZ' => $uz,
            ]);
            $merged++;
        }
        $this->out('  дедуп: смержено %d ключей, удалено %d дублей', $merged, $dropped);

        // 3. Дозаливка RU там, где он пустой а мы знаем правильное значение
        $filled = 0;
        foreach ($this->missingRu as $code => $ruValue) {
            $row = $dataClass::query()
                ->where('UF_CODE', $code)
                ->setSelect(['ID', 'UF_VALUE_RU'])
                ->exec()
                ->fetch();
            if (!$row || (string) ($row['UF_VALUE_RU'] ?? '') !== '') {
                continue;
            }
            $dataClass::update((int) $row['ID'], ['UF_VALUE_RU' => $ruValue]);
            $filled++;
        }
        $this->out('  дозалито пустых RU: %d', $filled);

        // 4. Сбросить файловый ORM-кэш — TranslatorService иначе будет час
        //    отдавать старое (setCacheTtl=3600 без cacheJoins).
        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/hl/');
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/translations/');

        $this->outSuccess('Translations: дедуп + дозаливка завершены');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — дубли уже снесены');
    }
}
