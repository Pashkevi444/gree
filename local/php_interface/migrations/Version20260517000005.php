<?php

namespace Sprint\Migration;

/**
 * Сидим SEO-записи для всех статических страниц сайта.
 *
 * Контент составлен из доменных особенностей бренда Gree + сегмент рынка
 * Узбекистан. Если придёт ТЗ от заказчика — править прямо в Bitrix-админке,
 * не нужно перекатывать миграцию.
 */
class Version20260517000005 extends Version
{
    protected $description = "Сидим SEO статических страниц";

    /**
     * Текст уложен под ТЗ Gree (xlsx). Цифры/факты — каждый третий кондиционер
     * в мире, 18 заводов, 1411 лабораторий, 46 технологий, бренд №1 в 2024,
     * охлаждение +50°/обогрев −30°, 10 лет гарантии на инвертор — оттуда же.
     * H1-заголовки совпадают с ТЗ дословно.
     *
     * @var array<int, array<string, string>>
     */
    private array $entries = [
        [
            'UF_PAGE_CODE'         => 'home',
            'UF_TITLE_RU'          => 'Gree Узбекистан — идеальные кондиционеры для климата от +50 до −30 °C',
            'UF_TITLE_EN'          => 'Gree Uzbekistan — perfect air conditioners for +50 to −30 °C climate',
            'UF_DESCRIPTION_RU'    => 'Каждый третий кондиционер в мире — Gree. Бренд №1 по производству сплит-систем в 2024 году. Доставка по Узбекистану, 10 лет гарантии на инвертор, собственный сервисный центр.',
            'UF_DESCRIPTION_EN'    => 'Every third AC in the world is a Gree. World №1 split-system manufacturer in 2024. Delivery across Uzbekistan, 10-year warranty on inverter, in-house service center.',
            'UF_KEYWORDS_RU'       => 'gree, кондиционер, сплит-система, инвертор, узбекистан, ташкент, охлаждение, обогрев',
            'UF_KEYWORDS_EN'       => 'gree, air conditioner, split system, inverter, uzbekistan, tashkent, cooling, heating',
            'UF_OG_TITLE_RU'       => 'Идеальные кондиционеры Gree для Узбекистана',
            'UF_OG_TITLE_EN'       => 'Perfect Gree air conditioners for Uzbekistan',
            'UF_OG_DESCRIPTION_RU' => 'Охлаждают при +50 °C, обогревают при −30 °C. 10 лет гарантии на инвертор.',
            'UF_OG_DESCRIPTION_EN' => 'Cool at +50 °C, heat at −30 °C. 10-year warranty on inverter.',
            'UF_OG_IMAGE'          => '/dist/images/6311bac5bcac86eed9564a7ad5501eda4bbfd3a0.png',
        ],
        [
            'UF_PAGE_CODE'         => 'catalog',
            // H1 из ТЗ: «Каталог сплит-систем Gree»
            'UF_TITLE_RU'          => 'Каталог сплит-систем Gree — настенные, колонные, промышленные',
            'UF_TITLE_EN'          => 'Gree split-system catalog — wall, column, industrial',
            'UF_DESCRIPTION_RU'    => 'Настенные (до 80 м²), колонные (до 200 м²) и промышленные кондиционеры Gree. Фильтр по цене, мощности и цвету. Доставка по Узбекистану.',
            'UF_DESCRIPTION_EN'    => 'Wall-mounted (up to 80 m²), column (up to 200 m²) and industrial Gree ACs. Filter by price, power, color. Delivery across Uzbekistan.',
            'UF_KEYWORDS_RU'       => 'каталог gree, сплит-система, настенный, колонный, промышленный, кондиционер',
            'UF_KEYWORDS_EN'       => 'gree catalog, split system, wall, column, industrial, ac',
            'UF_OG_TITLE_RU'       => 'Каталог сплит-систем Gree',
            'UF_OG_TITLE_EN'       => 'Gree split-system catalog',
            'UF_OG_DESCRIPTION_RU' => 'Настенные, колонные и промышленные модели — подбор под любую площадь.',
            'UF_OG_DESCRIPTION_EN' => 'Wall, column and industrial models for any room area.',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'catalog-nastennie',
            // H1 из ТЗ: «Каталог настенных кондиционеров Gree»
            'UF_TITLE_RU'          => 'Каталог настенных кондиционеров Gree — до 80 м²',
            'UF_TITLE_EN'          => 'Wall-mounted Gree air conditioners — up to 80 m²',
            'UF_DESCRIPTION_RU'    => 'Настенные сплит-системы Gree для квартир и офисов до 80 м². Инвертор, Wi-Fi, режим самоочистки. 10 лет гарантии на инвертор, бесплатная доставка по Узбекистану.',
            'UF_DESCRIPTION_EN'    => 'Wall-mounted Gree split systems for apartments and offices up to 80 m². Inverter, Wi-Fi, self-clean. 10-year inverter warranty, free delivery across Uzbekistan.',
            'UF_KEYWORDS_RU'       => 'настенный кондиционер, gree, сплит-система, инвертор, wi-fi, самоочистка',
            'UF_KEYWORDS_EN'       => 'wall ac, gree, split system, inverter, wi-fi, self-clean',
            'UF_OG_TITLE_RU'       => 'Настенные кондиционеры Gree',
            'UF_OG_TITLE_EN'       => 'Wall-mounted Gree air conditioners',
            'UF_OG_DESCRIPTION_RU' => 'Для площадей до 80 м². Инвертор, тихий режим, самоочистка.',
            'UF_OG_DESCRIPTION_EN' => 'For areas up to 80 m². Inverter, silent mode, self-clean.',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'catalog-kolonnye',
            // H1 из ТЗ: «Каталог колонных кондиционеров Gree»
            'UF_TITLE_RU'          => 'Каталог колонных кондиционеров Gree — до 200 м²',
            'UF_TITLE_EN'          => 'Gree column-type air conditioners — up to 200 m²',
            'UF_DESCRIPTION_RU'    => 'Колонные напольно-потолочные кондиционеры Gree для помещений до 200 м². Торговые залы, рестораны, конференц-комнаты. Бесплатная доставка по Узбекистану.',
            'UF_DESCRIPTION_EN'    => 'Floor-ceiling column Gree ACs for spaces up to 200 m². Retail halls, restaurants, conference rooms. Free delivery across Uzbekistan.',
            'UF_KEYWORDS_RU'       => 'колонный кондиционер, напольно-потолочный, gree, коммерческий',
            'UF_KEYWORDS_EN'       => 'column ac, floor-ceiling, gree, commercial',
            'UF_OG_TITLE_RU'       => 'Колонные кондиционеры Gree',
            'UF_OG_TITLE_EN'       => 'Gree column air conditioners',
            'UF_OG_DESCRIPTION_RU' => 'Для помещений до 200 м² — торговля, общепит, офисы.',
            'UF_OG_DESCRIPTION_EN' => 'For spaces up to 200 m² — retail, HoReCa, offices.',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'catalog-promyshlennye',
            // H1 из ТЗ: «Каталог промышленных кондиционеров Gree»
            'UF_TITLE_RU'          => 'Каталог промышленных кондиционеров Gree — VRF, чиллеры',
            'UF_TITLE_EN'          => 'Industrial Gree air conditioners catalog — VRF, chillers',
            // ТЗ дословно: «Климат-контроль помещений любых площадей и сложности по индивидуальному проекту»
            'UF_DESCRIPTION_RU'    => 'Промышленные кондиционеры Gree — климат-контроль помещений любых площадей и сложности по индивидуальному проекту. VRF, чиллеры, прецизионные системы.',
            'UF_DESCRIPTION_EN'    => 'Industrial Gree air conditioners — climate control for any area and complexity on a per-project basis. VRF, chillers, precision systems.',
            'UF_KEYWORDS_RU'       => 'промышленный кондиционер, vrf, чиллер, gree, hvac, климат-контроль',
            'UF_KEYWORDS_EN'       => 'industrial ac, vrf, chiller, gree, hvac, climate control',
            'UF_OG_TITLE_RU'       => 'Промышленные кондиционеры Gree',
            'UF_OG_TITLE_EN'       => 'Industrial Gree air conditioners',
            'UF_OG_DESCRIPTION_RU' => 'Климат-контроль любой сложности по индивидуальному проекту.',
            'UF_OG_DESCRIPTION_EN' => 'Custom climate-control solutions of any complexity.',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'brand',
            'UF_TITLE_RU'          => 'О бренде Gree — №1 в мире по производству сплит-систем в 2024 году',
            'UF_TITLE_EN'          => 'About Gree — World №1 split-system manufacturer in 2024',
            // ТЗ дословно: «18 заводов по всему миру и 1411 лабораторий», «46 лидирующих технологий»
            'UF_DESCRIPTION_RU'    => 'Gree с 1991 года: 18 заводов по всему миру, 1411 лабораторий, 46 лидирующих технологий, 90 000 сотрудников. Каждый третий кондиционер в мире — Gree.',
            'UF_DESCRIPTION_EN'    => 'Gree since 1991: 18 factories worldwide, 1,411 laboratories, 46 leading technologies, 90,000 employees. Every third AC in the world is a Gree.',
            'UF_KEYWORDS_RU'       => 'gree, бренд, история, производство, технологии, 1991',
            'UF_KEYWORDS_EN'       => 'gree, brand, history, manufacturing, technology, 1991',
            'UF_OG_TITLE_RU'       => 'О бренде Gree',
            'UF_OG_TITLE_EN'       => 'About Gree',
            'UF_OG_DESCRIPTION_RU' => 'Мировой лидер климатического оборудования. 1991 — сегодня.',
            'UF_OG_DESCRIPTION_EN' => 'World HVAC leader. 1991 — today.',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'blog',
            // ТЗ дословно: H1 «Блог Gree», подзаголовок «Полезные советы и новости от экспертов Gree для вашего дома»
            'UF_TITLE_RU'          => 'Блог Gree — полезные советы и новости для вашего дома',
            'UF_TITLE_EN'          => 'Gree blog — helpful tips and news for your home',
            'UF_DESCRIPTION_RU'    => 'Полезные советы и новости от экспертов Gree для вашего дома. Выбор, монтаж, обслуживание кондиционеров. Новости компании и рынка.',
            'UF_DESCRIPTION_EN'    => 'Helpful tips and news from Gree experts for your home. Choosing, installing and maintaining ACs. Company and market news.',
            'UF_KEYWORDS_RU'       => 'gree, блог, советы, новости, кондиционер, обслуживание',
            'UF_KEYWORDS_EN'       => 'gree, blog, tips, news, air conditioner, maintenance',
            'UF_OG_TITLE_RU'       => 'Блог Gree',
            'UF_OG_TITLE_EN'       => 'Gree blog',
            'UF_OG_DESCRIPTION_RU' => 'Полезные советы и новости от экспертов Gree для вашего дома.',
            'UF_OG_DESCRIPTION_EN' => 'Helpful tips and news from Gree experts for your home.',
            'UF_OG_IMAGE'          => '/dist/images/f836c0d91ded99c33b3c8e7fb5bcc0fa6f6da8d3.png',
        ],
        [
            'UF_PAGE_CODE'         => 'cart',
            'UF_TITLE_RU'          => 'Корзина — Gree Узбекистан',
            'UF_TITLE_EN'          => 'Cart — Gree Uzbekistan',
            // ТЗ дословно: «Доставка по Ташкенту бесплатно до подъезда (1 день)», варианты оплаты Humo/Uzcard/Visa/MasterCard, рассрочки ANORBANK/UZUM
            'UF_DESCRIPTION_RU'    => 'Ваш заказ кондиционеров Gree. Бесплатная доставка по Ташкенту, оплата картой Humo/Uzcard/Visa/MasterCard, рассрочка ANORBANK и UZUM.',
            'UF_DESCRIPTION_EN'    => 'Your Gree air conditioner order. Free Tashkent delivery, Humo/Uzcard/Visa/MasterCard payment, ANORBANK and UZUM installment plans.',
            'UF_KEYWORDS_RU'       => 'корзина, заказ, gree, доставка, рассрочка, ташкент',
            'UF_KEYWORDS_EN'       => 'cart, order, gree, delivery, installment, tashkent',
            'UF_OG_TITLE_RU'       => 'Корзина',
            'UF_OG_TITLE_EN'       => 'Cart',
            'UF_OG_DESCRIPTION_RU' => '',
            'UF_OG_DESCRIPTION_EN' => '',
            'UF_OG_IMAGE'          => '',
        ],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Seo');
        if (!$hlblockId) {
            $this->outError('HL «Seo» не найден — сначала Version20260517000004');
            return;
        }

        foreach ($this->entries as $row) {
            $helper->Hlblock()->addElement($hlblockId, $row);
            $this->out('  + %s', $row['UF_PAGE_CODE']);
        }

        $this->outSuccess('Загружено SEO-записей: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется (записи удалятся вместе с HL-блоком)');
    }
}
