<?php

namespace Sprint\Migration;

/**
 * Заполняет деталку для gree-bora-x-07 — чтобы страница /catalog/gree-bora-x-07/
 * имела что показывать. Остальные товары можно дозаполнить через админку.
 *
 * Зависит от Version20260516000003 (свойства должны существовать).
 */
class Version20260516000004 extends Version
{
    protected $description = "Тестовая деталка для gree-bora-x-07";

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');
        if (!$iblockId) {
            $this->outError('Инфоблок products не найден');
            return;
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
        $row = $entity::query()
            ->where('CODE', 'gree-bora-x-07')
            ->setSelect(['ID'])
            ->exec()
            ->fetch();

        if (!$row) {
            $this->outError('Товар gree-bora-x-07 не найден');
            return;
        }

        $elementId = (int) $row['ID'];

        \CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, [
            'SKU'          => '1234567',
            'MODEL'        => 'GWH07AGA-K3NNA1B',
            'ENERGY_CLASS' => 'A++',
            'REFRIGERANT'  => 'R32',
            'IN_STOCK'     => 'Y',

            'COOLING_POWER_RU'     => '2.2 кВт',
            'COOLING_POWER_EN'     => '2.2 kW',
            'HEATING_POWER_RU'     => '2.5 кВт',
            'HEATING_POWER_EN'     => '2.5 kW',
            'NOISE_RU'             => '20-42 дБ',
            'NOISE_EN'             => '20-42 dB',
            'INDOOR_DIMENSIONS_RU' => '740 × 285 × 187 мм',
            'INDOOR_DIMENSIONS_EN' => '740 × 285 × 187 mm',
            'OUTDOOR_DIMENSIONS_RU'=> '720 × 495 × 270 мм',
            'OUTDOOR_DIMENSIONS_EN'=> '720 × 495 × 270 mm',
            'INDOOR_WEIGHT_RU'     => '9 кг',
            'INDOOR_WEIGHT_EN'     => '9 kg',
            'OUTDOOR_WEIGHT_RU'    => '23 кг',
            'OUTDOOR_WEIGHT_EN'    => '23 kg',

            'WARRANTY_TEXT_RU' => '<p>10 лет на инверторный компрессор. 3 года на электронные компоненты. 1 год на сервисное обслуживание и расходники.</p>',
            'WARRANTY_TEXT_EN' => '<p>10-year warranty on the inverter compressor. 3 years on electronics. 1 year on service and consumables.</p>',
            'KIT_TEXT_RU'      => '<p>В комплекте: внутренний блок, наружный блок, межблочный кабель, монтажная пластина, инфракрасный пульт ДУ с батарейками, паспорт изделия, гарантийный талон.</p>',
            'KIT_TEXT_EN'      => '<p>Package contents: indoor unit, outdoor unit, inter-unit cable, mounting plate, IR remote with batteries, product passport, warranty card.</p>',
            'INSTALLATION_TEXT_RU' => '<p>Установку выполняют сертифицированные специалисты сервисного центра Gree. Стандартный монтаж — 3 часа, включая прокладку трассы до 4 м, бурение, вакуумирование и пуск.</p>',
            'INSTALLATION_TEXT_EN' => '<p>Installation is performed by certified Gree service-center technicians. Standard installation takes ~3 hours: routing up to 4 m, drilling, vacuum and start-up.</p>',

            'FUNCTIONS' => ['wifi', 'energy-saving', 'turbo', 'silent', 'eco', 'smart-home'],
        ]);

        $this->outSuccess('Деталка gree-bora-x-07 заполнена');
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
