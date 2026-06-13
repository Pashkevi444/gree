@extends('layouts.app')

@php
    use Gree\Helpers\Language;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    <main class="main">

        {{-- ── Где купить (карточки точек продаж с координатами) ─────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('where.section.locations.title') }}</h2>
                <p class="section__description">{{ Language::t('where.section.locations.description') }}</p>
                <div class="section-content locations">
                    @foreach ($locations as $loc)
                        <div class="location-card">
                            @if ($loc->imageUrl !== '')
                                <img class="location-card__image" src="{{ $loc->imageUrl }}" alt="{{ $loc->name }}" />
                            @endif
                            <div class="location-card__title">{{ $loc->name }}</div>
                            <div class="location-card__text">{{ $loc->schedule }}</div>
                            @if (count($loc->phones) > 0)
                                <div class="location-card__text">
                                    @foreach ($loc->phones as $i => $phone)
                                        @if ($i > 0) • @endif
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                                    @endforeach
                                </div>
                            @endif
                            <button class="location-card__button" type="button"
                                    data-show-on-map="{{ $loc->latitude }},{{ $loc->longitude }}"
                                    data-show-on-map-label="{{ $loc->name }}">{{ Language::t('contacts.button.show_map') }}</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Наши партнёры (карусель, Swiper из main.js) ──────────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('where.section.partners.title') }}</h2>
                <div class="section-content">
                    <div class="partners">
                        <button class="partners__navigation-button partners__navigation-button--previous" type="button">
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.69922 10.5L0.899219 5.7L5.69922 0.900001" stroke="#363636" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="partners-carousel">
                            <div class="partners-carousel-wrapper">
                                @foreach ($partners as $partner)
                                    @if ($partner->imageUrl !== '')
                                        <img class="partners-carousel__slide" src="{{ $partner->imageUrl }}" alt="{{ $partner->name }}" />
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <button class="partners__navigation-button partners__navigation-button--next" type="button">
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.898438 10.5L5.69844 5.7L0.898437 0.900001" stroke="#363636" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Сети магазинов-партнёров (статичная сетка) ───────────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('where.section.chains.title') }}</h2>
                <div class="section-content partner-stores">
                    @foreach ($chains as $chain)
                        @if ($chain->imageUrl !== '')
                            <img class="partner-stores__item" src="{{ $chain->imageUrl }}" alt="{{ $chain->name }}" />
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

    </main>
@endsection
