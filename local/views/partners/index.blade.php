@extends('layouts.app')

@php
    use Gree\Contract\Service\ContactsServiceInterface;
    use Gree\Core\App;
    use Gree\Helpers\Language;

    // Бенефиты «Почему легко продавать кондиционеры Gree?» — статика из
    // переводов (5 карточек, не меняется часто). [code, icon].
    $benefits = [
        ['code' => 'climate',  'icon' => 'warranty'],
        ['code' => 'service',  'icon' => 'parts'],
        ['code' => 'brand',    'icon' => 'brigades'],
        ['code' => 'range',    'icon' => 'support'],
        ['code' => 'warranty', 'icon' => 'warranty'],
    ];

    // CTA «Стать партнёром» → telegram-канал из contacts (orders-telegram).
    // Модалки пока нет, кнопка просто открывает диалог в Telegram.
    $partnerCtaUrl = App::get(ContactsServiceInterface::class)
        ->findChannelByCode('orders-telegram')?->buttonUrl;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    <main class="main">

        {{-- ── Hero ──────────────────────────────────────────────── --}}
        <section class="hero container">
            <div class="hero-wrapper" style="--background-image: url('/dist/images/6311bac5bcac86eed9564a7ad5501eda4bbfd3a0.png')">
                {{-- raw: переводы могут содержать <br> и др. inline-теги
                     (UF_VALUE_RU/UZ типа TEXT, см. Version20260608000002) --}}
                <h2 class="hero__title">{!! Language::t('partners.hero.title') !!}</h2>
                <p class="hero__description">{!! Language::t('partners.hero.description') !!}</p>
                @if ($partnerCtaUrl)
                    <a class="hero__button" href="{{ $partnerCtaUrl }}" target="_blank" rel="noopener noreferrer">{{ Language::t('partners.hero.cta') }}</a>
                @endif
            </div>
        </section>

        {{-- ── B2B-партнёрство ───────────────────────────────────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('partners.section.b2b.title') }}</h2>
                <div class="section__description">{{ Language::t('partners.section.b2b.description') }}</div>
                <div class="section-content b2b">
                    @foreach ($b2b as $card)
                        <div class="b2b-card">
                            <div class="b2b-card__icon">@include('help.partials.step', ['number' => $card->stepNumber])</div>
                            <div class="b2b-card__title">{{ $card->name }}</div>
                            <div class="b2b-card__description">{{ $card->description }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Как работает партнёрство ──────────────────────────── --}}
        <section class="how-it-works container">
            <div class="how-it-works-wrapper">
                <h2 class="how-it-works__title">{{ Language::t('partners.section.how_it_works.title') }}</h2>
                <div class="how-it-works-cards">
                    @foreach ($howItWorks as $card)
                        <div class="how-it-works-card">
                            <div class="how-it-works-card__title">{{ $card->name }}</div>
                            <div class="how-it-works-card__description">{{ $card->description }}</div>
                            @if ($card->hasLink())
                                <a class="how-it-works-card__link" href="{{ $card->linkUrl }}"
                                   @if (str_starts_with($card->linkUrl, 'http')) target="_blank" rel="noopener noreferrer" @endif
                                >{{ $card->linkLabel }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Почему легко продавать (статика из переводов) ────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('partners.section.benefits.title') }}</h2>
                <div class="section-content benefits">
                    @foreach ($benefits as $b)
                        <div class="benefits-card">
                            <div class="benefits-card__icon">@include('help.partials.icon', ['code' => $b['icon']])</div>
                            <div class="benefits-card__title">{{ Language::t('partners.benefits.' . $b['code'] . '.title') }}</div>
                            <div class="benefits-card__description">{{ Language::t('partners.benefits.' . $b['code'] . '.description') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Компании, которые нам доверяют (Swiper-карусель) ── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('partners.section.companies.title') }}</h2>
                <div class="section-content">
                    <div class="partners">
                        <button class="partners__navigation-button partners__navigation-button--previous" type="button">
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.69922 10.5L0.899219 5.7L5.69922 0.900001" stroke="#363636" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="partners-carousel">
                            <div class="partners-carousel-wrapper">
                                @foreach ($companies as $company)
                                    @if ($company->imageUrl !== '')
                                        <img class="partners-carousel__slide" src="{{ $company->imageUrl }}" alt="{{ $company->name }}" />
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

    </main>
@endsection
