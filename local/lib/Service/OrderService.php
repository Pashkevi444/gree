<?php

declare(strict_types=1);

namespace Gree\Service;

use Gree\Collection\OrderItemCollection;
use Gree\Contract\DB\TransactionServiceInterface;
use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Notification\OrderNotifierInterface;
use Gree\Contract\Repository\CartItemRepositoryInterface;
use Gree\Contract\Repository\CartRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\OrderItemRepositoryInterface;
use Gree\Contract\Repository\OrderRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\CartTokenServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\OrderServiceInterface;
use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\DTO\OrderDto;
use Gree\DTO\OrderItemDto;
use Gree\Enum\OrderStatus;
use Gree\Enum\PaymentMethod;
use Gree\Logging\FileLogger;
use Gree\Service\Exception\CheckoutValidationException;
use Gree\Service\Exception\EmptyCartException;

final class OrderService extends BaseService implements OrderServiceInterface
{
    /** Hard cap to dodge accidental DOS during public-id collision retries. */
    private const int PUBLIC_ID_MAX_TRIES = 5;

    /** Phone normalised to E.164-ish: only digits + leading +. Min 9 digits. */
    private const string PHONE_REGEX = '/^\+?[0-9\s\-()]{9,32}$/';

    /** Max length for free-form fields stored as `string`. UA gets the same cap. */
    private const int MAX_STRING = 500;
    private const int MAX_NAME = 100;
    private const int MAX_COMMENT = 1000;

    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly OrderItemRepositoryInterface $orderItems,
        private readonly CartTokenServiceInterface $cartToken,
        private readonly CartRepositoryInterface $carts,
        private readonly CartItemRepositoryInterface $cartItems,
        private readonly OfferRepositoryInterface $offers,
        private readonly ProductRepositoryInterface $products,
        private readonly LanguageServiceInterface $language,
        private readonly HttpContextInterface $http,
        private readonly TransactionServiceInterface $tx,
        private readonly OrderNotifierInterface $notifier,
    ) {}

    public function place(
        OrderCustomerDto $customer,
        OrderDeliveryDto $delivery,
        PaymentMethod $payment,
    ): OrderDto {
        try {
            $this->validate($customer, $delivery);

            $token = $this->cartToken->read();
            if ($token === null) {
                throw new EmptyCartException('no cart cookie');
            }
            $cartId = $this->carts->findIdByToken($token);
            if ($cartId === null) {
                throw new EmptyCartException('cart not found');
            }

            $cartLines = $this->cartItems->listByCart($cartId);
            if ($cartLines->isEmpty()) {
                throw new EmptyCartException('cart is empty');
            }

            // Снапшот по свежим данным; offer удалённый между add-to-cart и place — пропускаем; все пропущены → пустая корзина.
            $items = $this->snapshot($cartLines);
            if ($items->isEmpty()) {
                throw new EmptyCartException('all cart items invalid');
            }

            $publicId = $this->generatePublicId();

            $orderDto = new OrderDto(
                publicId:   $publicId,
                status:     OrderStatus::New,
                customer:   $this->normaliseCustomer($customer),
                delivery:   $this->normaliseDelivery($delivery),
                payment:    $payment,
                items:      $items,
                total:      $items->total(),
                itemsCount: $items->itemsCount(),
                locale:     $this->language->get(),
                cartToken:  $token,
                ip:         (string) ($this->http->getRemoteAddress() ?? ''),
                userAgent:  $this->truncate((string) ($this->http->getHeader('User-Agent') ?? ''), self::MAX_STRING),
            );

            // All-or-nothing: упадёт insert позиции / delete корзинной строки → шапка заказа не должна осиротеть.
            $orderId = $this->tx->run(function () use ($orderDto, $items, $cartLines) {
                $id = $this->orders->insert($orderDto);

                $itemIds = [];
                foreach ($items as $item) {
                    $itemIds[] = $this->orderItems->insert(new OrderItemDto(
                        offerId:     $item->offerId,
                        productName: $item->productName,
                        productCode: $item->productCode,
                        area:        $item->area,
                        color:       $item->color,
                        quantity:    $item->quantity,
                        unitPrice:   $item->unitPrice,
                        totalPrice:  $item->totalPrice,
                        orderId:     $id,
                    ));
                }
                // Связка Orders.UF_ITEM_IDS — менеджер видит в карточке заказа все позиции.
                $this->orders->setItemIds($id, $itemIds);

                // Чистим корзину — иначе double-submit / browser-back закажет то же самое.
                foreach ($cartLines as $row) {
                    $this->cartItems->delete($row->id);
                }

                return $id;
            });

            $placed = new OrderDto(
                publicId:   $publicId,
                status:     $orderDto->status,
                customer:   $orderDto->customer,
                delivery:   $orderDto->delivery,
                payment:    $orderDto->payment,
                items:      $items,
                total:      $orderDto->total,
                itemsCount: $orderDto->itemsCount,
                locale:     $orderDto->locale,
                id:         $orderId,
                cartToken:  $orderDto->cartToken,
                ip:         $orderDto->ip,
                userAgent:  $orderDto->userAgent,
            );

            // Best-effort уведомление менеджеру (e-mail). Notifier fail-soft —
            // почта недоступна, заказ всё равно оформлен.
            $this->notifier->notify($placed);

            return $placed;
        } catch (CheckoutValidationException | EmptyCartException $e) {
            throw $e;
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function findByPublicId(string $publicId): ?OrderDto
    {
        try {
            $header = $this->orders->findByPublicId($publicId);
            if ($header === null) {
                return null;
            }
            $items = $this->orderItems->listByOrder((int) $header->id);
            return new OrderDto(
                publicId:   $header->publicId,
                status:     $header->status,
                customer:   $header->customer,
                delivery:   $header->delivery,
                payment:    $header->payment,
                items:      $items,
                total:      $header->total,
                itemsCount: $header->itemsCount,
                locale:     $header->locale,
                id:         $header->id,
                cartToken:  $header->cartToken,
                ip:         $header->ip,
                userAgent:  $header->userAgent,
                createdAt:  $header->createdAt,
            );
        } catch (\Throwable $e) {
            FileLogger::getInstance()->critical(__METHOD__ . ' failed', ['exception' => $e]);
            return null;
        }
    }

    // ────────────────────────────────────────────────────────────────────────

    private function validate(OrderCustomerDto $customer, OrderDeliveryDto $delivery): void
    {
        $errors = [];
        if ($customer->name === '' || mb_strlen($customer->name) > self::MAX_NAME) {
            $errors['name'] = 'invalid';
        }
        if (!preg_match(self::PHONE_REGEX, $customer->phone)) {
            $errors['phone'] = 'invalid';
        }
        if ($customer->telegram !== '' && mb_strlen($customer->telegram) > self::MAX_STRING) {
            $errors['telegram'] = 'invalid';
        }
        // City: hlblock-привязка, ожидаем существующий ID записи Cities. Резолв
        // в CityDto делает контроллер (через CityRepository); сюда если ID=0
        // или nameRu/code пустые — значит ID невалидный.
        if ($delivery->city->id <= 0 || $delivery->city->code === '') {
            $errors['city'] = 'invalid';
        }
        if ($delivery->street === '' || mb_strlen($delivery->street) > self::MAX_STRING) {
            $errors['street'] = 'invalid';
        }
        if ($delivery->house === '' || mb_strlen($delivery->house) > self::MAX_STRING) {
            $errors['house'] = 'invalid';
        }
        if ($delivery->apartment !== '' && mb_strlen($delivery->apartment) > self::MAX_STRING) {
            $errors['apartment'] = 'invalid';
        }
        if (mb_strlen($delivery->comment) > self::MAX_COMMENT) {
            $errors['comment'] = 'invalid';
        }

        if ($errors) {
            throw new CheckoutValidationException($errors);
        }
    }

    /** Снапшот строк по свежим offer+product — цена и название «замораживаются» в момент заказа. */
    private function snapshot(\Gree\Collection\CartItemCollection $cartLines): OrderItemCollection
    {
        $offerIds = [];
        foreach ($cartLines as $line) {
            $offerIds[] = $line->offerId;
        }
        $offerCollection = $this->offers->getByIds($offerIds);

        $offersById = [];
        $productIds = [];
        foreach ($offerCollection as $offer) {
            $offersById[$offer->id] = $offer;
            $productIds[$offer->productId] = $offer->productId;
        }
        $productsById = $this->products->getByIds(array_values($productIds));

        $items = [];
        foreach ($cartLines as $line) {
            $offer = $offersById[$line->offerId] ?? null;
            if ($offer === null) {
                continue;
            }
            $product = $productsById[$offer->productId] ?? null;
            if ($product === null) {
                continue;
            }

            $items[] = new OrderItemDto(
                offerId:     $offer->id,
                productName: $product->name,
                productCode: $product->code,
                area:        $offer->area,
                color:       $offer->color,
                quantity:    $line->quantity,
                unitPrice:   $offer->price,
                totalPrice:  $offer->price * $line->quantity,
            );
        }

        return new OrderItemCollection(...$items);
    }

    private function normaliseCustomer(OrderCustomerDto $customer): OrderCustomerDto
    {
        return new OrderCustomerDto(
            name:     $this->truncate($customer->name, self::MAX_NAME),
            phone:    preg_replace('/[\s\-()]/', '', $customer->phone) ?? $customer->phone,
            telegram: $this->truncate($customer->telegram, self::MAX_STRING),
        );
    }

    private function normaliseDelivery(OrderDeliveryDto $delivery): OrderDeliveryDto
    {
        return new OrderDeliveryDto(
            city:      $delivery->city,
            street:    $this->truncate($delivery->street, self::MAX_STRING),
            house:     $this->truncate($delivery->house, self::MAX_STRING),
            apartment: $this->truncate($delivery->apartment, self::MAX_STRING),
            comment:   $this->truncate($delivery->comment, self::MAX_COMMENT),
        );
    }

    private function truncate(string $s, int $max): string
    {
        return mb_strlen($s) > $max ? mb_substr($s, 0, $max) : $s;
    }

    /** 12-char hex (48 бит). Несколько ретраев на редкую коллизию, дальше — кидаем. */
    private function generatePublicId(): string
    {
        for ($i = 0; $i < self::PUBLIC_ID_MAX_TRIES; $i++) {
            $id = bin2hex(random_bytes(6));
            if (!$this->orders->publicIdExists($id)) {
                return $id;
            }
        }
        throw new \RuntimeException('failed to generate unique public id after ' . self::PUBLIC_ID_MAX_TRIES . ' tries');
    }
}
