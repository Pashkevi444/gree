@extends('layouts.app')

@php
    use Gree\Helpers\Language;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    <main class="main">

        {{-- ── Способы оплаты ──────────────────────────────────────────── --}}
        <section class="section container" id="payment">
            <h2 class="section__title">{{ Language::t('help.section.payment.title') }}</h2>
            <p class="section__description">{{ Language::t('help.section.payment.description') }}</p>
            <div class="section-items payment-methods">
                @foreach ($paymentMethods as $method)
                    <div class="section-card payment-methods-item">
                        @if ($method->imageUrl !== '')
                            <img class="payment-methods-item__image" src="{{ $method->imageUrl }}" alt="{{ $method->name }}" />
                        @endif
                        <div class="payment-methods-item__name">{{ $method->name }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── Доставка ───────────────────────────────────────────────── --}}
        <section class="section container" id="delivery">
            <h2 class="section__title">{{ Language::t('help.section.delivery.title') }}</h2>
            <p class="section__description">{{ Language::t('help.section.delivery.description') }}</p>
            <div class="section-items delivery">
                @foreach ($delivery as $item)
                    <div class="section-card delivery-item">
                        <div class="delivery-item__icon">@include('help.partials.icon', ['code' => $item->iconCode])</div>
                        <div class="delivery-item__title">{{ $item->name }}</div>
                        <div class="delivery-item__description">{{ $item->description }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── Как обменять товар ─────────────────────────────────────── --}}
        <section class="section container" id="exchange">
            <h2 class="section__title">{{ Language::t('help.section.exchange.title') }}</h2>
            <p class="section__description">{{ Language::t('help.section.exchange.description') }}</p>
            <div class="section-items exchange">
                @foreach ($exchangeSteps as $step)
                    <div class="section-card exchange-item">
                        <div class="exchange-item__icon">@include('help.partials.step', ['number' => $step->stepNumber])</div>
                        <div class="exchange-item__title">{{ $step->name }}</div>
                        <div class="exchange-item__description">{{ $step->description }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── Как вернуть товар ──────────────────────────────────────── --}}
        <section class="section container" id="refund">
            <h2 class="section__title">{{ Language::t('help.section.refund.title') }}</h2>
            <p class="section__description">{{ Language::t('help.section.refund.description') }}</p>
            <div class="section-items refund">
                @foreach ($refundSteps as $step)
                    <div class="section-card refund-item">
                        <div class="refund-item__icon">@include('help.partials.step', ['number' => $step->stepNumber])</div>
                        <div class="refund-item__title">{{ $step->name }}</div>
                        <div class="refund-item__description">
                            {{ $step->description }}
                            @if ($step->tooltip !== '')
                                <span class="tooltip" data-text="{{ $step->tooltip }}">
                                    <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.9974 12.8334C10.2191 12.8334 12.8307 10.2217 12.8307 7.00008C12.8307 3.77842 10.2191 1.16675 6.9974 1.16675C3.77573 1.16675 1.16406 3.77842 1.16406 7.00008C1.16406 10.2217 3.77573 12.8334 6.9974 12.8334Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 9.33333V7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 4.66675H7.00667" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="section__footer-text">
                {{ Language::t('help.refund.footer.prefix') }}
                <a href="tel:{{ Language::t('help.refund.footer.phone') }}"><b>{{ Language::t('help.refund.footer.phone') }}.</b></a>
                <br />
                {{ Language::t('help.refund.footer.suffix') }}
            </div>
        </section>

        {{-- ── Единый сервисный центр ─────────────────────────────────── --}}
        <section class="section container" id="service">
            <h2 class="section__title">{{ Language::t('help.section.service.title') }}</h2>
            <p class="section__description">{{ Language::t('help.section.service.description') }}</p>
            <div class="section-items service">
                @foreach ($serviceFeatures as $feature)
                    <div class="section-card service-item">
                        <div class="service-item__icon">@include('help.partials.icon', ['code' => $feature->iconCode])</div>
                        <div class="service-item__title">{{ $feature->name }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── service-2 (hero + cards) ───────────────────────────────── --}}
        @if ($serviceHero !== null)
            <section class="service-2 container">
                <div class="service-2-wrapper" style="--background-image: url('{{ $serviceHero->backgroundUrl ?: '/dist/images/img.png' }}')">
                    <div class="service-2-hero">
                        <h2 class="service-2-hero__title">{{ $serviceHero->name }}</h2>
                        <p class="service-2-hero__description">{{ $serviceHero->description }}</p>
                    </div>
                    @foreach ($serviceCards as $card)
                        <div class="service-2-card">
                            <div class="service-2-card__icon">@include('help.partials.icon', ['code' => $card->iconCode])</div>
                            <div class="service-2-card__title">{{ $card->name }}</div>
                            <div class="service-2-card__description">{{ $card->description }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>
@endsection
