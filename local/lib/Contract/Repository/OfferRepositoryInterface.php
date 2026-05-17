<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\OfferCollection;
use Gree\DTO\FilterDto;

interface OfferRepositoryInterface
{
    /**
     * Pull all active offers for the given product IDs in one query, grouped
     * by parent product.
     *
     * @param int[] $productIds
     * @return array<int, OfferCollection>  product ID → its offers
     */
    public function getByProductIds(array $productIds): array;

    /**
     * Apply offer-level filters (price range, areas, colors) and return the
     * IDs of parent products that have at least one matching offer.
     *
     * @return int[]
     */
    public function findProductIds(FilterDto $filter): array;

    /**
     * Load offers by ID. Returns a single flat collection — each OfferDto
     * carries its productId, so the cart layer can resolve parent products.
     *
     * @param int[] $offerIds
     */
    public function getByIds(array $offerIds): OfferCollection;

    /**
     * Lightweight existence check — used by the cart layer to validate
     * client-supplied offer IDs without hydrating the full DTO graph.
     */
    public function existsActive(int $offerId): bool;
}
