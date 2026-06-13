@extends('layouts.app')

@php
    use Gree\Helpers\Language;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    <main class="main">

        {{-- ── Как с нами связаться ─────────────────────────────────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('contacts.section.channels.title') }}</h2>
                <div class="section-content contacts">
                    @foreach ($channels as $channel)
                        <div class="contacts-item">
                            <div class="contacts-item__icon">@include('contacts.partials.icon', ['code' => $channel->iconCode])</div>
                            <div class="contacts-item__title">{{ $channel->name }}</div>
                            <div class="contacts-item__description">{{ $channel->description }}</div>
                            @if ($channel->opensMap())
                                <button class="contacts-item__button" type="button"
                                        data-show-on-map="{{ $channel->latitude }},{{ $channel->longitude }}"
                                        data-show-on-map-label="{{ $channel->name }}">{{ $channel->buttonLabel }}</button>
                            @elseif ($channel->buttonUrl !== '')
                                <a class="contacts-item__button"
                                   href="{{ $channel->buttonUrl }}"
                                   @if (! str_starts_with($channel->buttonUrl, 'mailto:')) target="_blank" rel="noopener noreferrer" @endif>{{ $channel->buttonLabel }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Адреса ──────────────────────────────────────────────── --}}
        <section class="section container">
            <div class="section-wrapper">
                <h2 class="section__title">{{ Language::t('contacts.section.addresses.title') }}</h2>
                <div class="section-content locations">
                    @foreach ($addresses as $addr)
                        <div class="location-card">
                            @if ($addr->imageUrl !== '')
                                <img class="location-card__image" src="{{ $addr->imageUrl }}" alt="{{ $addr->name }}" />
                            @endif
                            <div class="location-card__title">{{ $addr->name }}</div>
                            <div class="location-card__text">{{ $addr->schedule }}</div>
                            @if (count($addr->phones) > 0)
                                <div class="location-card__text">
                                    @foreach ($addr->phones as $i => $phone)
                                        @if ($i > 0) • @endif
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                                    @endforeach
                                </div>
                            @endif
                            <button class="location-card__button" type="button"
                                    data-show-on-map="{{ $addr->latitude }},{{ $addr->longitude }}"
                                    data-show-on-map-label="{{ $addr->name }}">{{ Language::t('contacts.button.show_map') }}</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    </main>
@endsection
