<?php

declare(strict_types=1);

namespace Gree\Contract\Service;

use Gree\Collection\CartLineCollection;

interface CartServiceInterface
{
    /**
     * Enriched cart lines for the current visitor. Returns an empty collection
     * if no cart cookie is set — read-only access never creates a row.
     */
    public function view(): CartLineCollection;

    /**
     * Add an offer to the cart. If the same offer is already there, quantity
     * is incremented. Creates a cart on the fly if the visitor has none.
     * Returns the cart_items row ID.
     */
    public function add(int $offerId, int $quantity = 1): int;

    /**
     * Set the absolute quantity for a line. Non-positive value removes it.
     */
    public function update(int $itemId, int $quantity): void;

    public function remove(int $itemId): void;
}
