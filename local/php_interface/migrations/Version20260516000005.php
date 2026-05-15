<?php

namespace Sprint\Migration;

/**
 * Дозаливает HL Translations переводы для страницы детальной товара
 * (вкладки, заголовки блоков под формой, недостающие спеки).
 *
 * Если перевод уже есть — addElement создаст дубликат, поэтому сначала
 * проверяем по UF_CODE и пропускаем существующие.
 */
class Version20260516000005 extends Version
{
    protected $description = "UI-переводы для страницы детальной";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'product.color' => ['ru' => 'Цвет', 'en' => 'Color'],
        'product.power_area' => ['ru' => 'Мощность (площадь применения)', 'en' => 'Power (coverage area)'],
        'product.price' => ['ru' => 'Цена', 'en' => 'Price'],
        'product.help' => ['ru' => 'Нужна помощь', 'en' => 'Need help'],
        'product.tab.specs' => ['ru' => 'Технические характеристики', 'en' => 'Specifications'],
        'product.tab.functions' => ['ru' => 'Функции', 'en' => 'Functions'],
        'product.tab.kit' => ['ru' => 'Комплектация', 'en' => 'Package contents'],
        'product.tab.warranty' => ['ru' => 'Гарантия', 'en' => 'Warranty'],
        'product.tab.installation' => ['ru' => 'Установка', 'en' => 'Installation'],
        'spec.outdoor_dimensions' => ['ru' => 'Габариты наружного блока', 'en' => 'Outdoor unit dimensions'],
        'spec.inverter.yes' => ['ru' => 'Да', 'en' => 'Yes'],
        'spec.inverter.no' => ['ru' => 'Нет', 'en' => 'No'],
        'product.area_unit' => ['ru' => 'до :area м²', 'en' => 'up to :area m²'],
        'product.not_found.title' => ['ru' => 'Товар не найден', 'en' => 'Product not found'],
        'product.not_found.back' => ['ru' => 'Вернуться в каталог', 'en' => 'Back to catalog'],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('HL Translations не найден');
            return;
        }

        $existing = $this->existingCodes($hlblockId);

        $added = 0;
        foreach ($this->entries as $code => $values) {
            if (in_array($code, $existing, true)) {
                continue;
            }
            $helper->Hlblock()->addElement($hlblockId, [
                'UF_CODE'     => $code,
                'UF_VALUE_RU' => $values['ru'],
                'UF_VALUE_EN' => $values['en'],
            ]);
            $added++;
        }

        $this->outSuccess('Добавлено новых переводов: %d (всего в сидере %d)', $added, count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    /**
     * @return string[]
     */
    private function existingCodes(int $hlblockId): array
    {
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblockId)->fetch();
        $dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();

        $codes = [];
        $rows = $dataClass::query()->setSelect(['UF_CODE'])->exec();
        while ($row = $rows->fetch()) {
            $codes[] = (string) ($row['UF_CODE'] ?? '');
        }
        return $codes;
    }
}
