<?php

namespace Sprint\Migration;

/**
 * Меняем тип UF_ITEM_IDS с `integer multiple` на нативный `hlblock` —
 * «Привязка к элементам highload-блоков». В админке поле рисуется как
 * мульти-селект с поиском по записям HL «OrderItems», отображается через
 * UF_PRODUCT_NAME (а не голые ID).
 *
 * Изменить USER_TYPE_ID существующего UF Bitrix не даёт, поэтому удаляем поле
 * и создаём заново; backfill повторяется (одна проходка по OrderItems +
 * update Orders.UF_ITEM_IDS).
 *
 * Идемпотентно: deleteFieldIfExists → saveField → backfill.
 */
class Version20260622000004 extends Version
{
    protected $description = "Orders.UF_ITEM_IDS → тип «привязка к HL» (OrderItems) + backfill";

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

        // Display-поле: то, что менеджер видит в селекте вместо ID.
        $displayField = \Bitrix\Main\UserFieldTable::query()
            ->where('ENTITY_ID', 'HLBLOCK_' . $itemsHlId)
            ->where('FIELD_NAME', 'UF_PRODUCT_NAME')
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();
        $displayFieldId = (int) ($displayField['ID'] ?? 0);

        // 1. Снести int-поле (тип менять нельзя — только удалить и пересоздать).
        $helper->Hlblock()->deleteFieldIfExists('Orders', 'UF_ITEM_IDS');

        // 2. Создать заново как hlblock-привязку.
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
        $this->outSuccess('UF_ITEM_IDS пересоздан как hlblock-привязка (HLBLOCK_ID=%d, HLFIELD_ID=%d)', $itemsHlId, $displayFieldId);

        // 3. Backfill: собрать ID OrderItems по UF_ORDER_ID и записать в Orders.UF_ITEM_IDS.
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
