<?php

namespace Sprint\Migration;

/**
 * Заливает тестовые данные на все товары и торговые предложения.
 *
 * На уровне товара (`products`):
 *   - SKU, MODEL, ENERGY_CLASS, REFRIGERANT
 *   - WARRANTY_TEXT_RU/EN, KIT_TEXT_RU/EN, INSTALLATION_TEXT_RU/EN
 *   - FUNCTIONS (список кодов фич — переводы в HL Translations)
 *   - GALLERY (5 копий тестовой картинки)
 *
 * На уровне torgового предложения (`products_offers`):
 *   - COOLING_POWER_RU/EN, HEATING_POWER_RU/EN, NOISE_RU/EN
 *   - INDOOR_DIMENSIONS_RU/EN, OUTDOOR_DIMENSIONS_RU/EN
 *   - INDOOR_WEIGHT_RU/EN, OUTDOOR_WEIGHT_RU/EN
 *   Значения формируются на основе AREA — чем больше площадь, тем больше мощность.
 */
class Version20260516000011 extends Version
{
    protected $description = "Полная тестовая заливка товаров и offers";

    private string $imagesDir;

    /** @var array<string, array<string, string|int>> */
    private array $productMeta = [
        // По товарам — общие модельные атрибуты.
        // Все продукты получают одинаковый набор функций для теста.
        'gree-bora-x-07'      => ['sku' => '1234567', 'model' => 'GWH07AGA-K3NNA1B', 'energy' => 'A++', 'refrigerant' => 'R32'],
        'gree-bora-x-09'      => ['sku' => '1234568', 'model' => 'GWH09AGA-K3NNA1B', 'energy' => 'A++', 'refrigerant' => 'R32'],
        'gree-pular-12'       => ['sku' => '1234569', 'model' => 'GWH12RPLA-K3NNA1B', 'energy' => 'A++', 'refrigerant' => 'R32'],
        'gree-lomo-dc-09'     => ['sku' => '1234570', 'model' => 'GWH09QB-K6DNA1A', 'energy' => 'A+', 'refrigerant' => 'R32'],
        'gree-hansol-iii-07'  => ['sku' => '1234571', 'model' => 'GWH07KF-K3DNA5B', 'energy' => 'A',  'refrigerant' => 'R410A'],
        'gree-free-match-12'  => ['sku' => '1234572', 'model' => 'GUD35ZD-K3NNA1A', 'energy' => 'A++', 'refrigerant' => 'R32'],
        'gree-gwh18agd'       => ['sku' => '1234573', 'model' => 'GWH18AGD-K3DNA',  'energy' => 'A',  'refrigerant' => 'R410A'],
        'gree-vir09hp'        => ['sku' => '1234574', 'model' => 'VIR09HP115V1B',   'energy' => 'A+', 'refrigerant' => 'R410A'],
    ];

    /** @var string[] */
    private array $functions = ['wifi', '130v', 'energy-saving', 'turbo', 'silent', 'eco', 'smart-home', 'ai'];

    private const WARRANTY_RU = '<p>10 лет на инверторный компрессор. 3 года на электронные компоненты. 1 год на сервисное обслуживание и расходники.</p>';
    private const WARRANTY_EN = '<p>10-year warranty on the inverter compressor. 3 years on electronics. 1 year on service and consumables.</p>';
    private const KIT_RU = '<p>В комплекте: внутренний блок, наружный блок, межблочный кабель, монтажная пластина, инфракрасный пульт ДУ с батарейками, паспорт изделия, гарантийный талон.</p>';
    private const KIT_EN = '<p>Package contents: indoor unit, outdoor unit, inter-unit cable, mounting plate, IR remote with batteries, product passport, warranty card.</p>';
    private const INSTALL_RU = '<p>Установку выполняют сертифицированные специалисты сервисного центра Gree. Стандартный монтаж — 3 часа, включая прокладку трассы до 4 м, бурение, вакуумирование и пуск.</p>';
    private const INSTALL_EN = '<p>Installation is performed by certified Gree service-center technicians. Standard installation takes ~3 hours: routing up to 4 m, drilling, vacuum and start-up.</p>';

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $this->imagesDir = __DIR__ . '/images';
        $helper = $this->getHelperManager();
        $productsId = $helper->Iblock()->getIblockIdIfExists('products');
        $offersId   = $helper->Iblock()->getIblockIdIfExists('products_offers');

        if (!$productsId || !$offersId) {
            $this->outError('Iblocks products / products_offers не найдены');
            return;
        }

        // ─── 1. Per-product fill ───────────────────────────────────────────
        $entity = \Bitrix\Iblock\Iblock::wakeUp($productsId)->getEntityDataClass();
        $rows = $entity::query()->setSelect(['ID', 'CODE'])->exec();

        $productById = [];
        while ($row = $rows->fetch()) {
            $code = (string) ($row['CODE'] ?? '');
            $meta = $this->productMeta[$code] ?? null;
            if ($meta === null) {
                continue;
            }
            $id = (int) $row['ID'];
            $productById[$id] = $code;

            $props = [
                'SKU'           => (string) $meta['sku'],
                'MODEL'         => (string) $meta['model'],
                'ENERGY_CLASS'  => (string) $meta['energy'],
                'REFRIGERANT'   => (string) $meta['refrigerant'],
                'WARRANTY_TEXT_RU'     => self::WARRANTY_RU,
                'WARRANTY_TEXT_EN'     => self::WARRANTY_EN,
                'KIT_TEXT_RU'          => self::KIT_RU,
                'KIT_TEXT_EN'          => self::KIT_EN,
                'INSTALLATION_TEXT_RU' => self::INSTALL_RU,
                'INSTALLATION_TEXT_EN' => self::INSTALL_EN,
                'FUNCTIONS'            => $this->functions,
                'GALLERY'              => $this->buildGalleryFiles(),
            ];

            \CIBlockElement::SetPropertyValuesEx($id, $productsId, $props);
            $this->out('  product %s обновлён', $code);
        }

        // ─── 2. Offers: per-offer specs based on AREA ──────────────────────
        $offerEntity = \Bitrix\Iblock\Iblock::wakeUp($offersId)->getEntityDataClass();
        $offerRows = $offerEntity::query()
            ->setSelect(['ID', 'AREA_VALUE' => 'AREA.VALUE'])
            ->exec();

        $touched = 0;
        while ($row = $offerRows->fetch()) {
            $area = (int) ($row['AREA_VALUE'] ?? 0);
            $specs = $this->specsByArea($area);

            \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $offersId, $specs);
            $touched++;
        }
        $this->outSuccess('Заполнено товаров: %d, offers: %d', count($productById), $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    /**
     * Five copies of the test image — gives the gallery slider something to
     * scroll through. Real product photos go via admin upload later.
     */
    private function buildGalleryFiles(): array
    {
        $path = $this->imagesDir . '/product.png';
        if (!is_file($path)) {
            return [];
        }
        $files = [];
        for ($i = 0; $i < 5; $i++) {
            $files[] = \CFile::MakeFileArray($path);
        }
        return $files;
    }

    /**
     * @return array<string, string>
     */
    private function specsByArea(int $area): array
    {
        // Approximation: cooling/heating roughly 0.11/0.12 kW per m².
        $cool = max(2.2, round($area * 0.11, 1));
        $heat = max(2.5, round($area * 0.12, 1));

        // Larger units are bigger physically.
        $indoorDims = match (true) {
            $area <= 25 => ['ru' => '740 × 285 × 187 мм', 'en' => '740 × 285 × 187 mm'],
            $area <= 50 => ['ru' => '960 × 327 × 230 мм', 'en' => '960 × 327 × 230 mm'],
            default     => ['ru' => '1101 × 327 × 249 мм', 'en' => '1101 × 327 × 249 mm'],
        };
        $outdoorDims = match (true) {
            $area <= 25 => ['ru' => '720 × 495 × 270 мм', 'en' => '720 × 495 × 270 mm'],
            $area <= 50 => ['ru' => '848 × 540 × 320 мм', 'en' => '848 × 540 × 320 mm'],
            default     => ['ru' => '958 × 660 × 402 мм', 'en' => '958 × 660 × 402 mm'],
        };
        $indoorWeight = match (true) {
            $area <= 25 => ['ru' => '9 кг',  'en' => '9 kg'],
            $area <= 50 => ['ru' => '12 кг', 'en' => '12 kg'],
            default     => ['ru' => '15 кг', 'en' => '15 kg'],
        };
        $outdoorWeight = match (true) {
            $area <= 25 => ['ru' => '23 кг', 'en' => '23 kg'],
            $area <= 50 => ['ru' => '32 кг', 'en' => '32 kg'],
            default     => ['ru' => '43 кг', 'en' => '43 kg'],
        };
        $noise = match (true) {
            $area <= 25 => ['ru' => '20-42 дБ', 'en' => '20-42 dB'],
            $area <= 50 => ['ru' => '22-45 дБ', 'en' => '22-45 dB'],
            default     => ['ru' => '24-48 дБ', 'en' => '24-48 dB'],
        };

        return [
            'COOLING_POWER_RU'      => $cool . ' кВт',
            'COOLING_POWER_EN'      => $cool . ' kW',
            'HEATING_POWER_RU'      => $heat . ' кВт',
            'HEATING_POWER_EN'      => $heat . ' kW',
            'NOISE_RU'              => $noise['ru'],
            'NOISE_EN'              => $noise['en'],
            'INDOOR_DIMENSIONS_RU'  => $indoorDims['ru'],
            'INDOOR_DIMENSIONS_EN'  => $indoorDims['en'],
            'OUTDOOR_DIMENSIONS_RU' => $outdoorDims['ru'],
            'OUTDOOR_DIMENSIONS_EN' => $outdoorDims['en'],
            'INDOOR_WEIGHT_RU'      => $indoorWeight['ru'],
            'INDOOR_WEIGHT_EN'      => $indoorWeight['en'],
            'OUTDOOR_WEIGHT_RU'     => $outdoorWeight['ru'],
            'OUTDOOR_WEIGHT_EN'     => $outdoorWeight['en'],
        ];
    }
}
