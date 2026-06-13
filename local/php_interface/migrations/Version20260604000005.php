<?php

namespace Sprint\Migration;

/**
 * Тип инфоблока «partners» + 3 iblock:
 *   partners_b2b              — 3 карточки моделей сотрудничества (NAME + DESC + STEP_NUMBER)
 *   partners_how_it_works     — 4 карточки этапов (NAME + DESC + LINK_LABEL + LINK_URL)
 *   partners_companies_trust  — карусель логотипов компаний (NAME + IMAGE)
 *
 * Секция «Почему легко продавать?» — статические переводы в HL Translations
 * (мало карточек, не меняются часто).
 */
class Version20260604000005 extends Version
{
    protected $description = "Тип iblock 'partners' + 3 iblock (b2b/how_it_works/companies_trust)";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID'        => 'partners',
            'SECTIONS'  => 'N',
            'IN_RSS'    => 'N',
            'SORT'      => 900,
            'LANG'      => [
                'ru' => ['NAME' => 'Партнёры', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
            ],
        ]);

        $this->createB2b();
        $this->createHowItWorks();
        $this->createCompaniesTrust();

        $this->outSuccess('Тип «partners» и 3 iblock\'а созданы');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        foreach (['partners_b2b', 'partners_how_it_works', 'partners_companies_trust'] as $code) {
            $helper->Iblock()->deleteIblockIfExists($code);
        }
        try {
            $helper->Iblock()->deleteIblockTypeIfExists('partners');
        } catch (\Throwable) {
        }
    }

    private function createB2b(): void
    {
        $id = $this->makeIblock('partners_b2b', 'PartnersB2b', 'B2B-партнёрство', 100);
        $this->addLocalized($id, 'NAME', 'Заголовок', 100);
        $this->addLocalized($id, 'DESCRIPTION', 'Описание', 200, 3);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Номер шага',
            'CODE'          => 'STEP_NUMBER',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => '300',
        ]);
    }

    private function createHowItWorks(): void
    {
        $id = $this->makeIblock('partners_how_it_works', 'PartnersHowItWorks', 'Как работает партнёрство', 200);
        $this->addLocalized($id, 'NAME', 'Заголовок', 100);
        $this->addLocalized($id, 'DESCRIPTION', 'Описание', 200, 4);
        $this->addLocalized($id, 'LINK_LABEL', 'Текст ссылки (опционально)', 300);
        $this->addPlain($id, 'LINK_URL', 'URL ссылки (опционально)', 400);
    }

    private function createCompaniesTrust(): void
    {
        $id = $this->makeIblock('partners_companies_trust', 'PartnersCompaniesTrust', 'Компании, которые нам доверяют', 300);
        $this->addLocalized($id, 'NAME', 'Название (для alt)', 100);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Логотип',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '200',
        ]);
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function makeIblock(string $code, string $apiCode, string $name, int $sort): int
    {
        $helper = $this->getHelperManager();
        $id = $helper->Iblock()->saveIblock([
            'NAME'           => $name,
            'CODE'           => $code,
            'API_CODE'       => $apiCode,
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'partners',
            'SORT'           => $sort,
        ]);
        $helper->Iblock()->saveIblockFields($id, [
            'CODE' => [
                'DEFAULT_VALUE' => [
                    'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L',
                    'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y',
                ],
                'IS_REQUIRED' => 'N',
            ],
            'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
            'XML_ID'      => ['IS_REQUIRED' => 'N'],
            'TAGS'        => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
        ]);
        return (int) $id;
    }

    private function addLocalized(int $iblockId, string $base, string $label, int $sortBase, int $rows = 1): void
    {
        $helper = $this->getHelperManager();
        foreach (['RU', 'UZ'] as $i => $lang) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME'          => sprintf('%s (%s)', $label, $lang),
                'CODE'          => $base . '_' . $lang,
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT'     => (string) $rows,
                'SORT'          => (string) ($sortBase + $i * 10),
            ]);
        }
    }

    private function addPlain(int $iblockId, string $code, string $name, int $sort): void
    {
        $this->getHelperManager()->Iblock()->saveProperty($iblockId, [
            'NAME'          => $name,
            'CODE'          => $code,
            'PROPERTY_TYPE' => 'S',
            'SORT'          => (string) $sort,
        ]);
    }
}
