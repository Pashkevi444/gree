<?php

namespace Sprint\Migration;

/**
 * Исправление тестовых товаров:
 *  - добавляет PREVIEW_PICTURE ко всем товарам (Version20260514000008 использовала неправильные коды)
 *  - обновляет цены с рублей на UZS
 *  - обновляет BESTSELLER / INVERTER_MOTOR до корректных значений
 */
class Version20260514000012 extends Version
{
    protected $description = "Исправление товаров: картинки + цены в UZS";

    private string $imagesDir;

    private array $products = [
        [
            'code'           => 'gree-bora-x-07',
            'price'          => 3_490_000,
            'bestseller'     => 'Y',
            'inverter_motor' => 'Y',
        ],
        [
            'code'           => 'gree-bora-x-09',
            'price'          => 4_290_000,
            'bestseller'     => 'N',
            'inverter_motor' => 'Y',
        ],
        [
            'code'           => 'gree-pular-12',
            'price'          => 5_490_000,
            'bestseller'     => 'Y',
            'inverter_motor' => 'Y',
        ],
        [
            'code'           => 'gree-lomo-dc-09',
            'price'          => 4_790_000,
            'bestseller'     => 'Y',
            'inverter_motor' => 'Y',
        ],
        [
            'code'           => 'gree-hansol-iii-07',
            'price'          => 2_890_000,
            'bestseller'     => 'N',
            'inverter_motor' => 'N',
        ],
        [
            'code'           => 'gree-free-match-12',
            'price'          => 8_990_000,
            'bestseller'     => 'N',
            'inverter_motor' => 'Y',
        ],
        [
            'code'           => 'gree-gwh18agd',
            'price'          => 12_490_000,
            'bestseller'     => 'N',
            'inverter_motor' => 'N',
        ],
        [
            'code'           => 'gree-vir09hp',
            'price'          => 7_990_000,
            'bestseller'     => 'N',
            'inverter_motor' => 'N',
        ],
    ];

    public function up(): void
    {
        $this->imagesDir = __DIR__ . '/images';
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');

        foreach ($this->products as $item) {
            $file = \CFile::MakeFileArray($this->imagesDir . '/product.png');

            $helper->Iblock()->saveElement(
                $iblockId,
                [
                    'CODE'            => $item['code'],
                    'PREVIEW_PICTURE' => $file,
                ],
                [
                    'PRICE'          => $item['price'],
                    'BESTSELLER'     => $item['bestseller'],
                    'INVERTER_MOTOR' => $item['inverter_motor'],
                ]
            );

            $this->out(
                'Обновлён «%s»: %s UZS, BESTSELLER=%s, INV=%s',
                $item['code'],
                number_format($item['price'], 0, '.', ' '),
                $item['bestseller'],
                $item['inverter_motor']
            );
        }

        $this->outSuccess('Все товары обновлены');
    }

    public function down(): void
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');

        foreach ($this->products as $item) {
            $helper->Iblock()->saveElement(
                $iblockId,
                [
                    'CODE'            => $item['code'],
                    'PREVIEW_PICTURE' => false,
                ],
                [
                    'PRICE' => 0,
                ]
            );
        }

        $this->outSuccess('Откат: картинки удалены, цены сброшены');
    }
}
