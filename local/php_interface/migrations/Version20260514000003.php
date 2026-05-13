<?php

namespace Sprint\Migration;

class Version20260514000003 extends Version
{
    protected $description = "Тестовые данные: товары (кондиционеры)";

    private array $codes = [
        'gree-bora-x-07',
        'gree-bora-x-09',
        'gree-pular-12',
        'gree-lomo-dc-09',
        'gree-hansol-iii-07',
        'gree-free-match-12',
        'gree-gwh18agd',
        'gree-vir09hp',
    ];

    public function up()
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');

        $typeWall       = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'TYPE', 'wall');
        $typeColumn     = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'TYPE', 'column');
        $typeIndustrial = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'TYPE', 'industrial');

        $colorWhite     = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'COLORS', 'white');
        $colorSilver    = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'COLORS', 'silver');
        $colorChampagne = $helper->Iblock()->getPropertyEnumIdByXmlId($iblockId, 'COLORS', 'champagne');

        $products = [
            [
                'fields' => [
                    'NAME'         => 'Gree BORA X 07',
                    'CODE'         => 'gree-bora-x-07',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 100,
                    'PREVIEW_TEXT' => 'Инверторный настенный кондиционер 7000 BTU. Площадь обслуживания до 20 кв.м. Уровень шума от 20 дБ. Класс энергоэффективности A++.',
                    'DETAIL_TEXT'  => '<p>Gree BORA X — флагманская серия настенных инверторных кондиционеров. Оснащена технологией G-Tech, обеспечивающей максимальную энергоэффективность и минимальный уровень шума.</p><p>Встроенный очиститель воздуха с фильтром Cold Catalyst удаляет бактерии, вирусы и неприятные запахи.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeWall,
                    'PRICE'          => 34990,
                    'AREA'           => 20,
                    'BESTSELLER'     => 'Y',
                    'INVERTER_MOTOR' => 'Y',
                    'COLORS'         => [$colorWhite, $colorSilver],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree BORA X 09',
                    'CODE'         => 'gree-bora-x-09',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 110,
                    'PREVIEW_TEXT' => 'Инверторный настенный кондиционер 9000 BTU. Площадь обслуживания до 25 кв.м. Wi-Fi управление через приложение GREE+.',
                    'DETAIL_TEXT'  => '<p>Модель BORA X 09 — оптимальный выбор для спален и небольших гостиных. Поддерживает управление через мобильное приложение GREE+ и работает с голосовыми помощниками Алиса и Google Home.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeWall,
                    'PRICE'          => 42990,
                    'AREA'           => 25,
                    'BESTSELLER'     => 'N',
                    'INVERTER_MOTOR' => 'Y',
                    'COLORS'         => [$colorWhite, $colorSilver],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree Pular 12',
                    'CODE'         => 'gree-pular-12',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 120,
                    'PREVIEW_TEXT' => 'Инверторный настенный кондиционер 12000 BTU. Площадь до 35 кв.м. Встроенная самоочистка испарителя.',
                    'DETAIL_TEXT'  => '<p>Серия Pular сочетает высокую производительность с интеллектуальной системой самоочистки. Испаритель автоматически промораживается и высушивается каждые 8 часов работы.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeWall,
                    'PRICE'          => 54990,
                    'AREA'           => 35,
                    'BESTSELLER'     => 'Y',
                    'INVERTER_MOTOR' => 'Y',
                    'COLORS'         => [$colorWhite],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree Lomo DC 09',
                    'CODE'         => 'gree-lomo-dc-09',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 130,
                    'PREVIEW_TEXT' => 'Инверторный настенный кондиционер 9000 BTU в стильном корпусе. Площадь до 25 кв.м. Цвет — шампань.',
                    'DETAIL_TEXT'  => '<p>Gree Lomo DC — дизайнерская серия для тех, кто ценит эстетику. Плавные линии корпуса и цветовое решение "шампань" органично вписываются в любой интерьер. Полный набор инверторных технологий Gree.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeWall,
                    'PRICE'          => 47990,
                    'AREA'           => 25,
                    'BESTSELLER'     => 'Y',
                    'INVERTER_MOTOR' => 'Y',
                    'COLORS'         => [$colorWhite, $colorChampagne],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree Hansol III 07',
                    'CODE'         => 'gree-hansol-iii-07',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 140,
                    'PREVIEW_TEXT' => 'Бюджетный настенный кондиционер 7000 BTU без инвертора. Площадь до 20 кв.м. Надёжное решение по доступной цене.',
                    'DETAIL_TEXT'  => '<p>Gree Hansol III — проверенная серия для тех, кому нужна надёжность без переплаты. Простое управление, стандартный режим охлаждения и обогрева, гарантия 3 года.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeWall,
                    'PRICE'          => 28990,
                    'AREA'           => 20,
                    'BESTSELLER'     => 'N',
                    'INVERTER_MOTOR' => 'N',
                    'COLORS'         => [$colorWhite],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree Free Match 12',
                    'CODE'         => 'gree-free-match-12',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 200,
                    'PREVIEW_TEXT' => 'Инверторный колонный кондиционер 12000 BTU. Площадь до 35 кв.м. Для магазинов, офисов и больших гостиных.',
                    'DETAIL_TEXT'  => '<p>Free Match — напольно-потолочная серия для помещений, где настенный монтаж невозможен или нежелателен. Устанавливается как на полу, так и под потолком. Мощный инверторный компрессор Gree G-Tech.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeColumn,
                    'PRICE'          => 89990,
                    'AREA'           => 35,
                    'BESTSELLER'     => 'N',
                    'INVERTER_MOTOR' => 'Y',
                    'COLORS'         => [$colorWhite, $colorSilver],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree GWH18AGD-K3DNA',
                    'CODE'         => 'gree-gwh18agd',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 210,
                    'PREVIEW_TEXT' => 'Колонный кондиционер 18000 BTU. Площадь до 50 кв.м. Для крупных торговых и офисных помещений.',
                    'DETAIL_TEXT'  => '<p>Мощный напольно-потолочный кондиционер для больших открытых пространств. Два направления воздушного потока — вверх и вниз. Встроенный дренажный насос.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeColumn,
                    'PRICE'          => 124990,
                    'AREA'           => 50,
                    'BESTSELLER'     => 'N',
                    'INVERTER_MOTOR' => 'N',
                    'COLORS'         => [$colorWhite],
                ],
            ],
            [
                'fields' => [
                    'NAME'         => 'Gree VIR09HP115V1B',
                    'CODE'         => 'gree-vir09hp',
                    'ACTIVE'       => 'Y',
                    'SORT'         => 300,
                    'PREVIEW_TEXT' => 'Промышленный кондиционер 9000 BTU. Площадь до 25 кв.м. Для серверных, производственных помещений.',
                    'DETAIL_TEXT'  => '<p>Промышленная серия Gree VIR предназначена для непрерывной круглосуточной эксплуатации. Усиленный корпус, защита от пыли и влаги IP54, диапазон рабочих температур от -40 до +55°C.</p>',
                ],
                'props' => [
                    'TYPE'           => $typeIndustrial,
                    'PRICE'          => 79990,
                    'AREA'           => 25,
                    'BESTSELLER'     => 'N',
                    'INVERTER_MOTOR' => 'N',
                    'COLORS'         => [$colorWhite],
                ],
            ],
        ];

        foreach ($products as $p) {
            $id = $helper->Iblock()->saveElement($iblockId, $p['fields'], $p['props']);
            $this->out('Товар добавлен: %s [id=%d]', $p['fields']['NAME'], $id);
        }

        $this->outSuccess('Все товары добавлены');
    }

    public function down()
    {
        $helper   = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('products');

        foreach ($this->codes as $code) {
            $helper->Iblock()->deleteElementIfExists($iblockId, $code);
            $this->out('Товар удалён: %s', $code);
        }

        $this->outSuccess('Товары удалены');
    }
}
