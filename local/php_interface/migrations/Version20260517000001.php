<?php

namespace Sprint\Migration;

/**
 * Highloadblock «Carts» — анонимные корзины.
 *
 *   UF_TOKEN       string(64)  — UUID v4, идентификатор корзины из cookie `cart_token`
 *                                (HttpOnly, SameSite=Lax, lifetime 1 год). Уникальный
 *                                индекс через SHOW_FILTER=Y + MANDATORY=Y; жёсткой
 *                                уникальности на уровне БД нет — её гарантируем в
 *                                репозитории через upsert по этому полю.
 *   UF_CREATED_AT  datetime    — создание корзины (для cleanup-агента старых)
 *   UF_UPDATED_AT  datetime    — последнее изменение позиций
 *
 * Почему именно HL-блок, а не iblock: домен «корзина» — не контент, индексы по
 * UF-полю строятся нативно, отсутствует SEO / sections / detail page overhead.
 *
 * Почему именно cookie-UUID: пользователь не залогинен, заводить кастомную
 * сессию излишне; UUID v4 в HttpOnly-куке + индекс по UF_TOKEN = O(1) lookup
 * и нулевая утечка через URL/реферер.
 */
class Version20260517000001 extends Version
{
    protected $description = "HL-блок «Carts» — анонимные корзины";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'Carts',
            'TABLE_NAME' => 'carts',
            'LANG'       => [
                'ru' => ['NAME' => 'Корзины'],
                'en' => ['NAME' => 'Carts'],
            ],
        ]);

        $helper->Hlblock()->saveField('Carts', [
            'FIELD_NAME'        => 'UF_TOKEN',
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => 'Y',
            'SHOW_FILTER'       => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Токен', 'en' => 'Token'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Токен', 'en' => 'Token'],
            'SETTINGS'          => ['SIZE' => 64, 'MAX_LENGTH' => 64],
        ]);

        $helper->Hlblock()->saveField('Carts', [
            'FIELD_NAME'        => 'UF_CREATED_AT',
            'USER_TYPE_ID'      => 'datetime',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Создана', 'en' => 'Created at'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Создана', 'en' => 'Created'],
        ]);

        $helper->Hlblock()->saveField('Carts', [
            'FIELD_NAME'        => 'UF_UPDATED_AT',
            'USER_TYPE_ID'      => 'datetime',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Обновлена', 'en' => 'Updated at'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Обновлена', 'en' => 'Updated'],
        ]);

        $this->outSuccess('HL-блок «Carts» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Carts');
        $this->outSuccess('HL-блок «Carts» удалён');
    }
}
