<?php

declare(strict_types=1);

namespace Gree\Tests\Integration;

use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\OrderItemRepositoryInterface;
use Gree\Contract\Repository\OrderRepositoryInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\Contract\Service\OrderServiceInterface;
use Gree\Core\App;
use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\DTO\CityDto;
use Gree\Enum\OrderStatus;
use Gree\Enum\PaymentMethod;
use Gree\Service\Exception\CheckoutValidationException;
use Gree\Service\Exception\EmptyCartException;

/**
 * Сквозной тест чекаут-флоу против настоящей БД.
 *
 * Бьём по реальному `OrderService`, **не через HTTP** — поэтому транзакция в
 * `IntegrationTestCase::setUp/tearDown` действительно откатит всё созданное.
 * Никаких ручных DELETE, никакого mysqli, никаких висящих заказов после
 * прогона: rollback в том же коннекшене Bitrix.
 *
 * Что покрываем:
 *   - happy path: положили оффер в корзину → `place()` → есть Order +
 *     OrderItems со снапшотом цены, корзина опустошена;
 *   - повторный place на той же корзине → `EmptyCartException`;
 *   - валидация телефона → `CheckoutValidationException`;
 *   - пустая корзина → `EmptyCartException` без побочных эффектов.
 */
final class OrderCheckoutFlowTest extends IntegrationTestCase
{
    private OrderServiceInterface $orders;
    private OrderRepositoryInterface $orderRepo;
    private OrderItemRepositoryInterface $orderItemRepo;
    private CartRepositoryInterface $carts;
    private CartItemRepositoryInterface $cartItems;

    private int $cartId = 0;
    private int $offerId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orders        = App::get(OrderServiceInterface::class);
        $this->orderRepo     = App::get(OrderRepositoryInterface::class);
        $this->orderItemRepo = App::get(OrderItemRepositoryInterface::class);
        $this->carts         = App::get(CartRepositoryInterface::class);
        $this->cartItems     = App::get(CartItemRepositoryInterface::class);

        // CartTokenService::issue() выписывает UUID v4 и кладёт его в очередь
        // BitrixHttpContext (queued-кэш), которая видна сразу же при
        // последующем read() — Bitrix Request's cookie list immutable, в неё
        // влезть нельзя, поэтому через сервис, а не $_COOKIE. После rollback'а
        // транзакции строка в Carts пропадёт.
        $token = App::get(CartTokenServiceInterface::class)->issue();
        $this->cartId = $this->carts->createWithToken($token);
        $this->offerId = $this->pickAnyActiveOfferId();
    }

    public function testHappyPathPlacesOrderAndEmptiesCart(): void
    {
        $this->cartItems->insert($this->cartId, $this->offerId, 2);

        $order = $this->orders->place(
            new OrderCustomerDto(name: 'Иван Тестовый', phone: '+998 90 123 45 67', telegram: '@ivan'),
            new OrderDeliveryDto(
                city:   new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'),
                street: 'Amir Temur ave',
                house:  '1',
            ),
            PaymentMethod::Card,
        );

        $this->assertNotNull($order->id);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{12}$/', $order->publicId);
        $this->assertSame(OrderStatus::New, $order->status);
        $this->assertSame(PaymentMethod::Card, $order->payment);
        $this->assertGreaterThan(0, $order->total);
        $this->assertSame(2, $order->itemsCount);

        // Шапка реально лежит в БД
        $persisted = $this->orderRepo->findByPublicId($order->publicId);
        $this->assertNotNull($persisted);
        $this->assertSame(
            '+998901234567',
            $persisted->customer->phone,
            'phone должен нормализоваться без пробелов/скобок',
        );

        // Позиция — снапшот цены/количества
        $items = $this->orderItemRepo->listByOrder((int) $order->id);
        $this->assertSame(1, $items->count());
        $line = $items->first();
        $this->assertSame($this->offerId, $line->offerId);
        $this->assertSame(2, $line->quantity);
        $this->assertGreaterThan(0, $line->unitPrice);
        $this->assertSame($line->unitPrice * 2, $line->totalPrice);

        // Корзина опустошена
        $this->assertTrue(
            $this->cartItems->listByCart($this->cartId)->isEmpty(),
            'cart должен быть пуст после успешного заказа',
        );
    }

    public function testDuplicateSubmitFailsWithEmptyCart(): void
    {
        $this->cartItems->insert($this->cartId, $this->offerId, 1);

        $this->orders->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );

        $this->expectException(EmptyCartException::class);
        $this->orders->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testRejectsBadPhone(): void
    {
        $this->cartItems->insert($this->cartId, $this->offerId, 1);

        $this->expectException(CheckoutValidationException::class);
        $this->orders->place(
            new OrderCustomerDto(name: 'Иван', phone: 'qq'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    public function testEmptyCartFailsBeforePersist(): void
    {
        // Ничего в корзину не клали — сразу place
        $this->expectException(EmptyCartException::class);
        $this->orders->place(
            new OrderCustomerDto(name: 'Иван', phone: '+998 90 123 45 67'),
            new OrderDeliveryDto(city: new CityDto(id: 1, code: 'tashkent', nameRu: 'Ташкент', nameUz: 'Toshkent'), street: 'X', house: '1'),
            PaymentMethod::Card,
        );
    }

    // ────────────────────────────────────────────────────────────────────────

    /**
     * ID любого активного оффера. Если каталог не заполнен — скип
     * (миграции данных могут отсутствовать в окружении CI).
     */
    private function pickAnyActiveOfferId(): int
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $iblock = \Bitrix\Iblock\IblockTable::query()
            ->where('API_CODE', 'ProductsOffers')
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();
        if (!$iblock) {
            $this->markTestSkipped('ProductsOffers iblock not found — apply migrations first');
        }

        $entity = \Bitrix\Iblock\Iblock::wakeUp((int) $iblock['ID'])->getEntityDataClass();
        $row = $entity::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID'])
            ->setLimit(1)
            ->exec()
            ->fetch();
        if (!$row) {
            $this->markTestSkipped('No active offers in catalog — seed data first');
        }
        return (int) $row['ID'];
    }
}
