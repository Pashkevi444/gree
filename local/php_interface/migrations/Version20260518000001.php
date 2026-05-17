<?php

namespace Sprint\Migration;

/**
 * Highloadblock «Orders» — оформленные заказы (шапка).
 *
 *   UF_PUBLIC_ID         string(16)  — публичный идентификатор для URL (base36,
 *                                      12 символов, ~62 бит энтропии). НЕ ID.
 *                                      Поисковый индекс через SHOW_FILTER=Y.
 *   UF_CART_TOKEN        string(64)  — UUID v4 корзины на момент оформления.
 *                                      Помогает связать заказ с исходной корзиной
 *                                      и отлавливать двойные сабмиты.
 *   UF_STATUS            string(32)  — состояние: new | confirmed | shipped |
 *                                      delivered | cancelled. По умолчанию «new».
 *
 *   ── Контактные данные ──────────────────────────────────────────────────────
 *   UF_CUSTOMER_NAME     string      — имя
 *   UF_CUSTOMER_PHONE    string      — телефон (нормализованный)
 *   UF_CUSTOMER_TELEGRAM string      — telegram-ник, опционально
 *
 *   ── Доставка ───────────────────────────────────────────────────────────────
 *   UF_DELIVERY_CITY     string      — код города (slug)
 *   UF_DELIVERY_STREET   string
 *   UF_DELIVERY_HOUSE    string
 *   UF_DELIVERY_APARTMENT string     — опционально
 *   UF_DELIVERY_COMMENT  text        — опционально
 *
 *   ── Оплата ─────────────────────────────────────────────────────────────────
 *   UF_PAYMENT_METHOD    string(32)  — card | uzum_bank | anor_bank
 *
 *   ── Итоги (snapshot) ───────────────────────────────────────────────────────
 *   UF_TOTAL             integer     — сумма заказа на момент оформления (UZS)
 *   UF_ITEMS_COUNT       integer     — общее количество единиц
 *
 *   ── Аудит ──────────────────────────────────────────────────────────────────
 *   UF_LOCALE            string(8)   — ru | en, для манагерских уведомлений
 *   UF_IP                string(64)  — REMOTE_ADDR
 *   UF_USER_AGENT        string      — User-Agent (обрезать до 500 символов в коде)
 *
 *   UF_CREATED_AT        datetime
 *   UF_UPDATED_AT        datetime
 *
 * Позиции заказа — в отдельном HL «OrderItems» (Version20260518000002).
 */
class Version20260518000001 extends Version
{
    protected $description = "HL-блок «Orders» — шапка заказа";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'Orders',
            'TABLE_NAME' => 'orders',
            'LANG'       => [
                'ru' => ['NAME' => 'Заказы'],
                'en' => ['NAME' => 'Orders'],
            ],
        ]);

        $string = static function (string $name, string $ru, string $en, bool $mandatory = false, bool $filter = false, int $size = 0): array {
            $field = [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => $mandatory ? 'Y' : 'N',
                'SHOW_FILTER'       => $filter ? 'Y' : 'N',
                'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
                'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
            ];
            if ($size > 0) {
                $field['SETTINGS'] = ['SIZE' => $size, 'MAX_LENGTH' => $size];
            }
            return $field;
        };

        $textarea = static function (string $name, string $ru, string $en): array {
            return [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => 'N',
                'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
                'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
                'SETTINGS'          => ['ROWS' => 4],
            ];
        };

        $int = static function (string $name, string $ru, string $en, bool $mandatory = false): array {
            return [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'integer',
                'MANDATORY'         => $mandatory ? 'Y' : 'N',
                'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
                'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
            ];
        };

        $datetime = static function (string $name, string $ru, string $en): array {
            return [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'datetime',
                'MANDATORY'         => 'Y',
                'EDIT_FORM_LABEL'   => ['ru' => $ru, 'en' => $en],
                'LIST_COLUMN_LABEL' => ['ru' => $ru, 'en' => $en],
            ];
        };

        $fields = [
            $string('UF_PUBLIC_ID',          'Публичный ID',    'Public ID',       true,  true, 16),
            $string('UF_CART_TOKEN',         'Токен корзины',   'Cart token',      false, true, 64),
            $string('UF_STATUS',             'Статус',          'Status',          true,  true, 32),

            $string('UF_CUSTOMER_NAME',      'Имя клиента',     'Customer name',   true),
            $string('UF_CUSTOMER_PHONE',     'Телефон',         'Phone',           true,  true),
            $string('UF_CUSTOMER_TELEGRAM',  'Telegram',        'Telegram'),

            $string('UF_DELIVERY_CITY',      'Город',           'City',            true),
            $string('UF_DELIVERY_STREET',    'Улица',           'Street',          true),
            $string('UF_DELIVERY_HOUSE',     'Дом',             'House',           true),
            $string('UF_DELIVERY_APARTMENT', 'Квартира',        'Apartment'),
            $textarea('UF_DELIVERY_COMMENT', 'Комментарий',     'Comment'),

            $string('UF_PAYMENT_METHOD',     'Способ оплаты',   'Payment method',  true, true, 32),

            $int('UF_TOTAL',                 'Итого (UZS)',     'Total (UZS)',     true),
            $int('UF_ITEMS_COUNT',           'Позиций',         'Items count',     true),

            $string('UF_LOCALE',             'Локаль',          'Locale',          false, false, 8),
            $string('UF_IP',                 'IP',              'IP',              false, false, 64),
            $string('UF_USER_AGENT',         'User-Agent',      'User-Agent'),

            $datetime('UF_CREATED_AT',       'Создан',          'Created at'),
            $datetime('UF_UPDATED_AT',       'Обновлён',        'Updated at'),
        ];

        foreach ($fields as $f) {
            $helper->Hlblock()->saveField('Orders', $f);
        }

        $this->outSuccess('HL-блок «Orders» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Orders');
        $this->outSuccess('HL-блок «Orders» удалён');
    }
}
