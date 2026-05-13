<?php

namespace Sprint\Migration;

class Version20260514000007 extends Version
{
    protected $description = "Тестовые данные: блоки главной страницы";

    public function up()
    {
        $helper = $this->getHelperManager();

        $this->seedSlider($helper);
        $this->seedGreeCards($helper);
        $this->seedGreeStats($helper);
        $this->seedAppFeatures($helper);
        $this->seedTechnologies($helper);
    }

    private function seedSlider(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('home_slider');

        $slides = [
            [
                'fields' => [
                    'NAME'   => 'Идеальные кондиционеры для Узбекистана',
                    'CODE'   => 'slide-main',
                    'ACTIVE' => 'Y',
                    'SORT'   => 100,
                ],
                'props' => [
                    'SUBTITLE'    => 'Охладят при <span>+50°C</span> и согреют при <span>−30°C</span>',
                    'BUTTON_TEXT' => 'Выбрать кондиционер',
                    'BUTTON_URL'  => '/catalog/',
                ],
            ],
            [
                'fields' => [
                    'NAME'   => 'Gree — мировой лидер среди производителей кондиционеров',
                    'CODE'   => 'slide-about',
                    'ACTIVE' => 'Y',
                    'SORT'   => 200,
                ],
                'props' => [
                    'SUBTITLE'    => 'Более <span>60 млн</span> кондиционеров в год — гарантия качества и опыта',
                    'BUTTON_TEXT' => 'Узнать о бренде',
                    'BUTTON_URL'  => '/brand/gree/',
                ],
            ],
        ];

        foreach ($slides as $s) {
            $elemId = $helper->Iblock()->saveElement($id, $s['fields'], $s['props']);
            $this->out('Слайд: %s [%d]', $s['fields']['NAME'], $elemId);
        }

        $this->outSuccess('Слайдер заполнен');
    }

    private function seedGreeCards(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('home_gree_cards');

        $cards = [
            [
                'fields' => ['NAME' => 'Гарантия',        'CODE' => 'guarantee',      'ACTIVE' => 'Y', 'SORT' => 100, 'PREVIEW_TEXT' => '10 лет гарантии на инвертор кондиционера'],
                'props'  => ['ICON_CODE' => 'thumbs-up'],
            ],
            [
                'fields' => ['NAME' => 'Доставка',        'CODE' => 'delivery',       'ACTIVE' => 'Y', 'SORT' => 200, 'PREVIEW_TEXT' => 'Бесплатно доставим в любую точку города'],
                'props'  => ['ICON_CODE' => 'truck'],
            ],
            [
                'fields' => ['NAME' => 'Рассрочка',       'CODE' => 'installment',    'ACTIVE' => 'Y', 'SORT' => 300, 'PREVIEW_TEXT' => 'Приобретайте комфорт сейчас, а платите потом'],
                'props'  => ['ICON_CODE' => 'dollar'],
            ],
            [
                'fields' => ['NAME' => 'Сервисный центр', 'CODE' => 'service-center', 'ACTIVE' => 'Y', 'SORT' => 400, 'PREVIEW_TEXT' => 'Свой сервисный центр — быстро решаем все вопросы'],
                'props'  => ['ICON_CODE' => 'wrench'],
            ],
        ];

        foreach ($cards as $c) {
            $elemId = $helper->Iblock()->saveElement($id, $c['fields'], $c['props']);
            $this->out('Карточка: %s [%d]', $c['fields']['NAME'], $elemId);
        }

        $this->outSuccess('Карточки «Почему Gree» заполнены');
    }

    private function seedGreeStats(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('home_gree_stats');

        $stats = [
            [
                'fields' => ['NAME' => '№1 в мире',     'CODE' => 'stat-world-first',  'ACTIVE' => 'Y', 'SORT' => 100, 'PREVIEW_TEXT' => 'По производству сплит-систем в 2024 году'],
                'props'  => ['NUMBER_PREFIX' => '№', 'NUMBER_VALUE' => 1,  'NUMBER_SUFFIX' => 'в мире'],
            ],
            [
                'fields' => ['NAME' => '46 технологий', 'CODE' => 'stat-technologies', 'ACTIVE' => 'Y', 'SORT' => 200, 'PREVIEW_TEXT' => 'Их используют другие бренды в своих кондиционерах'],
                'props'  => ['NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 46, 'NUMBER_SUFFIX' => 'технологий'],
            ],
            [
                'fields' => ['NAME' => '18 заводов',    'CODE' => 'stat-factories',    'ACTIVE' => 'Y', 'SORT' => 300, 'PREVIEW_TEXT' => 'По всему миру, а также 1411 лабораторий'],
                'props'  => ['NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 18, 'NUMBER_SUFFIX' => 'заводов'],
            ],
        ];

        foreach ($stats as $s) {
            $elemId = $helper->Iblock()->saveElement($id, $s['fields'], $s['props']);
            $this->out('Статистика: %s [%d]', $s['fields']['NAME'], $elemId);
        }

        $this->outSuccess('Статистика Gree заполнена');
    }

    private function seedAppFeatures(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('home_app_features');

        $features = [
            [
                'fields' => ['NAME' => 'Контроль из любой точки', 'CODE' => 'remote-control', 'ACTIVE' => 'Y', 'SORT' => 100, 'PREVIEW_TEXT' => 'Управляйте кондиционером дома, в офисе или в поездке'],
                'props'  => ['ICON_CODE' => 'remote'],
            ],
            [
                'fields' => ['NAME' => 'Экономия энергии',        'CODE' => 'energy-saving',  'ACTIVE' => 'Y', 'SORT' => 200, 'PREVIEW_TEXT' => 'Включайте кондиционер только когда это действительно нужно'],
                'props'  => ['ICON_CODE' => 'energy'],
            ],
        ];

        foreach ($features as $f) {
            $elemId = $helper->Iblock()->saveElement($id, $f['fields'], $f['props']);
            $this->out('Фича приложения: %s [%d]', $f['fields']['NAME'], $elemId);
        }

        $this->outSuccess('Фичи приложения заполнены');
    }

    private function seedTechnologies(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('home_technologies');

        $items = [
            ['NAME' => 'Работа в экстремальных условиях', 'CODE' => 'tech-extreme',   'PREVIEW_TEXT' => 'Стабильная работа при напряжении от 130В и температуре от −30°C до +53°C.'],
            ['NAME' => 'Интеллектуальное управление',     'CODE' => 'tech-smart',     'PREVIEW_TEXT' => 'Wi-Fi модуль, голосовые помощники и приложение GREE+ для полного контроля.'],
            ['NAME' => 'Система самоочистки',             'CODE' => 'tech-selfclean', 'PREVIEW_TEXT' => 'Автоматическая проморозка и сушка испарителя каждые 8 часов работы.'],
            ['NAME' => 'Инверторная технология',          'CODE' => 'tech-inverter',  'PREVIEW_TEXT' => 'Плавное регулирование мощности снижает шум и энергопотребление на 40%.'],
            ['NAME' => 'Функция I-FEEL',                  'CODE' => 'tech-ifeel',     'PREVIEW_TEXT' => 'Измеряет температуру рядом с вами, а не только в точке установки блока.'],
            ['NAME' => 'Ионизация воздуха',               'CODE' => 'tech-ionizer',   'PREVIEW_TEXT' => 'Cold Plasma нейтрализует бактерии, вирусы и поддерживает чистоту воздуха.'],
        ];

        foreach ($items as $i => $item) {
            $fields = array_merge($item, ['ACTIVE' => 'Y', 'SORT' => ($i + 1) * 100]);
            $elemId = $helper->Iblock()->saveElement($id, $fields);
            $this->out('Технология: %s [%d]', $item['NAME'], $elemId);
        }

        $this->outSuccess('Технологии заполнены');
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $map = [
            'home_slider'       => ['slide-main', 'slide-about'],
            'home_gree_cards'   => ['guarantee', 'delivery', 'installment', 'service-center'],
            'home_gree_stats'   => ['stat-world-first', 'stat-technologies', 'stat-factories'],
            'home_app_features' => ['remote-control', 'energy-saving'],
            'home_technologies' => ['tech-extreme', 'tech-smart', 'tech-selfclean', 'tech-inverter', 'tech-ifeel', 'tech-ionizer'],
        ];

        foreach ($map as $iblockCode => $codes) {
            $id = $helper->Iblock()->getIblockIdIfExists($iblockCode);
            foreach ($codes as $code) {
                $helper->Iblock()->deleteElementIfExists($id, $code);
            }
            $this->out('Данные инфоблока "%s" удалены', $iblockCode);
        }

        $this->outSuccess('Данные главной удалены');
    }
}
