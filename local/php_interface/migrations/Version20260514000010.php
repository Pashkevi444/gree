<?php

namespace Sprint\Migration;

class Version20260514000010 extends Version
{
    protected $description = "Тестовые данные и картинки для страницы бренда";

    private string $imagesDir;

    public function up()
    {
        $this->imagesDir = __DIR__ . '/images';
        $helper = $this->getHelperManager();

        $this->seedHistory($helper);
        $this->seedWhyGree($helper);
        $this->seedGreeCards($helper);
        $this->seedGreeStats($helper);
        $this->seedAboutCards($helper);
        $this->seedTechnologies($helper);
    }

    private function seedHistory(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('brand_history');

        $helper->Iblock()->saveElement($id, [
            'NAME'             => 'История бренда',
            'CODE'             => 'history',
            'ACTIVE'           => 'Y',
            'SORT'             => 100,
            'DETAIL_TEXT_TYPE' => 'html',
            'DETAIL_TEXT'      => '<p>Развитие GREE начинается с 1991 года, когда на юге Китая (город Чжухай) два предприятия Guanxiong Plastic Company и Haili Air Conditioner Factory объединились в корпорацию Gree Air Conditioner Factory.</p>'
                . '<p>Становление корпорации начиналось с открытия одного завода, производившего оконные кондиционеры для внутреннего рынка. Изначально в компании работало 200 человек, а годовой выпуск продукции не превышал 20 тыс. единиц.</p>'
                . '<p>В наши дни Компания насчитывает более 90 000 сотрудников, включая 16 000 сотрудников НИОКР и более 30 000 технических работников.</p>'
                . '<p>GREE — это самый крупный производитель кондиционеров в Китае и один из крупнейших мировых производителей.</p>',
        ]);
        $this->outSuccess('История бренда заполнена');
    }

    private function seedWhyGree(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('brand_why_gree');

        $helper->Iblock()->saveElement($id, [
            'NAME'         => 'Почему выбирают Gree',
            'CODE'         => 'why-gree',
            'ACTIVE'       => 'Y',
            'SORT'         => 100,
            'PREVIEW_TEXT' => 'Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев использования.',
        ], [
            'BUTTON_TEXT' => 'Узнать больше о Gree',
            'BUTTON_URL'  => '/brand/gree/',
        ]);
        $this->outSuccess('Блок "Почему выбирают Gree" заполнен');
    }

    private function seedGreeCards(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('brand_gree_cards');

        $cards = [
            ['NAME' => 'Гарантия',        'CODE' => 'guarantee',      'SORT' => 100, 'PREVIEW_TEXT' => '10 лет гарантии на инвертор кондиционера',           'ICON_CODE' => 'thumbs-up'],
            ['NAME' => 'Доставка',        'CODE' => 'delivery',       'SORT' => 200, 'PREVIEW_TEXT' => 'Бесплатно доставим в любую точку города',             'ICON_CODE' => 'truck'],
            ['NAME' => 'Рассрочка',       'CODE' => 'installment',    'SORT' => 300, 'PREVIEW_TEXT' => 'Приобретайте комфорт сейчас, а платите потом',        'ICON_CODE' => 'dollar'],
            ['NAME' => 'Сервисный центр', 'CODE' => 'service-center', 'SORT' => 400, 'PREVIEW_TEXT' => 'Свой сервисный центр — быстро решаем все вопросы',    'ICON_CODE' => 'wrench'],
        ];

        foreach ($cards as $card) {
            $props  = ['ICON_CODE' => $card['ICON_CODE']];
            $fields = array_diff_key($card, ['ICON_CODE' => null]);
            $fields['ACTIVE'] = 'Y';
            $helper->Iblock()->saveElement($id, $fields, $props);
            $this->out('Карточка "%s" добавлена', $card['NAME']);
        }
        $this->outSuccess('Карточки преимуществ заполнены');
    }

    private function seedGreeStats(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('brand_gree_stats');

        $stats = [
            ['NAME' => '500 млн клиентов', 'CODE' => 'stat-clients',   'SORT' => 100, 'PREVIEW_TEXT' => 'Довольных клиентов', 'NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 500000000, 'NUMBER_SUFFIX' => ''],
            ['NAME' => '18 заводов',        'CODE' => 'stat-factories', 'SORT' => 200, 'PREVIEW_TEXT' => 'Заводов',            'NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 18,        'NUMBER_SUFFIX' => ''],
            ['NAME' => '1411 лабораторий',  'CODE' => 'stat-labs',      'SORT' => 300, 'PREVIEW_TEXT' => 'Лабораторий',        'NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 1411,      'NUMBER_SUFFIX' => ''],
            ['NAME' => '16000 инженеров',   'CODE' => 'stat-engineers', 'SORT' => 400, 'PREVIEW_TEXT' => 'Инженеров',          'NUMBER_PREFIX' => '',  'NUMBER_VALUE' => 16000,     'NUMBER_SUFFIX' => ''],
        ];

        foreach ($stats as $stat) {
            $props  = ['NUMBER_PREFIX' => $stat['NUMBER_PREFIX'], 'NUMBER_VALUE' => $stat['NUMBER_VALUE'], 'NUMBER_SUFFIX' => $stat['NUMBER_SUFFIX']];
            $fields = ['NAME' => $stat['NAME'], 'CODE' => $stat['CODE'], 'ACTIVE' => 'Y', 'SORT' => $stat['SORT'], 'PREVIEW_TEXT' => $stat['PREVIEW_TEXT']];
            $helper->Iblock()->saveElement($id, $fields, $props);
            $this->out('Стат "%s" добавлена', $stat['NAME']);
        }
        $this->outSuccess('Статистика бренда заполнена');
    }

    private function seedAboutCards(HelperManager $helper): void
    {
        $id = $helper->Iblock()->getIblockIdIfExists('brand_about_cards');

        $cards = [
            [
                'NAME' => 'Достижения',
                'CODE' => 'achievements',
                'SORT' => 100,
                'DETAIL_TEXT' => 'GREE — мировой лидер в производстве кондиционеров. Собственные научные центры, тысячи патентов и миллионы довольных клиентов по всему миру. На наших заводах собираются не только устройства GREE, но и техника для других мировых брендов.',
            ],
            [
                'NAME' => 'Миссия',
                'CODE' => 'mission',
                'SORT' => 200,
                'DETAIL_TEXT' => 'Мы создаём умные климатические решения, которые делают жизнь комфортнее, чище и тише. GREE — это технологии, которые работают на вас каждый день, без шума, перегрева и компромиссов. Климат, которому доверяют.',
            ],
            [
                'NAME' => 'Контроль качества',
                'CODE' => 'quality',
                'SORT' => 300,
                'DETAIL_TEXT' => 'От первого винта до финального теста — каждый кондиционер GREE проходит многоуровневый контроль. Мы не передаём производство на аутсорс — вся сборка и разработка под нашим полным контролем. Это и есть гарантия качества.',
            ],
            [
                'NAME' => 'Инновации',
                'CODE' => 'innovations',
                'SORT' => 400,
                'DETAIL_TEXT' => 'У GREE — 152 исследовательских центра по всему миру. Мы создаём технологии завтрашнего дня: от интеллектуальных инверторов до систем очистки воздуха. Каждая модель — результат глубоких инженерных разработок, а не просто сборка.',
            ],
        ];

        foreach ($cards as $card) {
            $helper->Iblock()->saveElement($id, array_merge($card, ['ACTIVE' => 'Y', 'DETAIL_TEXT_TYPE' => 'text']));
            $this->out('Карточка "%s" добавлена', $card['NAME']);
        }
        $this->outSuccess('Карточки "О компании" заполнены');
    }

    private function seedTechnologies(HelperManager $helper): void
    {
        $id   = $helper->Iblock()->getIblockIdIfExists('brand_technologies');
        $file = \CFile::MakeFileArray($this->imagesDir . '/technology.png');

        $items = [
            [
                'NAME' => 'Инновационный трансформатор SMPS',
                'CODE' => 'tech-smps',
                'SORT' => 100,
                'DETAIL_TEXT' => 'Инновационный импульсный трансформатор SMPS обеспечивает стабильную работу кондиционера при перепадах напряжения, снижает энергопотребление и повышает надёжность системы. Современное решение для эффективной и долговечной работы климатической техники.',
            ],
            [
                'NAME' => 'Низкий уровень шума',
                'CODE' => 'tech-silence',
                'SORT' => 200,
                'DETAIL_TEXT' => 'Благодаря инверторной технологии и оптимизированной конструкции вентиляторов кондиционеры GREE работают практически бесшумно. Комфорт без раздражающего гула — идеальный выбор для спальни, детской или офиса.',
            ],
            [
                'NAME' => 'Функция I-FEEL',
                'CODE' => 'tech-ifeel',
                'SORT' => 300,
                'DETAIL_TEXT' => 'Датчик температуры, установленный в беспроводном пульте дистанционного управления, измеряет температуру воздуха в месте своего нахождения и передаёт эту информацию внутреннему блоку. Кондиционер работает так, чтобы достичь заданных параметров по месту нахождения пульта.',
            ],
            [
                'NAME' => 'Комфортный «Ночной режим»',
                'CODE' => 'tech-night',
                'SORT' => 400,
                'DETAIL_TEXT' => 'Ночной режим автоматически снижает уровень шума и мягко регулирует температуру, создавая оптимальные условия для сна. Без перепадов, сквозняков и перегрева — только комфорт и глубокий отдых всю ночь.',
            ],
        ];

        foreach ($items as $item) {
            $fields = array_merge($item, ['ACTIVE' => 'Y', 'DETAIL_TEXT_TYPE' => 'text', 'PREVIEW_PICTURE' => $file]);
            $helper->Iblock()->saveElement($id, $fields);
            $this->out('Технология "%s" добавлена', $item['NAME']);
        }
        $this->outSuccess('Технологии бренда заполнены');
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $map = [
            'brand_history'      => ['history'],
            'brand_why_gree'     => ['why-gree'],
            'brand_gree_cards'   => ['guarantee', 'delivery', 'installment', 'service-center'],
            'brand_gree_stats'   => ['stat-clients', 'stat-factories', 'stat-labs', 'stat-engineers'],
            'brand_about_cards'  => ['achievements', 'mission', 'quality', 'innovations'],
            'brand_technologies' => ['tech-smps', 'tech-silence', 'tech-ifeel', 'tech-night'],
        ];

        foreach ($map as $iblockCode => $codes) {
            $id = $helper->Iblock()->getIblockIdIfExists($iblockCode);
            foreach ($codes as $code) {
                $helper->Iblock()->deleteElementIfExists($id, $code);
            }
            $this->out('Данные инфоблока "%s" удалены', $iblockCode);
        }

        $this->outSuccess('Тестовые данные бренда удалены');
    }
}
