<?php

namespace Sprint\Migration;

/**
 * HL «Orders» + UF_ITEMS_SUMMARY (text, multiline) — менеджер видит состав
 * заказа прямо в карточке заказа без перехода в OrderItems. Заполняется
 * автоматически при оформлении (OrderService::place), формат строки:
 *   «Gree BORA X 07 (Белый, 30 м²) × 2 — 4 000 000 UZS»
 *
 * Идемпотентно: saveField перезапишет существующее поле теми же настройками.
 */
class Version20260622000002 extends Version
{
    protected $description = "Orders.UF_ITEMS_SUMMARY — состав заказа текстом для менеджера";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Orders');
        if (!$hlblockId) {
            $this->outError('HL «Orders» не найден');
            return;
        }

        $helper->Hlblock()->saveField('Orders', [
            'FIELD_NAME'        => 'UF_ITEMS_SUMMARY',
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Состав заказа', 'en' => 'Items'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Состав',        'en' => 'Items'],
            'SETTINGS'          => ['ROWS' => 8, 'SIZE' => 80],
        ]);

        $this->outSuccess('Orders.UF_ITEMS_SUMMARY добавлено');
    }

    public function down(): void
    {
        $this->outSuccess('Откат: поле историческое — оставляем');
    }
}
