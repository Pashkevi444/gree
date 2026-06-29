<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Service;

use Gree\Collection\CartItemCollection;
use Gree\Collection\OfferCollection;
use Gree\Contract\DB\TransactionServiceInterface;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\OrderItemRepositoryInterface;
use Gree\Contract\Repository\OrderRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\DTO\CartItemDto;
use Gree\DTO\OfferDto;
use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\DTO\ProductDto;
use Gree\Enum\Color;
use Gree\DTO\CityDto;
use Gree\Enum\Locale;
use Gree\Enum\OrderStatus;
use Gree\Enum\PaymentMethod;
use Gree\Enum\ProductType;
use Gree\Http\InMemoryHttpContext;
use Gree\Service\Exception\CheckoutValidationException;
use Gree\Service\Exception\EmptyCartException;
use Gree\Service\OrderService;
use PHPUnit\Framework\TestCase;

final class OrderServiceTest extends TestCase
{
    private OrderRepositoryInterface $orders;
    private OrderItemRepositoryInterface $orderItems;
    private CartTokenServiceInterface $cartToken;
    private CartRepositoryInterface $carts;
    private CartItemRepositoryInterface $cartItems;
    private OfferRepositoryInterface $offers;
    private ProductRepositoryInterface $products;
    private LanguageServiceInterface $language;
    private InMemoryHttpContext $http;
    private TransactionServiceInterface $tx;
    private OrderService $service;

    protected function setUp(): void
    {
        $this->orders     = $this->createMock(OrderRepositoryInterface::class);
        $this->orderItems = $this->createMock(OrderItemRepositoryInterface::class);
        $this->cartToken  = $this->createMock(CartTokenServiceInterface::class);
        $this->carts      = $this->createMock(CartRepositoryInterface::class);
        $this->cartItems  = $this->createMock(CartItemRepositoryInterface::class);
        $this->offers     = $this->createMock(OfferRepositoryInterface::class);
        $this->products   = $this->createMock(ProductRepositoryInterface::class);
        $this->language   = $this->createMock(LanguageServiceInterface::class);
        $this->language->method('get')->willReturn(Locale::Ru);
        $this->http = new InMemoryHttpContext(remoteAddress: '10.0.0.1');
        $this->http->setHeader('User-Agent', 'IntegrationTest');

        $this->orders->method('publicIdExists')->willReturn(false);

        $this->tx = $this->createMock(TransactionServiceInterface::class);
        // Default behaviour: run() executes the callback immediately. Tests that
        // assert rollback override this method specifically.
        $this->tx->method('run')->willReturnCallback(static fn(callable $cb) => $cb());

        $this->service = new OrderService(
            $this->orders,
            $this->orderItems,
            $this->cartToken,
            $this->carts,
            $this->cartItems,
            $this->offers,
            $this->products,
            $this->language,
            $this->http,
            $this->tx,
        );
    }

    public function testPlaceCapturesPriceAtOrderTimeAndClearsCart(): void
    {
        $this->cartToken->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);

        $cartLine = new CartItemDto(id: 99, cartId: 5, offerId: 88, quantity: 2);
        $this->cartItems->method('listByCart')->willReturn(new CartItemCollection($cartLine));

        $offer = new OfferDto(
            id: 88,
            productId: 17,
            price: 3_000_000,
            area: 30,
            color: Color::White,
            inStock: true,
        );
        $this->offers->method('getByIds')->with([88])->willReturn(new OfferCollection($offer));

        $product = new ProductDto(
            id: 17,
            name: 'Gree BORA X 07',
            code: 'gree-bora-x-07',
            type: ProductType::Wall,
            price: 3_000_000,
            area: 30,
        );
        $this->products->method('getByIds')->with([17])->willReturn([17 => $product]);

        // Order header inserted with the SNAPSHOTED total = 2 × 3_000_000
        $this->orders->expects($this->once())
            ->method('insert')
            ->willReturnCallback(function ($orderDto) {
                $this->assertSame(6_000_000, $orderDto->total);
                $this->assertSame(2, $orderDto->itemsCount);
                $this->assertSame(OrderStatus::New, $orderDto->status);
                $this->assertSame('Иван', $orderDto->customer->name);
                $this->assertSame(PaymentMethod::Card, $orderDto->payment);
                $this->assertSame('10.0.0.1', $orderDto->ip);
                $this->assertSame('IntegrationTest', $orderDto->userAgent);
                return 1001;
            });

        // One snapshot line inserted, with the productName + unitPrice captured.
        $this->orderItems->expects($this->once())
            ->method('insert')
            ->willReturnCallback(function ($item) {
                $this->assertSame(1001, $item->orderId);
                $this->assertSame(88, $item->offerId);
                $this->assertSame('Gree BORA X 07', $item->productName);
                $this->assertSame(2, $item->quantity);
                $this->assertSame(3_000_000, $item->unitPrice);
                $this->assertSame(6_000_000, $item->totalPrice);
                return 9001;
            });

        // Cart cleared on success.
        $this->cartItems->expects($this->once())
            ->method('delete')
            ->with(99);

        $order = $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'Amir Temur', house: '1'),
            PaymentMethod::Card,
        );

        $this->assertSame(1001, $order->id);
        $this->assertNotSame('', $order->publicId);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{12}$/', $order->publicId);
    }

    public function testPlaceRejectsEmptyName(): void
    {
        $this->expectException(CheckoutValidationException::class);
        $this->service->place(
            new OrderCustomerDto(name: '', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPlaceRejectsBadPhone(): void
    {
        $this->expectException(CheckoutValidationException::class);
        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: 'not a phone'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPlaceRejectsMissingStreet(): void
    {
        $this->expectException(CheckoutValidationException::class);
        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: '', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPlaceFailsOnEmptyCart(): void
    {
        $this->cartToken->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);
        $this->cartItems->method('listByCart')->willReturn(new CartItemCollection());

        $this->orders->expects($this->never())->method('insert');

        $this->expectException(EmptyCartException::class);
        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPlaceFailsWhenNoCartCookie(): void
    {
        $this->cartToken->method('read')->willReturn(null);
        $this->orders->expects($this->never())->method('insert');

        $this->expectException(EmptyCartException::class);
        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPlaceSkipsLinesWithMissingOffer(): void
    {
        $this->cartToken->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);
        $this->cartItems->method('listByCart')->willReturn(new CartItemCollection(
            new CartItemDto(id: 99, cartId: 5, offerId: 88, quantity: 1),
        ));
        // No offer returned — every line is invalid → EmptyCart
        $this->offers->method('getByIds')->willReturn(new OfferCollection());
        $this->products->method('getByIds')->willReturn([]);

        $this->orders->expects($this->never())->method('insert');

        $this->expectException(EmptyCartException::class);
        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPhoneIsNormalisedBeforeStorage(): void
    {
        $this->cartToken->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);
        $this->cartItems->method('listByCart')->willReturn(new CartItemCollection(
            new CartItemDto(id: 99, cartId: 5, offerId: 88, quantity: 1),
        ));
        $this->offers->method('getByIds')->willReturn(new OfferCollection(
            new OfferDto(id: 88, productId: 17, price: 1000, area: 20, color: null),
        ));
        $this->products->method('getByIds')->willReturn([
            17 => new ProductDto(id: 17, name: 'X', code: 'x', type: ProductType::Wall, price: 1000, area: 20),
        ]);

        $this->orders->expects($this->once())
            ->method('insert')
            ->willReturnCallback(function ($orderDto) {
                // spaces, dashes, parens stripped — only digits + leading + remain
                $this->assertSame('+998901234567', $orderDto->customer->phone);
                return 1;
            });
        $this->orderItems->method('insert')->willReturn(1);

        $this->service->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 (90) 123-45-67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testPersistenceFailureRollsBackTheTransaction(): void
    {
        $this->cartToken->method('read')->willReturn('tok');
        $this->carts->method('findIdByToken')->willReturn(5);
        $this->cartItems->method('listByCart')->willReturn(new CartItemCollection(
            new CartItemDto(id: 99, cartId: 5, offerId: 88, quantity: 1),
        ));
        $this->offers->method('getByIds')->willReturn(new OfferCollection(
            new OfferDto(id: 88, productId: 17, price: 1000, area: 20, color: null),
        ));
        $this->products->method('getByIds')->willReturn([
            17 => new ProductDto(id: 17, name: 'X', code: 'x', type: ProductType::Wall, price: 1000, area: 20),
        ]);

        // Order header inserts fine, but order-items insert blows up — emulates
        // a DB constraint violation midway through persistence.
        $this->orders->method('insert')->willReturn(42);
        $this->orderItems->method('insert')->willThrowException(new \RuntimeException('boom'));

        // Wire a real-ish run() so we can verify rollback was called.
        $rolledBack = false;
        $this->tx = $this->createMock(TransactionServiceInterface::class);
        $this->tx->method('run')->willReturnCallback(function (callable $cb) use (&$rolledBack) {
            try {
                return $cb();
            } catch (\Throwable $e) {
                $rolledBack = true;
                throw $e;
            }
        });

        $service = new OrderService(
            $this->orders, $this->orderItems, $this->cartToken, $this->carts,
            $this->cartItems, $this->offers, $this->products, $this->language,
            $this->http, $this->tx,
        );

        try {
            $service->place(
                new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
                new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
                PaymentMethod::Card,
            );
            $this->fail('Expected RuntimeException to bubble up');
        } catch (\RuntimeException $e) {
            $this->assertSame('boom', $e->getMessage());
        }

        $this->assertTrue($rolledBack, 'rollback path must have been taken');
    }
}
