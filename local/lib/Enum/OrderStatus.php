<?php

declare(strict_types=1);

namespace Gree\Enum;

/**
 * Order lifecycle:
 *
 *   New ─→ Confirmed ─→ Shipped ─→ Delivered
 *     └────────────→ Cancelled (any time before Delivered)
 *
 * Stored as the string value in UF_STATUS. Validated on read so a bad value
 * in DB (manual admin edit, future migration) raises a clear error instead
 * of crashing the page.
 */
enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New        => 'Новый',
            self::Confirmed  => 'Подтверждён',
            self::Shipped    => 'Передан в доставку',
            self::Delivered  => 'Доставлен',
            self::Cancelled  => 'Отменён',
        };
    }
}
