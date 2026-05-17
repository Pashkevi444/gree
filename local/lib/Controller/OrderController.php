<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CartServiceInterface;
use Gree\Contract\Service\OrderServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\DTO\OrderCustomerDto;
use Gree\DTO\OrderDeliveryDto;
use Gree\Enum\DeliveryCity;
use Gree\Enum\PaymentMethod;
use Gree\Helpers\Route;
use Gree\Security\AccessDeniedException;
use Gree\Service\Exception\CheckoutValidationException;
use Gree\Service\Exception\EmptyCartException;
use Gree\View\OrderCheckoutViewData;
use Gree\View\OrderSuccessViewData;

final class OrderController extends BaseController
{
    public function __construct(
        private readonly OrderServiceInterface $order,
        private readonly CartServiceInterface $cart,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly SeoServiceInterface $seo,
        private readonly ApiGuardInterface $guard,
    ) {}

    /**
     * GET /order/ — checkout form. If the cart is empty, bounce back to /cart/.
     */
    public function checkout(): HttpResponse
    {
        $lines = $this->cart->view();
        if ($lines->isEmpty()) {
            $response = new HttpResponse();
            $response->addHeader('Location', Route::to('cart.index'));
            $response->setStatus('302 Found');
            return $response;
        }

        $this->applySeo($this->seo->forPage('order'));
        $this->addPageAssets('order');

        return $this->view('order/checkout', new OrderCheckoutViewData(
            breadcrumbs:    $this->breadcrumbs->checkout(),
            lines:          $lines,
            total:          $lines->total(),
            itemsCount:     $lines->itemsCount(),
            cities:         DeliveryCity::cases(),
            paymentMethods: PaymentMethod::cases(),
        ));
    }

    /**
     * GET /order/success/{publicId}/ — thank-you screen. 404 on unknown publicId.
     */
    public function success(string $publicId): HttpResponse
    {
        $order = $this->order->findByPublicId($publicId);
        if ($order === null) {
            return $this->view('errors/404')->setStatus('404 Not Found');
        }

        $this->applySeo($this->seo->forPage('order_success'));
        $this->addPageAssets('order-complete');

        return $this->view('order/success', new OrderSuccessViewData(order: $order));
    }

    /**
     * POST /api/v1/order — place order from the visitor's cart + form body.
     */
    public function place(): HttpResponse
    {
        try {
            $this->guard->guardStateChanging($this->getRequest());
        } catch (AccessDeniedException $e) {
            return $this->json(['error' => 'forbidden', 'reason' => $e->getMessage()], 403);
        }

        $payload = $this->payload();

        $customer = OrderCustomerDto::fromArray($payload['customer'] ?? [
            'name'     => $payload['name']     ?? '',
            'phone'    => $payload['phone']    ?? '',
            'telegram' => $payload['telegram'] ?? '',
        ]);
        $delivery = OrderDeliveryDto::fromArray($payload['delivery'] ?? [
            'city'      => $payload['city']      ?? 'tashkent',
            'street'    => $payload['street']    ?? '',
            'house'     => $payload['house']     ?? '',
            'apartment' => $payload['apartment'] ?? '',
            'comment'   => $payload['comment']   ?? '',
        ]);

        $paymentRaw = (string) ($payload['payment'] ?? $payload['payment_method'] ?? '');
        $payment = PaymentMethod::tryFromOrNull($paymentRaw);
        if ($payment === null) {
            return $this->json(['error' => 'validation', 'fields' => ['payment' => 'invalid']], 422);
        }

        try {
            $order = $this->order->place($customer, $delivery, $payment);
        } catch (CheckoutValidationException $e) {
            return $this->json(['error' => 'validation', 'fields' => $e->errors], 422);
        } catch (EmptyCartException) {
            return $this->json(['error' => 'empty_cart'], 422);
        }

        return $this->json([
            'public_id'    => $order->publicId,
            'redirect_url' => Route::to('order.success', ['publicId' => $order->publicId]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        $request = $this->getRequest();
        if ($request->isJson()) {
            $request->decodeJson();
            return $request->getJsonList()->toArray();
        }
        return $request->getPostList()->toArray();
    }
}
