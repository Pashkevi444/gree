<?php

namespace Sprint\Migration;

/**
 * Highloadblock «CartItems» — позиции в корзинах.
 *
 *   UF_CART_ID     integer  — FK на Carts.ID (через SHOW_FILTER=Y фильтруем
 *                              позиции по корзине O(1) на индексе)
 *   UF_OFFER_ID    integer  — FK на products_offers (торговое предложение,
 *                              а не модель — у нас цвет/площадь живут per-SKU)
 *   UF_QUANTITY    integer  — количество, всегда >= 1; репозиторий гарантирует
 *                              удаление строки при выставлении в 0
 *   UF_CREATED_AT  datetime — добавление позиции
 *   UF_UPDATED_AT  datetime — последнее изменение количества
 *
 * Уникальность пары (cart_id, offer_id) обеспечивается в репозитории: при
 * добавлении товара сначала ищем существующую строку и инкрементим qty,
 * иначе создаём новую. HL-блок не даёт нативного UNIQUE-индекса по нескольким
 * полям — пришлось бы лезть в raw ALTER TABLE.
 */
class Version20260517000002 extends Version
{
    protected $description = "HL-блок «CartItems» — позиции корзины";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'CartItems',
            'TABLE_NAME' => 'cart_items',
            'LANG'       => [
                'ru' => ['NAME' => 'Позиции корзин'],
                'en' => ['NAME' => 'Cart items'],
            ],
        ]);

        $helper->Hlblock()->saveField('CartItems', [
            'FIELD_NAME'        => 'UF_CART_ID',
            'USER_TYPE_ID'      => 'integer',
            'MANDATORY'         => 'Y',
            'SHOW_FILTER'       => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'ID корзины', 'en' => 'Cart ID'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Корзина', 'en' => 'Cart'],
        ]);

        $helper->Hlblock()->saveField('CartItems', [
            'FIELD_NAME'        => 'UF_OFFER_ID',
            'USER_TYPE_ID'      => 'integer',
            'MANDATORY'         => 'Y',
            'SHOW_FILTER'       => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'ID торгового предложения', 'en' => 'Offer ID'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Оффер', 'en' => 'Offer'],
        ]);

        $helper->Hlblock()->saveField('CartItems', [
            'FIELD_NAME'        => 'UF_QUANTITY',
            'USER_TYPE_ID'      => 'integer',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Количество', 'en' => 'Quantity'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Кол-во', 'en' => 'Qty'],
            'SETTINGS'          => ['DEFAULT_VALUE' => 1, 'MIN_VALUE' => 1],
        ]);

        $helper->Hlblock()->saveField('CartItems', [
            'FIELD_NAME'        => 'UF_CREATED_AT',
            'USER_TYPE_ID'      => 'datetime',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Создана', 'en' => 'Created at'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Создана', 'en' => 'Created'],
        ]);

        $helper->Hlblock()->saveField('CartItems', [
            'FIELD_NAME'        => 'UF_UPDATED_AT',
            'USER_TYPE_ID'      => 'datetime',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Обновлена', 'en' => 'Updated at'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Обновлена', 'en' => 'Updated'],
        ]);

        $this->outSuccess('HL-блок «CartItems» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('CartItems');
        $this->outSuccess('HL-блок «CartItems» удалён');
    }
}
