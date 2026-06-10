@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
    /** @var \Gree\Collection\CartLineCollection $lines */

    // The PATCH/DELETE routes carry the line ID in the path. Build the URL once
    // with a placeholder so JS can swap it without parsing routing rules.
    $patchTpl = Route::to('api.v1.cart.items.update', ['id' => '__ID__']);

    // Префикс/постфикс цены — выдираем из i18n-шаблона product.price_from:
    //   RU: «от :price UZS»  → пре «от », пост «UZS»     (формантема впереди)
    //   UZ: «:price UZS dan» → пре «»,    пост «UZS dan» (формантема сзади)
    // Один источник для двух локалей, без отдельного ключа/миграции.
    $pricePartsRaw = explode('__PRICE__', Language::t('product.price_from', ['price' => '__PRICE__']), 2);
    $pricePrefix   = $pricePartsRaw[0] ?? '';
    $priceSuffix   = trim($pricePartsRaw[1] ?? 'UZS'); // без leading space — фронт сам добавит
@endphp

@section('content')
    {{-- Префикс цены — через CSS::before + attr(data-price-prefix) (правило в
         assets/custom.css). Перевод берётся из shared-ключа product.price_from
         (на UZ префикс пустой — «от» там не пишется, после числа идёт «dan»). --}}
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        <h1 class="title container">{{ Language::t('cart.title') }}</h1>

        @if ($lines->isEmpty())
            <section class="cart container">
                <div class="cart-empty">{{ Language::t('cart.empty') }}</div>
            </section>
        @else
            <section class="cart container">
                <div class="cart-items">
                    @foreach ($lines as $line)
                        <form class="cart-item"
                              data-cart-item="{{ $line->id }}"
                              data-price="{{ $line->unitPrice }}"
                              data-offer-id="{{ $line->offerId }}"
                              autocomplete="off">
                            @if ($line->image)
                                <img class="cart-item__image" src="{{ $line->image }}" alt="">
                            @endif
                            <div class="cart-item-body">
                                <a class="cart-item__title" href="{{ $line->productUrl }}">{{ $line->productName }}</a>
                                <div class="cart-item-configuration">
                                    @if ($line->color)
                                        <div class="cart-item-configuration-item">
                                            <div class="cart-item-configuration-item__title">{{ Language::t('cart.item.color') }}</div>
                                            <div class="cart-item-configuration-item__content">
                                                <div class="cart-item-color" style="background-color: {{ $line->color->hex() }}"></div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($line->area > 0)
                                        <div class="cart-item-configuration-item">
                                            <div class="cart-item-configuration-item__title">{{ Language::t('cart.item.area') }}</div>
                                            <div class="cart-item-configuration-item__content">
                                                <div class="cart-item-area">{{ Language::t('cart.item.area_unit', ['area' => $line->area]) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="cart-item-end">
                                {{-- ВНИМАНИЕ: data-postfix обязателен — фронтовый cart.js на load
                                     перерендерит textContent как `${fmt(value)} ${dataset.postfix}`.
                                     Префикс «от» добавляется CSS-правилом ::before (assets/custom.css),
                                     поэтому он переживает перерендер. --}}
                                <div class="cart-item__price" data-line-total data-postfix="{{ $priceSuffix }}" data-price-prefix="{{ $pricePrefix }}">
                                    {{ number_format($line->totalPrice, 0, '.', ' ') }} {{ $priceSuffix }}
                                </div>
                                <div class="cart-item-amount">
                                    <div class="cart-item-amount__title">{{ Language::t('cart.item.qty') }}</div>
                                    <div class="cart-item-amount__content">
                                        <div class="number-input">
                                            <button class="number-input__button number-input__button--minus" type="button" data-cart-step="-1">
                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.33398 8H12.6673" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                            <input class="number-input__control" type="number" name="amount" data-cart-qty value="{{ $line->quantity }}" min="0" readonly />
                                            <button class="number-input__button number-input__button--plus" type="button" data-cart-step="1">
                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M3.33398 8H12.6673" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>
                <div class="cart-sidebar">
                    <div class="cart-sidebar__title">{{ Language::t('cart.summary.title') }}</div>
                    <div class="cart-sidebar-items">
                        <div class="cart-sidebar__item">
                            <div data-cart-count
                                 data-count-template="{{ Language::t('cart.summary.items', ['count' => '__COUNT__']) }}">{{ Language::t('cart.summary.items', ['count' => $itemsCount]) }}</div>
                            <div class="cart-summary-price" data-cart-total data-postfix="{{ $priceSuffix }}" data-price-prefix="{{ $pricePrefix }}">{{ number_format($total, 0, '.', ' ') }} {{ $priceSuffix }}</div>
                        </div>
                        <div class="cart-sidebar__item">
                            <div>{{ Language::t('cart.summary.delivery') }}</div>
                            <div>{{ Language::t('cart.summary.delivery_free') }}</div>
                        </div>
                    </div>
                    <div class="cart-sidebar__divider"></div>
                    <div class="cart-sidebar-total">
                        <div class="cart-sidebar-total__title">{{ Language::t('cart.summary.total') }}</div>
                        <div class="cart-sidebar-total__price" data-cart-grand-total data-postfix="{{ $priceSuffix }}" data-price-prefix="{{ $pricePrefix }}">{{ number_format($total, 0, '.', ' ') }} {{ $priceSuffix }}</div>
                    </div>
                    <a class="cart-sidebar__button" href="{{ Route::to('order.checkout') }}">{{ Language::t('cart.checkout') }}</a>
                </div>
            </section>
        @endif
    </main>

    <script type="application/json" id="cart-page-config">@json(['patchTpl' => $patchTpl])</script>
    <script defer src="/local/templates/gree/assets/cart-page.js"></script>
@endsection
