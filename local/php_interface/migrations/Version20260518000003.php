<?php

namespace Sprint\Migration;

/**
 * UI-переводы для страниц оформления заказа: /order/ и /order/success/{id}/.
 *
 * Тексты — дословно из ТЗ Gree (xlsx), листы «Корзина» (шаг 2 + шаг 3):
 *   - доставка по Ташкенту бесплатно (1 день);
 *   - оплата: карта Humo/Uzcard/Visa/MasterCard, рассрочка ANORBANK, UZUM;
 *   - «Благодарим за заказ!» + 15 минут.
 */
class Version20260518000003 extends Version
{
    protected $description = "UI-переводы: order.* / order_success.*";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        // ── breadcrumbs ──────────────────────────────────────────────────────
        'breadcrumbs.order'             => ['ru' => 'Оформление заказа', 'en' => 'Checkout'],

        // ── page title / hero ────────────────────────────────────────────────
        'order.title'                   => ['ru' => 'Оформление заказа', 'en' => 'Checkout'],

        // ── form: contacts ────────────────────────────────────────────────────
        'order.contacts.title'          => ['ru' => 'Контакты', 'en' => 'Contacts'],
        'order.contacts.name'           => ['ru' => 'Имя', 'en' => 'Name'],
        'order.contacts.name_placeholder' => ['ru' => 'Укажите как вас зовут', 'en' => 'Tell us your name'],
        'order.contacts.phone'          => ['ru' => 'Номер телефона', 'en' => 'Phone'],
        'order.contacts.phone_placeholder' => ['ru' => '+998 98 123 45 67', 'en' => '+998 98 123 45 67'],
        'order.contacts.telegram'       => ['ru' => 'Телеграм', 'en' => 'Telegram'],
        'order.contacts.telegram_placeholder' => ['ru' => '@ваш_ник в телеграме', 'en' => '@your_handle'],

        // ── form: delivery ───────────────────────────────────────────────────
        'order.delivery.title'          => ['ru' => 'Доставка', 'en' => 'Delivery'],
        'order.delivery.note'           => [
            'ru' => 'Доставка по Ташкенту бесплатно до подъезда (1 день). Доставка по Узбекистану — сроки и стоимость уточнит менеджер при подтверждении заказа.',
            'en' => 'Free delivery within Tashkent (1 day). For other cities of Uzbekistan a manager will confirm the timeline and price.',
        ],
        'order.delivery.city'           => ['ru' => 'Город', 'en' => 'City'],
        'order.delivery.city.tashkent'  => ['ru' => 'Ташкент', 'en' => 'Tashkent'],
        'order.delivery.street'         => ['ru' => 'Адрес доставки', 'en' => 'Street'],
        'order.delivery.street_placeholder' => ['ru' => 'Укажите улицу', 'en' => 'Street name'],
        'order.delivery.house'          => ['ru' => 'Дом', 'en' => 'House'],
        'order.delivery.house_placeholder' => ['ru' => 'Укажите дом', 'en' => 'House number'],
        'order.delivery.apartment'      => ['ru' => 'Квартира', 'en' => 'Apartment'],
        'order.delivery.apartment_placeholder' => ['ru' => 'Укажите, если есть — необязательно', 'en' => 'Optional'],
        'order.delivery.comment'        => ['ru' => 'Комментарий', 'en' => 'Comment'],
        'order.delivery.comment_placeholder' => ['ru' => 'Оставьте комментарий менеджеру — необязательно', 'en' => 'Optional note for the manager'],

        // ── form: payment ────────────────────────────────────────────────────
        'order.payment.title'           => ['ru' => 'Способ оплаты', 'en' => 'Payment method'],
        'order.payment.card'            => ['ru' => 'Оплата картой (Humo, Uzcard, Visa, MasterCard)', 'en' => 'Card (Humo, Uzcard, Visa, MasterCard)'],
        'order.payment.uzum_bank'       => ['ru' => 'Рассрочка от Uzum Bank', 'en' => 'Uzum Bank installment'],
        'order.payment.anor_bank'       => ['ru' => 'Рассрочка от Anorbank', 'en' => 'Anorbank installment'],

        // ── sidebar ──────────────────────────────────────────────────────────
        'order.sidebar.title'           => ['ru' => 'Ваш заказ', 'en' => 'Your order'],
        'order.sidebar.total'           => ['ru' => 'Итого', 'en' => 'Total'],
        'order.sidebar.submit'          => ['ru' => 'Перейти к оплате', 'en' => 'Proceed to payment'],

        // ── inline errors / validation ──────────────────────────────────────
        'order.error.empty_cart'        => ['ru' => 'Корзина пуста — добавьте товары перед оформлением.', 'en' => 'Your cart is empty — add items before checkout.'],
        'order.error.invalid'           => ['ru' => 'Проверьте корректность заполнения формы.', 'en' => 'Please review the form for errors.'],
        'order.error.network'           => ['ru' => 'Ошибка соединения. Попробуйте ещё раз.', 'en' => 'Network error. Please try again.'],

        // ── success page ─────────────────────────────────────────────────────
        'order_success.title'           => ['ru' => 'Благодарим за заказ!', 'en' => 'Thank you for your order!'],
        'order_success.description'     => [
            'ru' => 'Наш менеджер свяжется с вами для уточнения деталей заказа в течение 15 минут (в рабочее время).',
            'en' => 'Our manager will contact you within 15 minutes (during business hours) to confirm the details.',
        ],
        'order_success.cta'             => ['ru' => 'Вернуться на главную', 'en' => 'Back to home'],
        'order_success.signature'       => ['ru' => 'Ваш Gree', 'en' => 'Yours, Gree'],

        // ── cart "checkout" button (ранее в Корзина блейде была пустая <a href="">) ──
        'cart.checkout'                 => ['ru' => 'Перейти к оформлению', 'en' => 'Proceed to checkout'],
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

        $this->outSuccess('Загружено: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
