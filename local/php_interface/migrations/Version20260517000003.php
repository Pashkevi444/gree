<?php

namespace Sprint\Migration;

/**
 * UI-переводы для страницы корзины + хлебных крошек.
 */
class Version20260517000003 extends Version
{
    protected $description = "UI-переводы: cart.* / breadcrumbs.cart";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'breadcrumbs.cart' => ['ru' => 'Корзина', 'en' => 'Cart'],

        'cart.title'                  => ['ru' => 'Корзина', 'en' => 'Cart'],
        'cart.empty'                  => ['ru' => 'Корзина пуста', 'en' => 'Your cart is empty'],
        'cart.checkout'               => ['ru' => 'Перейти к оформлению', 'en' => 'Proceed to checkout'],

        'cart.item.color'             => ['ru' => 'Цвет', 'en' => 'Color'],
        'cart.item.area'              => ['ru' => 'Площадь применения', 'en' => 'Application area'],
        'cart.item.qty'               => ['ru' => 'Количество', 'en' => 'Quantity'],
        'cart.item.area_unit'         => ['ru' => 'до :area м²', 'en' => 'up to :area m²'],

        'cart.summary.title'          => ['ru' => 'Ваш заказ', 'en' => 'Your order'],
        'cart.summary.items'          => ['ru' => ':count товара', 'en' => ':count items'],
        'cart.summary.delivery'       => ['ru' => 'Доставка', 'en' => 'Delivery'],
        'cart.summary.delivery_free'  => ['ru' => 'Бесплатно', 'en' => 'Free'],
        'cart.summary.total'          => ['ru' => 'Итого', 'en' => 'Total'],
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
