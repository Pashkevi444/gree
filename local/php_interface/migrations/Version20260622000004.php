<?php

namespace Sprint\Migration;

/**
 * HL «Orders»: связка позиций через нативный USER_TYPE_ID='hlblock'
 * («Привязка к элементам highload-блоков»). В админке поле — мульти-селект
 * с поиском по записям HL «OrderItems», отображается через UF_PRODUCT_NAME.
 *
 * За одну миграцию:
 *   1) сносим UF_ITEMS_SUMMARY (от прошлого подхода — текстовый дубликат);
 *   2) сносим UF_ITEM_IDS если уже есть (тип менять нельзя — только пересоздать);
 *   3) создаём UF_ITEM_IDS как hlblock-привязку;
 *   4) backfill для существующих заказов из OrderItems.UF_ORDER_ID.
 *
 * Идемпотентно: чистки защищены проверкой getField, backfill — overwrite.
 */
class Version20260622000004 extends Version
{
    protected $description = "Orders.UF_ITEM_IDS — hlblock-привязка к OrderItems + backfill, чистка UF_ITEMS_SUMMARY";

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

        // Display-поле: что менеджер видит в селекте вместо ID.
        $displayField = \Bitrix\Main\UserFieldTable::query()
            ->where('ENTITY_ID', 'HLBLOCK_' . $itemsHlId)
            ->where('FIELD_NAME', 'UF_PRODUCT_NAME')
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();
        $displayFieldId = (int) ($displayField['ID'] ?? 0);

        // 1+2: чистим устаревшие поля. Sprint deleteField бросает, если поля нет — защищаемся getField.
        foreach (['UF_ITEMS_SUMMARY', 'UF_ITEM_IDS'] as $oldField) {
            if ($helper->Hlblock()->getField('Orders', $oldField)) {
                $helper->Hlblock()->deleteField('Orders', $oldField);
                $this->outSuccess('удалено старое поле %s', $oldField);
            }
        }

        // 3: создаём заново как hlblock-привязку.
        $helper->Hlblock()->saveField('Orders', [
            'FIELD_NAME'        => 'UF_ITEM_IDS',
            'USER_TYPE_ID'      => 'hlblock',
            'MULTIPLE'          => 'Y',
            'MANDATORY'         => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Позиции заказа', 'en' => 'Order items'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Позиции',        'en' => 'Items'],
            'SETTINGS' => [
                'HLBLOCK_ID'    => $itemsHlId,
                'HLFIELD_ID'    => $displayFieldId,
                'DISPLAY'       => 'LIST',
                'SHOW_NO_VALUE' => 'Y',
            ],
        ]);
        $this->outSuccess('UF_ITEM_IDS создан (HLBLOCK_ID=%d, HLFIELD_ID=%d)', $itemsHlId, $displayFieldId);

        // 4: backfill из OrderItems.UF_ORDER_ID → Orders.UF_ITEM_IDS.
        $ordersHl = \Bitrix\Highloadblock\HighloadBlockTable::getById($ordersHlId)->fetch();
        $itemsHl  = \Bitrix\Highloadblock\HighloadBlockTable::getById($itemsHlId)->fetch();
        $ordersCls = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($ordersHl)->getDataClass();
        $itemsCls  = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($itemsHl)->getDataClass();

        $itemsByOrder = [];
        $itemsRes = $itemsCls::query()->setSelect(['ID', 'UF_ORDER_ID'])->setOrder(['ID' => 'ASC'])->exec();
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
            $ordersCls::update($orderId, ['UF_ITEM_IDS' => $itemsByOrder[$orderId] ?? []]);
            $touched++;
        }
        $this->outSuccess('Backfill: %d заказов, %d позиций суммарно', $touched, array_sum(array_map('count', $itemsByOrder)));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не имеет смысла — поле историческое');
    }
}
