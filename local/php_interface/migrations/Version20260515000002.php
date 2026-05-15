<?php

namespace Sprint\Migration;

/**
 * Сиды UI-переводов в Highloadblock «Translations» — все хардкодные строки
 * из шаблонов (header / footer / home / catalog / product / brand / blog / 404).
 *
 * Длинные текстовые блоки, лежащие в инфоблоках (DETAIL_TEXT/PREVIEW_TEXT и т.д.),
 * сюда не входят — для них используется механизм EN-дублей свойств инфоблоков.
 */
class Version20260515000002 extends Version
{
    protected $description = "Сиды UI-переводов";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        // header — language switch
        'header.lang.ru' => ['ru' => 'Рус', 'en' => 'Rus'],
        'header.lang.en' => ['ru' => 'Англ', 'en' => 'Eng'],

        // header — main nav
        'header.catalog' => ['ru' => 'Каталог', 'en' => 'Catalog'],
        'header.nav.brand' => ['ru' => 'О бренде', 'en' => 'About brand'],
        'header.nav.help' => ['ru' => 'Помощь', 'en' => 'Help'],
        'header.nav.buy' => ['ru' => 'Где купить', 'en' => 'Where to buy'],
        'header.nav.partners' => ['ru' => 'Партнёрам', 'en' => 'For partners'],
        'header.nav.contacts' => ['ru' => 'Контакты', 'en' => 'Contacts'],
        'header.cart' => ['ru' => 'Корзина', 'en' => 'Cart'],

        // footer
        'footer.description' => ['ru' => 'My Gree Group — официальный дистрибьютор Gree в Узбекистане', 'en' => 'My Gree Group — official Gree distributor in Uzbekistan'],
        'footer.col.catalog' => ['ru' => 'Каталог', 'en' => 'Catalog'],
        'footer.nav.wall' => ['ru' => 'Настенные', 'en' => 'Wall-mounted'],
        'footer.nav.column' => ['ru' => 'Колонные', 'en' => 'Column'],
        'footer.nav.industrial' => ['ru' => 'Промышленные', 'en' => 'Industrial'],
        'footer.col.company' => ['ru' => 'Компания', 'en' => 'Company'],
        'footer.nav.about' => ['ru' => 'О бренде', 'en' => 'About brand'],
        'footer.nav.payment' => ['ru' => 'Оплата', 'en' => 'Payment'],
        'footer.nav.delivery' => ['ru' => 'Доставка', 'en' => 'Delivery'],
        'footer.nav.exchange' => ['ru' => 'Обмен', 'en' => 'Exchange'],
        'footer.nav.return' => ['ru' => 'Возврат', 'en' => 'Return'],
        'footer.nav.service' => ['ru' => 'Сервисный центр', 'en' => 'Service center'],

        // breadcrumbs
        'breadcrumbs.home' => ['ru' => 'Главная', 'en' => 'Home'],
        'breadcrumbs.catalog' => ['ru' => 'Каталог', 'en' => 'Catalog'],
        'breadcrumbs.wall' => ['ru' => 'Настенные кондиционеры', 'en' => 'Wall-mounted air conditioners'],

        // 404
        '404.title' => ['ru' => 'Страница не найдена', 'en' => 'Page not found'],
        '404.description' => ['ru' => 'Запрашиваемая страница не существует или была удалена.', 'en' => 'The page you requested does not exist or has been removed.'],
        '404.home_link' => ['ru' => 'На главную', 'en' => 'Back to home'],

        // ─── catalog page ───────────────────────────────────────
        'catalog.title' => ['ru' => 'Каталог настенных кондиционеров Gree', 'en' => 'Gree wall-mounted air conditioners catalog'],
        'catalog.description' => ['ru' => 'Современные настенные кондиционеры для комфортного климата в вашем доме или офисе.', 'en' => 'Modern wall-mounted air conditioners for a comfortable climate in your home or office.'],
        'catalog.filters' => ['ru' => 'Фильтры', 'en' => 'Filters'],
        'catalog.filter.type' => ['ru' => 'Тип', 'en' => 'Type'],
        'catalog.filter.price' => ['ru' => 'Цена', 'en' => 'Price'],
        'catalog.filter.area' => ['ru' => 'Площадь', 'en' => 'Area'],
        'catalog.filter.inverter' => ['ru' => 'Инверторный двигатель', 'en' => 'Inverter motor'],
        'catalog.filter.bestseller' => ['ru' => 'Хит продаж', 'en' => 'Bestseller'],
        'catalog.filter.color' => ['ru' => 'Цвет', 'en' => 'Color'],
        'catalog.filter.refrigerant' => ['ru' => 'Тип хладагента', 'en' => 'Refrigerant type'],
        'catalog.filter.functions' => ['ru' => 'Функции', 'en' => 'Functions'],
        'catalog.filter.yes' => ['ru' => 'Да', 'en' => 'Yes'],
        'catalog.filter.no' => ['ru' => 'Нет', 'en' => 'No'],
        'catalog.reset' => ['ru' => 'Сбросить все', 'en' => 'Reset all'],
        'catalog.found' => ['ru' => 'Найдено :count моделей', 'en' => 'Found :count models'],
        'catalog.area.up_to' => ['ru' => 'до :area м²', 'en' => 'up to :area m²'],
        'catalog.sort.popular' => ['ru' => 'По популярности', 'en' => 'By popularity'],
        'catalog.sort.price_asc' => ['ru' => 'По цене (по возрастанию)', 'en' => 'Price (low to high)'],
        'catalog.sort.price_desc' => ['ru' => 'По цене (по убыванию)', 'en' => 'Price (high to low)'],

        // function checkboxes
        'function.wifi' => ['ru' => 'Wi-Fi', 'en' => 'Wi-Fi'],
        'function.130v' => ['ru' => 'Работа от 130V', 'en' => 'Operates from 130V'],
        'function.energy_saving' => ['ru' => 'Энергосбережение', 'en' => 'Energy saving'],
        'function.turbo' => ['ru' => 'Турборежим', 'en' => 'Turbo mode'],
        'function.silent' => ['ru' => 'Тихий режим', 'en' => 'Silent mode'],
        'function.eco' => ['ru' => 'Экорежим', 'en' => 'Eco mode'],
        'function.smart_home' => ['ru' => 'Умный дом', 'en' => 'Smart home'],
        'function.ai' => ['ru' => 'Искусственный интеллект', 'en' => 'Artificial intelligence'],

        // product types
        'product.type.wall' => ['ru' => 'Настенный', 'en' => 'Wall-mounted'],
        'product.type.column' => ['ru' => 'Колонный', 'en' => 'Column'],
        'product.type.industrial' => ['ru' => 'Промышленный', 'en' => 'Industrial'],
        'product.types.wall' => ['ru' => 'Настенные', 'en' => 'Wall-mounted'],
        'product.types.column' => ['ru' => 'Колонные', 'en' => 'Column'],
        'product.types.industrial' => ['ru' => 'Промышленные', 'en' => 'Industrial'],

        // product card / details
        'product.bestseller' => ['ru' => 'Хит продаж', 'en' => 'Bestseller'],
        'product.area' => ['ru' => 'Площадь — :area м²', 'en' => 'Area — :area m²'],
        'product.price_from' => ['ru' => 'от :price UZS', 'en' => 'from :price UZS'],
        'product.details' => ['ru' => 'Подробнее', 'en' => 'View details'],
        'product.add_to_cart' => ['ru' => 'Добавить в корзину', 'en' => 'Add to cart'],
        'product.article' => ['ru' => 'Артикул', 'en' => 'SKU'],
        'product.in_stock' => ['ru' => 'В наличии', 'en' => 'In stock'],
        'product.model' => ['ru' => 'Модель', 'en' => 'Model'],

        // product spec labels
        'spec.type' => ['ru' => 'Тип', 'en' => 'Type'],
        'spec.power_area' => ['ru' => 'Мощность (площадь применения)', 'en' => 'Power (coverage area)'],
        'spec.cooling_power' => ['ru' => 'Мощность охлаждения', 'en' => 'Cooling capacity'],
        'spec.heating_power' => ['ru' => 'Мощность обогрева', 'en' => 'Heating capacity'],
        'spec.energy_class' => ['ru' => 'Класс энергоэффективности', 'en' => 'Energy efficiency class'],
        'spec.noise' => ['ru' => 'Уровень шума (внутренний блок)', 'en' => 'Noise level (indoor unit)'],
        'spec.area' => ['ru' => 'Площадь помещения', 'en' => 'Room area'],
        'spec.indoor_dimensions' => ['ru' => 'Габариты внутреннего блока', 'en' => 'Indoor unit dimensions'],
        'spec.indoor_weight' => ['ru' => 'Вес внутреннего блока', 'en' => 'Indoor unit weight'],
        'spec.outdoor_weight' => ['ru' => 'Вес наружного блока', 'en' => 'Outdoor unit weight'],
        'spec.refrigerant' => ['ru' => 'Хладагент', 'en' => 'Refrigerant'],
        'spec.inverter' => ['ru' => 'Инвертор', 'en' => 'Inverter'],
        'spec.kit' => ['ru' => 'Комплектация', 'en' => 'Included items'],
        'spec.specs' => ['ru' => 'Технические характеристики', 'en' => 'Specifications'],
        'spec.installation' => ['ru' => 'Установка', 'en' => 'Installation'],

        // colors
        'color.white' => ['ru' => 'Белый', 'en' => 'White'],
        'color.silver' => ['ru' => 'Серебристый', 'en' => 'Silver'],
        'color.black' => ['ru' => 'Чёрный', 'en' => 'Black'],
        'color.champagne' => ['ru' => 'Шампань', 'en' => 'Champagne'],

        // ─── home page ───────────────────────────────────────────
        'home.anchor.wall' => ['ru' => 'Настенные', 'en' => 'Wall-mounted'],
        'home.anchor.column' => ['ru' => 'Колонные', 'en' => 'Column'],
        'home.anchor.industrial' => ['ru' => 'Промышленные', 'en' => 'Industrial'],
        'home.anchor.wall.range' => ['ru' => 'до 80 м²', 'en' => 'up to 80 m²'],
        'home.anchor.column.range' => ['ru' => 'до 200 м²', 'en' => 'up to 200 m²'],
        'home.anchor.industrial.range' => ['ru' => 'от 100 м²', 'en' => 'from 100 m²'],
        'home.tech.description' => ['ru' => 'Ключевые преимущества кондиционеров Gree', 'en' => 'Key advantages of Gree air conditioners'],
        'home.section.wall.title' => ['ru' => 'Настенные кондиционеры', 'en' => 'Wall-mounted air conditioners'],
        'home.section.wall.desc' => ['ru' => 'Подходят для площадей до 80 м²', 'en' => 'Suitable for areas up to 80 m²'],
        'home.section.column.title' => ['ru' => 'Колонные кондиционеры', 'en' => 'Column air conditioners'],
        'home.section.column.desc' => ['ru' => 'Подходят для площадей до 200 м²', 'en' => 'Suitable for areas up to 200 m²'],
        'home.section.industrial.title' => ['ru' => 'Промышленные кондиционеры', 'en' => 'Industrial air conditioners'],
        'home.section.industrial.desc' => ['ru' => 'Климат-контроль помещений любых площадей и сложности по индивидуальному проекту', 'en' => 'Climate control for spaces of any size and complexity, by custom design'],
        'home.section.viewAll' => ['ru' => 'Посмотреть все модели', 'en' => 'View all models'],
        'home.gree.title' => ['ru' => 'Почему выбирают Gree', 'en' => 'Why choose Gree'],
        'home.gree.description' => ['ru' => 'Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев использования.', 'en' => 'Gree is a global leader in air conditioner manufacturing with proprietary technologies, strict quality control, and solutions for every use case.'],
        'home.gree.cta' => ['ru' => 'Узнать больше о Gree', 'en' => 'Learn more about Gree'],
        'home.app.title' => ['ru' => 'Управляйте кондиционером со смартфона', 'en' => 'Control your air conditioner from your smartphone'],
        'home.app.description' => ['ru' => 'Меняйте температуру, режим, таймер и скорость вентиляции из любой точки мира. Удобное управление и контроль', 'en' => 'Adjust temperature, mode, timer, and fan speed from anywhere in the world. Convenient management and control.'],
        'home.tech.title' => ['ru' => 'Технологии для вашего комфорта', 'en' => 'Technologies for your comfort'],
        'brand.about.title' => ['ru' => 'О компании Gree', 'en' => 'About Gree'],
        'home.tips.title' => ['ru' => 'Полезные советы для вашего дома', 'en' => 'Useful tips for your home'],
        'home.tips.subtitle' => ['ru' => 'Полезные советы и новости от экспертов Gree', 'en' => 'Useful tips and news from Gree experts'],
        'home.help.title' => ['ru' => 'Нужна помощь', 'en' => 'Need help'],

        // Gree advantage cards (also used on brand page)
        'gree.card.warranty.title' => ['ru' => 'Гарантия', 'en' => 'Warranty'],
        'gree.card.warranty.desc' => ['ru' => 'Мы уверены в качестве нашей техники и предоставляем расширенную гарантию', 'en' => 'We stand behind our products with an extended warranty'],
        'gree.card.delivery.title' => ['ru' => 'Доставка', 'en' => 'Delivery'],
        'gree.card.delivery.desc' => ['ru' => 'Бесплатно доставим в любую точку города', 'en' => 'Free delivery anywhere in the city'],
        'gree.card.installment.title' => ['ru' => 'Рассрочка', 'en' => 'Installment'],
        'gree.card.installment.desc' => ['ru' => 'Приобретайте комфорт сейчас, а платите потом', 'en' => 'Get comfort now, pay later'],
        'gree.card.service.title' => ['ru' => 'Сервисный центр', 'en' => 'Service center'],
        'gree.card.service.desc' => ['ru' => 'Свой сервисный центр — быстро решаем все вопросы', 'en' => 'In-house service center — fast issue resolution'],

        // stats / numbers
        'home.stat.world_1' => ['ru' => '№1 в мире', 'en' => '#1 worldwide'],
        'home.stat.world_1.desc' => ['ru' => 'По производству сплит-систем в 2024 году', 'en' => 'In split-system manufacturing in 2024'],
        'home.stat.tech' => ['ru' => '46 технологий', 'en' => '46 technologies'],
        'home.stat.tech.desc' => ['ru' => 'Их используют другие бренды в своих кондиционерах', 'en' => 'Used by other brands in their air conditioners'],
        'home.stat.factories' => ['ru' => '18 заводов', 'en' => '18 factories'],
        'home.stat.factories.desc' => ['ru' => 'По всему миру, а также 1411 лабораторий', 'en' => 'Worldwide, plus 1,411 laboratories'],

        // blog
        'blog.title' => ['ru' => 'Полезные советы', 'en' => 'Useful tips'],
        'blog.read_more' => ['ru' => 'Подробнее', 'en' => 'Read more'],
        'blog.show_more' => ['ru' => 'Показать ещё', 'en' => 'Show more'],
        'blog.news' => ['ru' => 'Новости', 'en' => 'News'],
        'blog.section' => ['ru' => 'Блог', 'en' => 'Blog'],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');

        if (!$hlblockId) {
            $this->outError('Highloadblock «Translations» не найден');
            return;
        }

        foreach ($this->entries as $code => $values) {
            $helper->Hlblock()->addElement($hlblockId, [
                'UF_CODE'     => $code,
                'UF_VALUE_RU' => $values['ru'],
                'UF_VALUE_EN' => $values['en'],
            ]);
        }

        $this->outSuccess('Загружено переводов: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
