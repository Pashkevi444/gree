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
     * Places a new order from the visitor's current cart cookie + the
     * checkout form payload. Atomically:
     *   1. Validates input + cart not empty.
     *   2. Snapshots every cart item (price, name, color, area) from current
     *      catalog state.
     *   3. Inserts Orders header + OrderItems lines.
     *   4. Empties the cart (so the same items can't be re-ordered by a
     *      double-submit).
     *
     * Returns the persisted OrderDto with `publicId` / `id` populated.
     *
     * @throws \Gree\Service\Exception\CheckoutValidationException  per-field errors
     * @throws \Gree\Service\Exception\EmptyCartException           cart had no items
     */
    public function place(
        OrderCustomerDto $customer,
        OrderDeliveryDto $delivery,
        PaymentMethod $payment,
    ): OrderDto;

    /**
     * Looks up a placed order by its public ID. Returns null when not found —
     * controller renders 404 in that case.
     */
    public function findByPublicId(string $publicId): ?OrderDto;
}
