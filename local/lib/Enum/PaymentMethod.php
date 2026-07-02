<?php

declare(strict_types=1);

namespace Gree\Enum;

/**
 * Способы оплаты, доступные на чекауте. Перечень — из ТЗ (xlsx, лист
 * «Корзина»): карта (Humo/Uzcard/Visa/MasterCard), рассрочка UZUM, рассрочка
 * Anorbank. Перевод названия — через UI-строку `order.payment.<value>`.
 */
enum PaymentMethod: string
{
    case Card = 'card';
    case UzumBank = 'uzum_bank';
    case AnorBank = 'anor_bank';

    /**
     * Translation key for the human-readable label. Resolves through Language::t.
     */
    public function translationKey(): string
    {
        return 'order.payment.' . $this->value;
    }

    /** Русский лейбл для админки / уведомлений менеджеру (UI покупателя берёт translationKey). */
    public function label(): string
    {
        return match ($this) {
            self::Card     => 'Карта',
            self::UzumBank => 'Рассрочка UZUM',
            self::AnorBank => 'Рассрочка Anorbank',
        };
    }

    public static function tryFromOrNull(?string $value): ?self
    {
        return ($value === null || $value === '') ? null : self::tryFrom($value);
    }
}
