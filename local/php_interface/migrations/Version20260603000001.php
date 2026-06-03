<?php

namespace Sprint\Migration;

/**
 * Структура инфоблоков страницы /help/ — по одному iblock на каждый
 * содержательный блок страницы (см. dist/help.html):
 *
 *   help_payment_methods  — карточки способов оплаты (NAME + IMAGE)
 *   help_delivery         — карточки вариантов доставки (NAME + PREVIEW + ICON_CODE)
 *   help_exchange_steps   — шаги обмена товара (NAME + PREVIEW + STEP_NUMBER)
 *   help_refund_steps     — шаги возврата товара (то же + TOOLTIP_RU/UZ)
 *   help_service_features — фичи единого сервисного центра (NAME + ICON_CODE)
 *   help_service_hero     — hero «надёжный сервис» (NAME + PREVIEW + BG image)
 *   help_service_cards    — карточки рядом с hero (NAME + PREVIEW + ICON_CODE)
 *
 * Текстовые поля везде парные _RU / _UZ (правило проекта).
 */
class Version20260603000001 extends Version
{
    protected $description = "Структура iblock'ов страницы /help/";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $this->createPaymentMethods();
        $this->createDelivery();
        $this->createExchangeSteps();
        $this->createRefundSteps();
        $this->createServiceFeatures();
        $this->createServiceHero();
        $this->createServiceCards();

        $this->outSuccess('Help iblock-структуры созданы (7 шт.)');
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        foreach ([
            'help_payment_methods', 'help_delivery',
            'help_exchange_steps', 'help_refund_steps',
            'help_service_features', 'help_service_hero', 'help_service_cards',
        ] as $code) {
            $helper->Iblock()->deleteIblockIfExists($code);
        }
        $this->outSuccess('Help iblock-структуры удалены');
    }

    // ── factories ────────────────────────────────────────────────────────────

    private function createPaymentMethods(): void
    {
        $id = $this->makeIblock('help_payment_methods', 'HelpPaymentMethods', 'Способы оплаты', 600);
        $this->addLocalizedString($id, 'NAME', 'Название', 100);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Логотип',
            'CODE'          => 'IMAGE',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '200',
        ]);
    }

    private function createDelivery(): void
    {
        $id = $this->makeIblock('help_delivery', 'HelpDelivery', 'Доставка', 610);
        $this->addLocalizedString($id, 'NAME', 'Название', 100);
        $this->addLocalizedString($id, 'PREVIEW_TEXT', 'Описание', 200, 4);
        $this->addIconCode($id, 300);
    }

    private function createExchangeSteps(): void
    {
        $id = $this->makeIblock('help_exchange_steps', 'HelpExchangeSteps', 'Шаги обмена', 620);
        $this->addLocalizedString($id, 'NAME', 'Название шага', 100);
        $this->addLocalizedString($id, 'PREVIEW_TEXT', 'Описание', 200, 3);
        $this->addStepNumber($id, 300);
    }

    private function createRefundSteps(): void
    {
        $id = $this->makeIblock('help_refund_steps', 'HelpRefundSteps', 'Шаги возврата', 630);
        $this->addLocalizedString($id, 'NAME', 'Название шага', 100);
        $this->addLocalizedString($id, 'PREVIEW_TEXT', 'Описание', 200, 3);
        $this->addStepNumber($id, 300);
        $this->addLocalizedString($id, 'TOOLTIP', 'Подсказка (tooltip)', 400, 4);
    }

    private function createServiceFeatures(): void
    {
        $id = $this->makeIblock('help_service_features', 'HelpServiceFeatures', 'Фичи сервисного центра', 640);
        $this->addLocalizedString($id, 'NAME', 'Название', 100);
        $this->addIconCode($id, 200);
    }

    private function createServiceHero(): void
    {
        $id = $this->makeIblock('help_service_hero', 'HelpServiceHero', 'Hero "надёжный сервис"', 650);
        $this->addLocalizedString($id, 'NAME', 'Заголовок', 100, 2);
        $this->addLocalizedString($id, 'PREVIEW_TEXT', 'Описание', 200, 4);
        $this->getHelperManager()->Iblock()->saveProperty($id, [
            'NAME'          => 'Фоновое изображение',
            'CODE'          => 'BACKGROUND',
            'PROPERTY_TYPE' => 'F',
            'SORT'          => '300',
        ]);
    }

    private function createServiceCards(): void
    {
        $id = $this->makeIblock('help_service_cards', 'HelpServiceCards', 'Карточки сервисного блока', 660);
        $this->addLocalizedString($id, 'NAME', 'Заголовок', 100);
        $this->addLocalizedString($id, 'PREVIEW_TEXT', 'Описание', 200, 4);
        $this->addIconCode($id, 300);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function makeIblock(string $code, string $apiCode, string $name, int $sort): int
    {
        $helper = $this->getHelperManager();
        $id = $helper->Iblock()->saveIblock([
            'NAME'           => $name,
            'CODE'           => $code,
            'API_CODE'       => $apiCode,
            'LID'            => ['s1'],
            'IBLOCK_TYPE_ID' => 'content',
            'SORT'           => $sort,
        ]);
        $helper->Iblock()->saveIblockFields($id, [
            'CODE'        => [
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

    private function addLocalizedString(int $iblockId, string $base, string $label, int $sortBase, int $rows = 1): void
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

    private function addIconCode(int $iblockId, int $sort): void
    {
        $this->getHelperManager()->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Код иконки',
            'CODE'          => 'ICON_CODE',
            'PROPERTY_TYPE' => 'S',
            'SORT'          => (string) $sort,
            'HINT'          => 'Blade switch\'ит ICON_CODE на конкретный SVG (см. views/help/index.blade.php)',
        ]);
    }

    private function addStepNumber(int $iblockId, int $sort): void
    {
        $this->getHelperManager()->Iblock()->saveProperty($iblockId, [
            'NAME'          => 'Номер шага',
            'CODE'          => 'STEP_NUMBER',
            'PROPERTY_TYPE' => 'N',
            'SORT'          => (string) $sort,
        ]);
    }
}
