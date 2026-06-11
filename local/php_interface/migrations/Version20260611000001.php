<?php

namespace Sprint\Migration;

/**
 * Обновление заголовка секции адресов на /contacts/ под новый dist (06-11):
 * «Адреса» → «Или приходите в один из шоурумов» (эталон dist/contacts.html).
 *
 * Идемпотентно: upsert по UF_CODE.
 */
class Version20260611000001 extends Version
{
    protected $description = "contacts.section.addresses.title → «Или приходите в один из шоурумов»";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'contacts.section.addresses.title' => [
            'ru' => 'Или приходите в один из шоурумов',
            'uz' => "Yoki shourumlardan biriga tashrif buyuring",
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
        $this->outSuccess('Откат контентной правки не требуется');
    }
}
