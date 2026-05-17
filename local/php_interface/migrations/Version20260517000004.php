<?php

namespace Sprint\Migration;

/**
 * Highloadblock «Seo» — SEO для статических страниц сайта.
 *
 * Ключ записи — UF_PAGE_CODE (e.g. "home", "catalog", "catalog-nastennie",
 * "blog", "cart"). Контроллер строит код страницы и читает SEO из этого HL.
 *
 * Для деталок (товар, статья блога) SEO формируется из стандартных IPROPERTY
 * шаблонов инфоблока (см. Version20260517000006) с подстановкой имени/превью
 * — отдельная запись в HL под каждый элемент не нужна.
 *
 * Поля все парные RU/EN — мультиязычность по правилам CLAUDE.md.
 */
class Version20260517000004 extends Version
{
    protected $description = "HL-блок «Seo» — SEO статических страниц";

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'Seo',
            'TABLE_NAME' => 'seo',
            'LANG'       => [
                'ru' => ['NAME' => 'SEO страниц'],
                'en' => ['NAME' => 'Pages SEO'],
            ],
        ]);

        $stringField = static function (string $name, string $labelRu, string $labelEn, bool $mandatory = false, bool $filter = false): array {
            return [
                'FIELD_NAME'        => $name,
                'USER_TYPE_ID'      => 'string',
                'MANDATORY'         => $mandatory ? 'Y' : 'N',
                'SHOW_FILTER'       => $filter ? 'Y' : 'N',
                'EDIT_FORM_LABEL'   => ['ru' => $labelRu, 'en' => $labelEn],
                'LIST_COLUMN_LABEL' => ['ru' => $labelRu, 'en' => $labelEn],
                'SETTINGS'          => ['ROWS' => 2],
            ];
        };

        $fields = [
            $stringField('UF_PAGE_CODE',        'Код страницы',     'Page code',        true, true),
            $stringField('UF_TITLE_RU',         'Title (RU)',       'Title (RU)',       true),
            $stringField('UF_TITLE_EN',         'Title (EN)',       'Title (EN)'),
            $stringField('UF_DESCRIPTION_RU',   'Description (RU)', 'Description (RU)'),
            $stringField('UF_DESCRIPTION_EN',   'Description (EN)', 'Description (EN)'),
            $stringField('UF_KEYWORDS_RU',      'Keywords (RU)',    'Keywords (RU)'),
            $stringField('UF_KEYWORDS_EN',      'Keywords (EN)',    'Keywords (EN)'),
            $stringField('UF_OG_TITLE_RU',      'OG title (RU)',    'OG title (RU)'),
            $stringField('UF_OG_TITLE_EN',      'OG title (EN)',    'OG title (EN)'),
            $stringField('UF_OG_DESCRIPTION_RU','OG description (RU)','OG description (RU)'),
            $stringField('UF_OG_DESCRIPTION_EN','OG description (EN)','OG description (EN)'),
            $stringField('UF_OG_IMAGE',         'OG image URL',     'OG image URL'),
        ];

        foreach ($fields as $field) {
            $helper->Hlblock()->saveField('Seo', $field);
        }

        $this->outSuccess('HL-блок «Seo» создан [id=%d]', $hlblockId);
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Seo');
        $this->outSuccess('HL-блок «Seo» удалён');
    }
}
