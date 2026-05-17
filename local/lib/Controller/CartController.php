<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Security\ApiGuardInterface;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CartServiceInterface;
use Gree\Contract\Service\SeoServiceInterface;
use Gree\Security\AccessDeniedException;
use Gree\Service\Exception\OfferNotFoundException;
use Gree\View\CartViewData;

final class CartController extends BaseController
{
    public function __construct(
        private readonly CartServiceInterface $cart,
        private readonly BreadcrumbsServiceInterface $breadcrumbs,
        private readonly ApiGuardInterface $guard,
        private readonly SeoServiceInterface $seo,
    ) {}

    public function index(): HttpResponse
    {
        $this->applySeo($this->seo->forPage('cart'));
        $this->addPageAssets('cart');

        $lines = $this->cart->view();

        return $this->view('cart/index', new CartViewData(
            breadcrumbs: $this->breadcrumbs->cart(),
            lines:       $lines,
            total:       $lines->total(),
            itemsCount:  $lines->itemsCount(),
        ));
    }

    // ── API ──────────────────────────────────────────────────────────────────

    public function get(): HttpResponse
    {
        $lines = $this->cart->view();
        return $this->json($this->serialise($lines));
    }

    public function add(): HttpResponse
    {
        if (($denial = $this->ensureSafe()) !== null) {
            return $denial;
        }

        $body = $this->payload();
        $offerId = (int) ($body['offer_id'] ?? 0);
        $quantity = (int) ($body['quantity'] ?? 1);

        if ($offerId <= 0) {
            return $this->json(['error' => 'offer_id is required'], 400);
        }
        // Hard cap so a hostile client can't fill the table with a single call.
        if ($quantity < 1 || $quantity > 999) {
            return $this->json(['error' => 'quantity out of range'], 400);
        }

        try {
            $itemId = $this->cart->add($offerId, $quantity);
        } catch (OfferNotFoundException $e) {
            return $this->json(['error' => 'offer_not_found'], 422);
        }
        $lines = $this->cart->view();

        return $this->json(['item_id' => $itemId] + $this->serialise($lines));
    }

    public function update(int $id): HttpResponse
    {
        if (($denial = $this->ensureSafe()) !== null) {
            return $denial;
        }

        $body = $this->payload();
        if (!array_key_exists('quantity', $body)) {
            return $this->json(['error' => 'quantity is required'], 400);
        }
        $quantity = (int) $body['quantity'];
        if ($quantity > 999) {
            return $this->json(['error' => 'quantity out of range'], 400);
        }

        try {
            $this->cart->update($id, $quantity);
        } catch (AccessDeniedException) {
            return $this->json(['error' => 'forbidden'], 403);
        }
        $lines = $this->cart->view();
        return $this->json($this->serialise($lines));
    }

    public function remove(int $id): HttpResponse
    {
        if (($denial = $this->ensureSafe()) !== null) {
            return $denial;
        }
        try {
            $this->cart->remove($id);
        } catch (AccessDeniedException) {
            return $this->json(['error' => 'forbidden'], 403);
        }
        $lines = $this->cart->view();
        return $this->json($this->serialise($lines));
    }

    /**
     * Runs the API guard. Returns a 403 JSON response if the request fails,
     * or null when the request is safe to proceed.
     */
    private function ensureSafe(): ?HttpResponse
    {
        try {
            $this->guard->guardStateChanging($this->getRequest());
            return null;
        } catch (AccessDeniedException $e) {
            return $this->json(['error' => 'forbidden', 'reason' => $e->getMessage()], 403);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serialise(\Gree\Collection\CartLineCollection $lines): array
    {
        $items = [];
        foreach ($lines as $line) {
            $items[] = $line->toJson();
        }
        return [
            'lines' => $items,
            'total' => $lines->total(),
            'count' => $lines->itemsCount(),
        ];
    }

    /**
     * Reads request body. Bitrix exposes parsed JSON bodies via getJsonList();
     * x-www-form-urlencoded falls through to getPostList(). No php://input or
     * superglobals here — both routes go through HttpRequest.
     *
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
