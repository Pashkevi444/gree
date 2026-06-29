<?php

namespace Sprint\Migration;

/**
 * Highloadblock «Cities» — справочник городов доставки. Поля:
 *   UF_CODE     string  — slug (tashkent, samarkand, ...). Уникален.
 *   UF_NAME_RU  string  — название RU («Ташкент»).
 *   UF_NAME_UZ  string  — название UZ («Toshkent»).
 *   UF_SORT     integer — порядок в селекте (UZ-города сверху, RU-города в конце).
 *
 * Сидинг: 14 областных центров Узбекистана + Москва + Санкт-Петербург.
 * Идемпотентно: HL и поля — saveHlblock/saveField; записи — upsert по UF_CODE.
 */
class Version20260629000001 extends Version
{
    protected $description = "HL «Cities» — справочник городов доставки + сид UZ + Москва/Питер";

    /** @var array<int, array{code: string, ru: string, uz: string, sort: int}> */
    private array $seed = [
        ['code' => 'tashkent',   'ru' => 'Ташкент',         'uz' => 'Toshkent',         'sort' => 100],
        ['code' => 'samarkand',  'ru' => 'Самарканд',       'uz' => 'Samarqand',        'sort' => 200],
        ['code' => 'bukhara',    'ru' => 'Бухара',          'uz' => 'Buxoro',           'sort' => 300],
        ['code' => 'andijan',    'ru' => 'Андижан',         'uz' => 'Andijon',          'sort' => 400],
        ['code' => 'namangan',   'ru' => 'Наманган',        'uz' => 'Namangan',         'sort' => 500],
        ['code' => 'fergana',    'ru' => 'Фергана',         'uz' => "Farg'ona",         'sort' => 600],
        ['code' => 'karshi',     'ru' => 'Карши',           'uz' => 'Qarshi',           'sort' => 700],
        ['code' => 'nukus',      'ru' => 'Нукус',           'uz' => "No'kis",           'sort' => 800],
        ['code' => 'urgench',    'ru' => 'Ургенч',          'uz' => 'Urganch',          'sort' => 900],
        ['code' => 'termez',     'ru' => 'Термез',          'uz' => 'Termiz',           'sort' => 1000],
        ['code' => 'jizzakh',    'ru' => 'Джизак',          'uz' => 'Jizzax',           'sort' => 1100],
        ['code' => 'gulistan',   'ru' => 'Гулистан',        'uz' => 'Guliston',         'sort' => 1200],
        ['code' => 'navoiy',     'ru' => 'Навои',           'uz' => 'Navoiy',           'sort' => 1300],
        ['code' => 'nurafshon',  'ru' => 'Нурафшан',        'uz' => 'Nurafshon',        'sort' => 1400],
        ['code' => 'moscow',     'ru' => 'Москва',          'uz' => 'Moskva',           'sort' => 9100],
        ['code' => 'saint-petersburg', 'ru' => 'Санкт-Петербург', 'uz' => 'Sankt-Peterburg', 'sort' => 9200],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();

        // 1. HL и поля.
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'Cities',
            'TABLE_NAME' => 'gree_cities',
            'LANG'       => [
                'ru' => ['NAME' => 'Города доставки'],
                'en' => ['NAME' => 'Delivery cities'],
            ],
        ]);

        $fields = [
            ['UF_CODE',    'string',  'Код',         'Code',     'Y'],
            ['UF_NAME_RU', 'string',  'Название RU', 'Name RU',  'Y'],
            ['UF_NAME_UZ', 'string',  'Название UZ', 'Name UZ',  'N'],
            ['UF_SORT',    'integer', 'Сортировка',  'Sort',     'N'],
        ];
        foreach ($fields as [$name, $type, $labelRu, $labelEn, $mandatory]) {
            $helper->Hlblock()->saveField('Cities', [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => $type,
                'MANDATORY'         => $mandatory,
                'EDIT_FORM_LABEL'   => ['ru' => $labelRu, 'en' => $labelEn],
                'LIST_COLUMN_LABEL' => ['ru' => $labelRu, 'en' => $labelEn],
            ]);
        }
        $this->outSuccess('HL Cities + поля созданы [id=%d]', $hlblockId);

        // 2. Сидинг.
        $hl = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $cls = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl)->getDataClass();

        $upserted = 0;
        foreach ($this->seed as $city) {
            $row = $cls::query()->where('UF_CODE', $city['code'])->setSelect(['ID'])->exec()->fetch();
            $payload = [
                'UF_CODE'    => $city['code'],
                'UF_NAME_RU' => $city['ru'],
                'UF_NAME_UZ' => $city['uz'],
                'UF_SORT'    => $city['sort'],
            ];
            if ($row) {
                $cls::update((int) $row['ID'], $payload);
            } else {
                $cls::add($payload);
            }
            $upserted++;
        }
        \Bitrix\Main\Application::getInstance()->getCache()->cleanDir('/hl/');
        $this->outSuccess('Сидинг: %d городов', $upserted);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Cities');
        $this->outSuccess('HL Cities удалён');
    }
}
