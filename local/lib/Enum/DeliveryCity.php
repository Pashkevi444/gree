<?php

declare(strict_types=1);

namespace Gree\Enum;

/**
 * Города доставки. По ТЗ форма закреплена за Ташкентом; остальные регионы
 * Узбекистана менеджер уточняет вручную после звонка. Если перечень будет
 * расширен — добавь кейс сюда + строку `order.delivery.city.<value>`.
 */
enum DeliveryCity: string
{
    case Tashkent = 'tashkent';

    public function translationKey(): string
    {
        return 'order.delivery.city.' . $this->value;
    }
}
