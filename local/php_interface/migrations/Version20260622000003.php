<?php

namespace Sprint\Migration;

/**
 * Меняем UF_ITEMS_SUMMARY (string-дубликат) на UF_ITEM_IDS (integer multiple) —
 * настоящая связь Orders → OrderItems. Менеджер видит в карточке заказа список
 * ID позиций; перейти к деталям одного клика — через HL «OrderItems».
 *
 * Backfill: для каждого существующего заказа собираем ID строк OrderItems с
 * UF_ORDER_ID == orders.ID и пишем в UF_ITEM_IDS.
 *
 * Идемпотентно: saveField перезапишет существующее, deleteFieldIfExists без
 * исключений, backfill update идемпотентен.
 */
class Version20260622000003 extends Version
{
    protected $description = "Orders: UF_ITEMS_SUMMARY → UF_ITEM_IDS (связь с OrderItems) + backfill";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $ordersHlId = $helper->Hlblock()->getHlblockIdIfExists('Orders');
        $itemsHlId  = $helper->Hlblock()->getHlblockIdIfExists('OrderItems');
        if (!$ordersHlId || !$itemsHlId) {
            $this->outError('HL Orders / OrderItems не найдены');
            return;
        }

        // 1. Снести устаревший summary-string.
        $helper->Hlblock()->deleteFieldIfExists('Orders', 'UF_ITEMS_SUMMARY');
        $this->outSuccess('UF_ITEMS_SUMMARY удалено');

        // 2. Добавить связку integer multiple.
        $helper->Hlblock()->saveField('Orders', [
            'FIELD_NAME'        => 'UF_ITEM_IDS',
            'USER_TYPE_ID'      => 'integer',
            'MULTIPLE'          => 'Y',
            'MANDATORY'         => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Позиции заказа', 'en' => 'Order items'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Позиции',        'en' => 'Items'],
            'SETTINGS'          => ['SIZE' => 10],
        ]);
        $this->outSuccess('UF_ITEM_IDS добавлено');

        // 3. Backfill для существующих заказов.
        $ordersHl = \Bitrix\Highloadblock\HighloadBlockTable::getById($ordersHlId)->fetch();
        $itemsHl  = \Bitrix\Highloadblock\HighloadBlockTable::getById($itemsHlId)->fetch();
        $ordersCls = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($ordersHl)->getDataClass();
        $itemsCls  = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($itemsHl)->getDataClass();

        $itemsByOrder = [];
        $itemsRes = $itemsCls::query()
            ->setSelect(['ID', 'UF_ORDER_ID'])
            ->setOrder(['ID' => 'ASC'])
            ->exec();
        while ($row = $itemsRes->fetch()) {
            $orderId = (int) $row['UF_ORDER_ID'];
            if ($orderId <= 0) {
                continue;
            }
            $itemsByOrder[$orderId][] = (int) $row['ID'];
        }

        $touched = 0;
        $ordersRes = $ordersCls::query()->setSelect(['ID'])->exec();
        while ($row = $ordersRes->fetch()) {
            $orderId = (int) $row['ID'];
            $ids = $itemsByOrder[$orderId] ?? [];
            $ordersCls::update($orderId, ['UF_ITEM_IDS' => $ids]);
            $touched++;
        }

        $this->outSuccess('Backfill: %d заказов, %d позиций суммарно', $touched, array_sum(array_map('count', $itemsByOrder)));
    }

    public function down(): void
    {
        $this->outSuccess('Откат: поле историческое — оставляем');
    }
}
