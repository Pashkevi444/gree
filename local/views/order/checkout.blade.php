@extends('layouts.app')

@php
    use Gree\Core\App;
    use Gree\Contract\Service\LanguageServiceInterface;
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
    /** @var \Gree\Collection\CartLineCollection $lines */
    /** @var \Gree\Collection\CityCollection $cities */
    /** @var array<int, \Gree\Enum\PaymentMethod> $paymentMethods */

    $apiUrl = Route::to('api.v1.order.place');
    $cartUrl = Route::to('cart.index');
    $currentLocale = App::get(LanguageServiceInterface::class)->get();
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        <h1 class="title container">{{ Language::t('order.title') }}</h1>
        <section class="order container">
            <form id="order-form" class="order-wrapper" method="post" autocomplete="off">

                {{-- ── Контакты ────────────────────────────────────────────── --}}
                <div class="order-card">
                    <div class="order-card__title">{{ Language::t('order.contacts.title') }}</div>
                    <div class="order-card-content">
                        <div class="form-layout form-layout--contacts">
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.contacts.name') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="text" name="name" required maxlength="100"
                                           placeholder="{{ Language::t('order.contacts.name_placeholder') }}" />
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.contacts.phone') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="tel" name="phone" required maxlength="32"
                                           placeholder="{{ Language::t('order.contacts.phone_placeholder') }}" />
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.contacts.telegram') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="text" name="telegram" maxlength="500"
                                           placeholder="{{ Language::t('order.contacts.telegram_placeholder') }}" />
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ── Доставка ─────────────────────────────────────────────── --}}
                <div class="order-card">
                    <div class="order-card__title">{{ Language::t('order.delivery.title') }}</div>
                    <div class="order-card__description">{{ Language::t('order.delivery.description') }}</div>
                    <div class="order-card-content">
                        <div class="form-layout form-layout--delivery">
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.delivery.city') }}</div>
                                <div class="label-content">
                                    <select class="form-control" name="city" required>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->nameFor($currentLocale) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.delivery.street') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="text" name="street" required maxlength="500"
                                           placeholder="{{ Language::t('order.delivery.street_placeholder') }}" />
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.delivery.house') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="text" name="house" required maxlength="500"
                                           placeholder="{{ Language::t('order.delivery.house_placeholder') }}" />
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.delivery.apartment') }}</div>
                                <div class="label-content">
                                    <input class="form-control" type="text" name="apartment" maxlength="500"
                                           placeholder="{{ Language::t('order.delivery.apartment_placeholder') }}" />
                                </div>
                            </label>
                            <label class="label">
                                <div class="label__title">{{ Language::t('order.delivery.comment') }}</div>
                                <div class="label-content">
                                    <textarea class="form-control" rows="3" name="comment" maxlength="1000"
                                              placeholder="{{ Language::t('order.delivery.comment_placeholder') }}"></textarea>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ── Оплата ──────────────────────────────────────────────── --}}
                <div class="order-card">
                    <div class="order-card__title">{{ Language::t('order.payment.title') }}</div>
                    <div class="order-card-content">
                        <div class="form-layout form-layout--payment">
                            @foreach ($paymentMethods as $i => $method)
                                <label class="payment">
                                    <input type="radio" name="payment" value="{{ $method->value }}" @if ($i === 0) checked @endif />
                                    @include('partials.payment-icon', ['method' => $method])
                                    {{ Language::t($method->translationKey()) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>

            {{-- ── Sidebar (заказ) ─────────────────────────────────────────── --}}
            <div class="order-sidebar">
                <div class="order-sidebar__title">{{ Language::t('order.sidebar.title') }}</div>
                <div class="order-sidebar-items">
                    @foreach ($lines as $line)
                        <div class="order-sidebar-item">
                            @if ($line->image)
                                <img class="order-sidebar-item__image" src="{{ $line->image }}" alt="">
                            @endif
                            <div class="order-sidebar-item-wrapper">
                                <div class="order-sidebar-item__title">{{ $line->productName }}</div>
                                <div class="order-sidebar-item__meta">
                                    @if ($line->color){{ Language::t('color.' . $line->color->value) }}@endif
                                    @if ($line->area > 0), {{ Language::t('cart.item.area_unit', ['area' => $line->area]) }}@endif
                                </div>
                                <div class="order-sidebar-item-footer">
                                    <div class="order-sidebar-item__price">
                                        {{ number_format($line->totalPrice, 0, '.', ' ') }} UZS
                                    </div>
                                    <div class="order-sidebar-item__amount">×{{ $line->quantity }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="order-sidebar__divider"></div>
                <div class="order-sidebar-total">
                    <div class="order-sidebar-total__title">{{ Language::t('order.sidebar.total') }}</div>
                    <div class="order-sidebar-total__price">{{ number_format($total, 0, '.', ' ') }} UZS</div>
                </div>
                <button id="order-submit" class="order-sidebar__button" type="submit" form="order-form">
                    {{ Language::t('order.sidebar.submit') }}
                </button>
                <div id="order-error" class="order-sidebar__error" style="display:none"></div>
            </div>
        </section>
    </main>

    @php
        // Конфиг отдельной переменной: Blade-парсер @json([...]) на многострочном
        // литерале пишет невалидный PHP (ParseError «Unclosed [»). @json($var) ок.
        $checkoutConfig = [
            'apiUrl'  => $apiUrl,
            'cartUrl' => $cartUrl,
            'text'    => [
                'invalid' => Language::t('order.error.invalid'),
                'network' => Language::t('order.error.network'),
                'empty'   => Language::t('order.error.empty_cart'),
            ],
        ];
    @endphp
    <script type="application/json" id="checkout-page-config">@json($checkoutConfig)</script>
    <script defer src="/local/templates/gree/assets/checkout-page.js"></script>
@endsection
