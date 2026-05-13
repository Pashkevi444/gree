<?php

namespace Sprint\Migration;

class Version20260514000002 extends Version
{
    protected $description = "Тестовые данные: бренды";

    private array $codes = ['gree', 'haier', 'midea', 'daikin', 'samsung'];

    public function up()
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('brands');

        $brands = [
            [
                'NAME'         => 'Gree',
                'CODE'         => 'gree',
                'ACTIVE'       => 'Y',
                'SORT'         => 100,
                'PREVIEW_TEXT' => 'Крупнейший в мире производитель кондиционеров. Основан в 1991 году в г. Чжухай, Китай. Выпускает более 60 млн единиц техники ежегодно.',
            ],
            [
                'NAME'         => 'Haier',
                'CODE'         => 'haier',
                'ACTIVE'       => 'Y',
                'SORT'         => 200,
                'PREVIEW_TEXT' => 'Один из мировых лидеров по производству бытовой и климатической техники. Штаб-квартира в г. Циндао, Китай.',
            ],
            [
                'NAME'         => 'Midea',
                'CODE'         => 'midea',
                'ACTIVE'       => 'Y',
                'SORT'         => 300,
                'PREVIEW_TEXT' => 'Крупнейший производитель климатического оборудования. Входит в топ-3 мировых производителей кондиционеров.',
            ],
            [
                'NAME'         => 'Daikin',
                'CODE'         => 'daikin',
                'ACTIVE'       => 'Y',
                'SORT'         => 400,
                'PREVIEW_TEXT' => 'Японский производитель, пионер инверторных технологий. Основан в 1924 году в Осаке. Эталон надёжности и энергоэффективности.',
            ],
            [
                'NAME'         => 'Samsung',
                'CODE'         => 'samsung',
                'ACTIVE'       => 'Y',
                'SORT'         => 500,
                'PREVIEW_TEXT' => 'Южнокорейский технологический гигант с широкой линейкой климатической техники премиум-класса.',
            ],
        ];

        foreach ($brands as $brand) {
            $helper->Iblock()->saveElement($iblockId, $brand);
            $this->out('Бренд добавлен: %s', $brand['NAME']);
        }

        $this->outSuccess('Все бренды добавлены');
    }

    public function down()
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('brands');

        foreach ($this->codes as $code) {
            $helper->Iblock()->deleteElementIfExists($iblockId, $code);
            $this->out('Бренд удалён: %s', $code);
        }

        $this->outSuccess('Бренды удалены');
    }
}
