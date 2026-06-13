<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\OfferCollection;
use Gree\DTO\FilterDto;

interface OfferRepositoryInterface
{
    /**
     * @param int[] $productIds
     * @return array<int, OfferCollection>  product ID → offers
     */
    public function getByProductIds(array $productIds): array;

    /**
     * Применяет офферные фильтры (price/areas/colors), возвращает ID товаров с хотя бы одним подходящим оффером.
     *
     * @return int[]
     */
    public function findProductIds(FilterDto $filter): array;

    /** @param int[] $offerIds */
    public function getByIds(array $offerIds): OfferCollection;

    /** Лёгкая проверка — корзина валидирует client-supplied offerId без гидрации полного DTO. */
    public function existsActive(int $offerId): bool;
}
