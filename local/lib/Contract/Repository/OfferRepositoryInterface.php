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
}
