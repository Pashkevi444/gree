<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\DTO\OrderDto;
use Gree\Enum\PaymentMethod;

interface OrderServiceInterface
{
    /**
     * Атомарно: валидация → snapshot строк корзины → insert Orders+OrderItems → очистка корзины.
     *
     * @throws \Gree\Service\Exception\CheckoutValidationException per-field
     * @throws \Gree\Service\Exception\EmptyCartException
     */
    public function place(
        OrderCustomerDto $customer,
        OrderDeliveryDto $delivery,
        PaymentMethod $payment,
    ): OrderDto;

    public function findByPublicId(string $publicId): ?OrderDto;
}
