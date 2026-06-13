<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\CartLineCollection;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CartServiceInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\DTO\CartLineDto;
use Gree\Logging\FileLogger;
use Gree\Service\Exception\OfferNotFoundException;

final class CartService extends BaseService implements CartServiceInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $carts,
        private readonly CartItemRepositoryInterface $items,
        private readonly OfferRepositoryInterface $offers,
        private readonly ProductRepositoryInterface $products,
        private readonly CartTokenServiceInterface $tokens,
    ) {}

    public function view(): CartLineCollection
    {
        try {
            $token = $this->tokens->read();
            if ($token === null) {
                return new CartLineCollection();
            }
            $cartId = $this->carts->findIdByToken($token);
            if ($cartId === null) {
                return new CartLineCollection();
            }

            $items = $this->items->listByCart($cartId);
            if ($items->isEmpty()) {
                return new CartLineCollection();
            }

            $offerIds = [];
            foreach ($items as $item) {
                $offerIds[] = $item->offerId;
            }

            $offerCollection = $this->offers->getByIds($offerIds);
            $offersById = [];
            $productIds = [];
            foreach ($offerCollection as $offer) {
                $offersById[$offer->id] = $offer;
                $productIds[$offer->productId] = $offer->productId;
            }

            $productsById = $this->products->getByIds(array_values($productIds));

            $lines = [];
            foreach ($items as $item) {
                $offer = $offersById[$item->offerId] ?? null;
                if ($offer === null) {
                    continue;
                }
                $product = $productsById[$offer->productId] ?? null;
                if ($product === null) {
                    continue;
                }

                $lines[] = new CartLineDto(
                    id:          $item->id,
                    offerId:     $offer->id,
                    productId:   $product->id,
                    productName: $product->name,
                    productCode: $product->code,
                    image:       $product->image,
                    productUrl:  \Gree\Helpers\Route::to('catalog.product', [
                        'section' => $product->type->slug(),
                        'code'    => $product->code,
                    ]),
                    unitPrice:   $offer->price,
                    quantity:    $item->quantity,
                    totalPrice:  $offer->price * $item->quantity,
                    area:        $offer->area,
                    color:       $offer->color,
                    inStock:     $offer->inStock,
                );
            }

            return new CartLineCollection(...$lines);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return new CartLineCollection();
        }
    }

    public function add(int $offerId, int $quantity = 1): int
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        try {
            // Валидация offer ДО создания корзины — bogus offer_id даст 422 без orphan-корзин.
            if (!$this->offers->existsActive($offerId)) {
                throw new OfferNotFoundException('offer #' . $offerId . ' not found or inactive');
            }

            $cartId = $this->resolveOrCreateCart();

            $existing = $this->items->findOne($cartId, $offerId);
            if ($existing !== null) {
                $newQty = $existing->quantity + $quantity;
                $this->items->updateQuantity($existing->id, $newQty);
                $this->carts->touch($cartId);
                return $existing->id;
            }

            $itemId = $this->items->insert($cartId, $offerId, $quantity);
            $this->carts->touch($cartId);
            return $itemId;
        } catch (OfferNotFoundException $e) {
            // Domain-rejection — без critical-лога.
            throw $e;
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'offerId' => $offerId,
                'quantity' => $quantity,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function update(int $itemId, int $quantity): void
    {
        try {
            $this->assertItemBelongsToCurrentCart($itemId);

            if ($quantity <= 0) {
                $this->items->delete($itemId);
                return;
            }
            $this->items->updateQuantity($itemId, $quantity);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'itemId' => $itemId,
                'quantity' => $quantity,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function remove(int $itemId): void
    {
        try {
            $this->assertItemBelongsToCurrentCart($itemId);
            $this->items->delete($itemId);
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'itemId' => $itemId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /** IDOR-guard: UF_CART_ID должен совпасть с корзиной из cookie. Любая ошибка → AccessDenied (404 палил бы существование). */
    private function assertItemBelongsToCurrentCart(int $itemId): void
    {
        $token = $this->tokens->read();
        if ($token === null) {
            throw new \Gree\Security\AccessDeniedException('no cart cookie');
        }
        $cartId = $this->carts->findIdByToken($token);
        if ($cartId === null) {
            throw new \Gree\Security\AccessDeniedException('cart not found');
        }
        $item = $this->items->findById($itemId);
        if ($item === null || $item->cartId !== $cartId) {
            throw new \Gree\Security\AccessDeniedException('item not in current cart');
        }
    }

    /** Ищет корзину по cookie-токену, иначе создаёт новую (с новым cookie). Всегда возвращает валидный cart_id. */
    private function resolveOrCreateCart(): int
    {
        $token = $this->tokens->read() ?? $this->tokens->issue();
        $cartId = $this->carts->findIdByToken($token);
        return $cartId ?? $this->carts->createWithToken($token);
    }
}
