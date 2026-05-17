<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\CartItemCollection;
use Gree\Collection\OfferCollection;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\DTO\CartItemDto;
use Gree\Enum\Color;
use Gree\Service\CartService;
use PHPUnit\Framework\TestCase;

final class CartServiceTest extends TestCase
{
    private CartRepositoryInterface $carts;
    private CartItemRepositoryInterface $items;
    private OfferRepositoryInterface $offers;
    private ProductRepositoryInterface $products;
    private CartTokenServiceInterface $tokens;
    private CartService $service;

    protected function setUp(): void
    {
        $this->carts    = $this->createMock(CartRepositoryInterface::class);
        $this->items    = $this->createMock(CartItemRepositoryInterface::class);
        $this->offers   = $this->createMock(OfferRepositoryInterface::class);
        $this->products = $this->createMock(ProductRepositoryInterface::class);
        $this->tokens   = $this->createMock(CartTokenServiceInterface::class);

        $this->service = new CartService(
            $this->carts,
            $this->items,
            $this->offers,
            $this->products,
            $this->tokens,
        );
    }

    public function testAddingExistingOfferIncrementsQuantity(): void
    {
        $this->tokens->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->with('tok')->willReturn(5);
        $this->offers->method('existsActive')->with(88)->willReturn(true);

        $existing = new CartItemDto(id: 99, cartId: 5, offerId: 88, quantity: 2);
        $this->items->method('findOne')->with(5, 88)->willReturn($existing);

        $this->items->expects($this->once())
            ->method('updateQuantity')
            ->with(99, 5); // 2 + 3
        $this->items->expects($this->never())->method('insert');

        $this->carts->expects($this->once())->method('touch')->with(5);

        $this->service->add(offerId: 88, quantity: 3);
    }

    public function testAddingNewOfferInserts(): void
    {
        $this->tokens->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->with('tok')->willReturn(5);
        $this->offers->method('existsActive')->with(88)->willReturn(true);

        $this->items->method('findOne')->willReturn(null);
        $this->items->expects($this->once())
            ->method('insert')
            ->with(5, 88, 1)
            ->willReturn(123);
        $this->items->expects($this->never())->method('updateQuantity');

        $itemId = $this->service->add(offerId: 88, quantity: 1);

        $this->assertSame(123, $itemId);
    }

    public function testAddCreatesCartIfTokenMissing(): void
    {
        $this->tokens->method('read')->willReturn(null);
        $this->tokens->expects($this->once())->method('issue')->willReturn('new-tok');
        $this->carts->method('findIdByToken')->with('new-tok')->willReturn(null);
        $this->carts->expects($this->once())
            ->method('createWithToken')
            ->with('new-tok')
            ->willReturn(42);
        $this->offers->method('existsActive')->with(88)->willReturn(true);

        $this->items->method('findOne')->with(42, 88)->willReturn(null);
        $this->items->expects($this->once())->method('insert')->with(42, 88, 2)->willReturn(7);

        $this->service->add(offerId: 88, quantity: 2);
    }

    public function testAddRejectsUnknownOffer(): void
    {
        $this->offers->expects($this->once())
            ->method('existsActive')
            ->with(999)
            ->willReturn(false);

        $this->items->expects($this->never())->method('insert');
        $this->items->expects($this->never())->method('updateQuantity');
        // Should not even create a cart for a bogus offer
        $this->carts->expects($this->never())->method('createWithToken');

        $this->expectException(\Gree\Service\Exception\OfferNotFoundException::class);
        $this->service->add(offerId: 999, quantity: 1);
    }

    public function testUpdateQuantityToZeroDeletesItem(): void
    {
        $this->seedOwnedItem(itemId: 99, cartId: 5);

        $this->items->expects($this->once())->method('delete')->with(99);
        $this->items->expects($this->never())->method('updateQuantity');

        $this->service->update(itemId: 99, quantity: 0);
    }

    public function testUpdateQuantityClampsNegativeToZeroAndDeletes(): void
    {
        $this->seedOwnedItem(itemId: 99, cartId: 5);
        $this->items->expects($this->once())->method('delete')->with(99);
        $this->service->update(itemId: 99, quantity: -3);
    }

    public function testUpdatePositiveQuantityCallsUpdate(): void
    {
        $this->seedOwnedItem(itemId: 99, cartId: 5);

        $this->items->expects($this->once())->method('updateQuantity')->with(99, 4);
        $this->items->expects($this->never())->method('delete');

        $this->service->update(itemId: 99, quantity: 4);
    }

    public function testRemoveDelegatesToRepository(): void
    {
        $this->seedOwnedItem(itemId: 99, cartId: 5);

        $this->items->expects($this->once())->method('delete')->with(99);
        $this->service->remove(itemId: 99);
    }

    public function testUpdateRejectsItemFromAnotherCart(): void
    {
        $this->tokens->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);
        $this->items->method('findById')
            ->with(99)
            ->willReturn(new CartItemDto(id: 99, cartId: 7, offerId: 88, quantity: 1));

        $this->items->expects($this->never())->method('updateQuantity');
        $this->items->expects($this->never())->method('delete');

        $this->expectException(\Gree\Security\AccessDeniedException::class);
        $this->service->update(itemId: 99, quantity: 2);
    }

    public function testUpdateRejectsAnonymousCaller(): void
    {
        $this->tokens->method('read')->willReturn(null);
        $this->items->expects($this->never())->method('updateQuantity');
        $this->items->expects($this->never())->method('delete');

        $this->expectException(\Gree\Security\AccessDeniedException::class);
        $this->service->update(itemId: 99, quantity: 2);
    }

    public function testViewReturnsEmptyCollectionIfNoToken(): void
    {
        $this->tokens->method('read')->willReturn(null);
        // view() must not issue a token / create a cart for read-only access
        $this->tokens->expects($this->never())->method('issue');
        $this->carts->expects($this->never())->method('createWithToken');

        $lines = $this->service->view();

        $this->assertSame(0, $lines->count());
    }

    public function testViewReturnsEmptyIfTokenWithoutCart(): void
    {
        $this->tokens->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(null);

        $lines = $this->service->view();

        $this->assertSame(0, $lines->count());
    }

    /**
     * Convenience: stub the IDOR guard chain so update/remove can run.
     */
    private function seedOwnedItem(int $itemId, int $cartId): void
    {
        $this->tokens->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn($cartId);
        $this->items->method('findById')
            ->with($itemId)
            ->willReturn(new CartItemDto(id: $itemId, cartId: $cartId, offerId: 88, quantity: 1));
    }
}
