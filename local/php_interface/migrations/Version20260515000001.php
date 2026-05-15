<?php

namespace Sprint\Migration;

/**
 * Создаёт Highloadblock «Translations» — справочник UI-строк (RU/EN).
 * Структура:
 *   UF_CODE      string  — ключ перевода (например, "header.catalog")
 *   UF_VALUE_RU  string  — русский текст
 *   UF_VALUE_EN  string  — английский текст
 */
class Version20260515000001 extends Version
{
    protected $description = "Highloadblock «Translations» для UI-строк";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'Translations',
            'TABLE_NAME' => 'translations',
            'LANG'       => [
                'ru' => ['NAME' => 'Переводы UI'],
                'en' => ['NAME' => 'UI translations'],
            ],
        ]);

        $helper->Hlblock()->saveField('Translations', [
            'FIELD_NAME'        => 'UF_CODE',
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => 'Y',
            'SHOW_FILTER'       => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Ключ', 'en' => 'Key'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Ключ', 'en' => 'Key'],
        ]);

        $helper->Hlblock()->saveField('Translations', [
            'FIELD_NAME'        => 'UF_VALUE_RU',
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Значение RU', 'en' => 'Value RU'],
            'LIST_COLUMN_LABEL' => ['ru' => 'RU', 'en' => 'RU'],
            'SETTINGS'          => ['ROWS' => 2],
        ]);

        $helper->Hlblock()->saveField('Translations', [
            'FIELD_NAME'        => 'UF_VALUE_EN',
            'USER_TYPE_ID'      => 'string',
            'MANDATORY'         => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Значение EN', 'en' => 'Value EN'],
            'LIST_COLUMN_LABEL' => ['ru' => 'EN', 'en' => 'EN'],
            'SETTINGS'          => ['ROWS' => 2],
        ]);

        $this->outSuccess('Highloadblock «Translations» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Translations');
        $this->outSuccess('Highloadblock «Translations» удалён');
    }
}
