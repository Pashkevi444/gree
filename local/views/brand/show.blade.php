@extends('layouts.app')

@php use Gree\Helpers\Language; @endphp

@section('content')
    <main class="main">
      @if ($history)
        <section class="hero container">
          <h1 class="hero__title">{{ $history->name }}</h1>
          <div class="hero__description">
            {!! $history->text !!}
          </div>
        </section>
      @endif

      @if ($whyGree)
        <section class="gree container">
          <h2 class="gree__title">{{ $whyGree->name }}</h2>
          <p class="gree__description">{{ $whyGree->description }}</p>
          {{-- Кнопка «Узнать больше о Gree» отключена по запросу — пока ведёт в никуда.
               URL/текст остаются в iblock (whyGree->buttonUrl / buttonText) на случай возврата. --}}
          {{--
          @if ($whyGree->buttonUrl)
            <a class="gree__button" href="{{ $whyGree->buttonUrl }}">{{ $whyGree->buttonText }}</a>
          @endif
          --}}

          @if ($greeCards->count())
            <div class="gree-cards">
              @foreach ($greeCards as $card)
                <div class="gree-card">
                  <div class="gree-card__icon">
                    @include('partials.icon', ['code' => $card->iconCode])
                  </div>
                  <div class="gree-card__title">{{ $card->name }}</div>
                  <div class="gree-card__description">{{ $card->description }}</div>
                </div>
              @endforeach
            </div>
          @endif

          @if ($greeStats->count())
            <div class="gree-items gree-items-columns-4">
              @foreach ($greeStats as $stat)
                <div class="gree-item">
                  <div class="gree-item-title">
                    {{ $stat->numberPrefix }}<number-flow data-value="{{ $stat->numberValue }}"></number-flow>{{ $stat->numberSuffix }}
                  </div>
                  <div class="gree-item__description">{{ $stat->name }}</div>
                </div>
              @endforeach
            </div>
          @endif
        </section>
      @endif

      @if ($aboutCards->count())
        <section class="about container">
          <h2 class="about__title">{{ Language::t('brand.about.title') }}</h2>
          <div class="about-cards">
            @foreach ($aboutCards as $card)
              <div class="about-card">
                <div class="about-card__title">{{ $card->name }}</div>
                <div class="about-card__description">{!! $card->description !!}</div>
              </div>
            @endforeach
          </div>
        </section>
      @endif

      @if ($technologies->count())
        <section class="technologies container">
          <h2 class="technologies__title">{{ Language::t('home.tech.title') }}</h2>
          <p class="technologies__description">{{ Language::t('home.tech.description') }}</p>
          <div class="technologies-items technologies-items-columns-2">
            @foreach ($technologies as $tech)
              <div class="technologies-item">
                @if ($tech->image)
                  <img class="technologies-item__image" src="{{ $tech->image }}" alt="{{ $tech->name }}" />
                @endif
                <div class="technologies-item__title">{{ $tech->name }}</div>
                <div class="technologies-item__description">{!! $tech->description !!}</div>
              </div>
            @endforeach
          </div>
        </section>
      @endif
    </main>
@endsection
