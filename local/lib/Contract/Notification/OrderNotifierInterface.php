<?php

declare(strict_types=1);

namespace Gree\Contract\Notification;

use Gree\DTO\OrderDto;

/**
 * Уведомление о новом заказе во внешний канал (e-mail).
 *
 * Контракт fail-soft: реализация НЕ должна кидать наружу — заказ уже создан,
 * доставка уведомления best-effort. Ошибку логируем critical внутри.
 */
interface OrderNotifierInterface
{
    public function notify(OrderDto $order): void;
}
