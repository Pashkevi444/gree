<?php

namespace Sprint\Migration;

/**
 * Переводы для кнопки «Показать ещё / Скрыть» в табе «Функции» детальной
 * товара. Новый dist (06-07) ожидает data-show-text / data-hide-text на
 * .product-functions__button — JS читает их при раскрытии списка функций.
 *
 * Идемпотентно: для каждого UF_CODE — update если есть, add если нет.
 */
class Version20260608000001 extends Version
{
    protected $description = "Переводы под обновлённый dist (06-07): product.functions.* + order.delivery.description";

    /** @var array<string, array{ru: string, uz: string}> */
    private array $entries = [
        'product.functions.show_more' => ['ru' => 'Показать ещё', 'uz' => "Ko'proq ko'rsatish"],
        'product.functions.hide'      => ['ru' => 'Скрыть',       'uz' => 'Yashirish'],
        // Короткое подзаголовочное описание блока «Доставка» в /order/
        // (под title, над content). В эталоне dist/order.html (06-07) появилось
        // как .order-card__description.
        'order.delivery.description'  => [
            'ru' => 'Осуществляется до "пятака" города',
            'uz' => "Shaharning markaziy nuqtasigacha amalga oshiriladi",
        ],
        // Заголовок мобильного drawer-а сортировки в каталоге.
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

        $this->outSuccess('product.functions.* переводы upserted: %d', $upserted);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — кнопка «Показать ещё» без переводов покажет пустую label');
    }
}
