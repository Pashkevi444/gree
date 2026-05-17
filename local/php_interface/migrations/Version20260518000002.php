<?php

namespace Sprint\Migration;

/**
 * Highloadblock «OrderItems» — позиции заказа.
 *
 * Все поля — **снапшоты на момент оформления**. Изменение продукта в каталоге
 * (переименование, удаление, смена цены) не должно править исторические данные
 * заказа. Это стандартный e-commerce паттерн: при чекауте мы фиксируем
 * (UF_PRODUCT_NAME, UF_OFFER_AREA, UF_OFFER_COLOR, UF_UNIT_PRICE) и больше
 * никогда не читаем их из исходного товара.
 *
 *   UF_ORDER_ID       integer  — FK на Orders.ID (SHOW_FILTER=Y, O(1) lookup)
 *   UF_OFFER_ID       integer  — ID торгового предложения (для аналитики/отчётов)
 *   UF_PRODUCT_NAME   string   — snapshot имени товара (на момент заказа)
 *   UF_PRODUCT_CODE   string   — snapshot CODE для перехода обратно в каталог
 *   UF_OFFER_AREA     integer  — площадь
 *   UF_OFFER_COLOR    string   — XML_ID цвета
 *   UF_QUANTITY       integer  — количество, >= 1
 *   UF_UNIT_PRICE     integer  — цена за штуку (UZS) на момент заказа
 *   UF_TOTAL          integer  — qty × unit price (избыточно, но удобно для отчётов)
 *   UF_CREATED_AT     datetime
 */
class Version20260518000002 extends Version
{
    protected $description = "HL-блок «OrderItems» — позиции заказа";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'OrderItems',
            'TABLE_NAME' => 'order_items',
            'LANG'       => [
                'ru' => ['NAME' => 'Позиции заказов'],
                'en' => ['NAME' => 'Order items'],
            ],
        ]);

        $int = static fn(string $name, string $ru, string $en, bool $filter = false): array => [
            'FIELD_NAME'        => $name,
            'USER_TYPE_ID'      => 'integer',
            'MANDATORY'         => 'Y',
            'SHOW_FILTER'       => $filter ? 'Y' : 'N',
            'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
            'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
        ];

        $string = static fn(string $name, string $ru, string $en, bool $mandatory = true): array => [
            'FIELD_NAME'        => $name,
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => $mandatory ? 'Y' : 'N',
            'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
            'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
        ];

        $fields = [
            $int('UF_ORDER_ID',      'ID заказа',          'Order ID',         true),
            $int('UF_OFFER_ID',      'ID предложения',     'Offer ID',         true),
            $string('UF_PRODUCT_NAME', 'Название товара',  'Product name'),
            $string('UF_PRODUCT_CODE', 'Код товара',       'Product code'),
            $int('UF_OFFER_AREA',    'Площадь, м²',        'Area, m²'),
            $string('UF_OFFER_COLOR', 'Цвет (XML_ID)',     'Color XML_ID',     false),
            $int('UF_QUANTITY',      'Количество',         'Quantity'),
            $int('UF_UNIT_PRICE',    'Цена за единицу',    'Unit price'),
            $int('UF_TOTAL',         'Сумма позиции',      'Line total'),
            [
                'FIELD_NAME'        => 'UF_CREATED_AT',
                'USER_TYPE_ID'      => 'datetime',
                'MANDATORY'         => 'Y',
                'EDIT_FORM_LABEL'   => ['ru' => 'Создан', 'en' => 'Created at'],
                'LIST_COLUMN_LABEL' => ['ru' => 'Создан', 'en' => 'Created'],
            ],
        ];

        foreach ($fields as $f) {
            $helper->Hlblock()->saveField('OrderItems', $f);
        }

        $this->outSuccess('HL-блок «OrderItems» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('OrderItems');
        $this->outSuccess('HL-блок «OrderItems» удалён');
    }
}
