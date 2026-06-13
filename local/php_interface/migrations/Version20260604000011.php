<?php

namespace Sprint\Migration;

/**
 * HL «CatalogHelpFeedback» — заявки с формы «Нужна помощь?» на детальной
 * карточке товара (/catalog/{section}/{code}/).
 *
 * Намеренно отдельный сторадж, не общий HL «Feedback»: контент-менеджер
 * хочет видеть отдельный grid именно по этим заявкам без UF_KIND-фильтра.
 *
 *   UF_NAME       string   — имя клиента (обязательно)
 *   UF_PHONE      string   — телефон (обязательно)
 *   UF_CREATED_AT datetime — время отправки
 */
class Version20260604000011 extends Version
{
    protected $description = "HL «CatalogHelpFeedback» — заявки с формы «Нужна помощь?» на товаре";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'CatalogHelpFeedback',
            'TABLE_NAME' => 'catalog_help_feedback',
            'LANG'       => [
                'ru' => ['NAME' => 'Заявки с карточки товара'],
            ],
        ]);

        $string = static function (string $name, string $label, bool $mandatory = false, bool $filter = false, int $rows = 1): array {
            return [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => $mandatory ? 'Y' : 'N',
                'SHOW_FILTER'       => $filter ? 'Y' : 'N',
                'EDIT_FORM_LABEL'   => ['ru' => $label],
                'LIST_COLUMN_LABEL' => ['ru' => $label],
                'SETTINGS'          => ['ROWS' => $rows],
            ];
        };

        $helper->Hlblock()->saveField('CatalogHelpFeedback', $string('UF_NAME',  'Имя',     true));
        $helper->Hlblock()->saveField('CatalogHelpFeedback', $string('UF_PHONE', 'Телефон', true, true));

        $helper->Hlblock()->saveField('CatalogHelpFeedback', [
            'FIELD_NAME'        => 'UF_CREATED_AT',
            'USER_TYPE_ID'      => 'datetime',
            'MANDATORY'         => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Создана'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Создана'],
        ]);

        $this->outSuccess('HL «CatalogHelpFeedback» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $this->getHelperManager()->Hlblock()->deleteHlblockIfExists('CatalogHelpFeedback');
        $this->outSuccess('HL «CatalogHelpFeedback» удалён');
    }
}
