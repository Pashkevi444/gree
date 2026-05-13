@extends('layouts.app')

@section('content')
    <main class="main">

      {{-- Слайдер --}}
      <section class="hero container">
        <div class="hero-wrapper">
          @foreach ($slider as $slide)
            <div
              class="hero-slide"
              style="--background-image: url('{{ $slide->backgroundImage }}')"
            >
              <h2 class="hero-slide__title">{{ $slide->name }}</h2>
              <p class="hero-slide__description">{!! $slide->subtitle !!}</p>
              <a class="hero-slide__button" href="{{ $slide->buttonUrl }}">{{ $slide->buttonText }}</a>
            </div>
          @endforeach
        </div>
        <div class="hero-footer">
          <div class="hero-pagination"></div>
          <div class="hero-navigation">
            <button class="hero-navigation__button hero-navigation__button--previous" type="button">
              <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.7002 10.5L0.900196 5.7L5.7002 0.900001" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <button class="hero-navigation__button hero-navigation__button--next" type="button">
              <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.899902 10.5L5.6999 5.7L0.899902 0.900001" stroke="#363636" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
        </div>
      </section>

      {{-- Каталог (статика — товары выводятся отдельно) --}}
      <section class="catalog container">
        <div class="catalog-header">
          <a class="catalog-header__button" href="#catalog-wall"> Настенные <span>до 80 м²</span> </a>
          <a class="catalog-header__button" href="#catalog-column"> Колонные <span>до 80 м²</span> </a>
          <a class="catalog-header__button" href="#catalog-industry"> Промышленные <span>от 100 м²</span> </a>
        </div>
        <div id="catalog-wall" class="catalog-section">
          <h2 class="catalog-section__title">Настенные кондиционеры</h2>
          <p class="catalog-section__description">Подходят для площадей до 80 м²</p>
          <div class="catalog-section__items">
            <div class="product-card">
              <div class="product-card__badge product-card__badge--bestseller">Хит продаж</div>
              <img class="product-card__image" src="/local/templates/gree/images/e39f753c587e47108643ec6d0d09750fb40b83fd.png" alt="" />
              <div class="product-card__name">Кондиционер Gree Pular GWH07AGA-K3NNA1B</div>
              <div class="product-card-meta">
                <div class="product-card-meta__text">Площадь — 30 м²</div>
              </div>
              <div class="product-card__price">от 250 000 000 000 UZS</div>
              <a class="product-card__button" href="/product.html">Подробнее</a>
            </div>
          </div>
          <a class="catalog-section__button" href="/catalog/">Посмотреть все модели</a>
        </div>
        <div id="catalog-column" class="catalog-section">
          <h2 class="catalog-section__title">Колонные кондиционеры</h2>
          <p class="catalog-section__description">Подходят для площадей до 200 м²</p>
          <div class="catalog-section__items">
            <div class="product-card">
              <img class="product-card__image" src="/local/templates/gree/images/e39f753c587e47108643ec6d0d09750fb40b83fd.png" alt="" />
              <div class="product-card__name">Кондиционер Gree Pular GWH07AGA-K3NNA1B</div>
              <div class="product-card-meta">
                <div class="product-card-meta__text">Площадь — 30 м²</div>
              </div>
              <div class="product-card__price">от 250 000 000 000 UZS</div>
              <a class="product-card__button" href="/product.html">Подробнее</a>
            </div>
          </div>
          <a class="catalog-section__button" href="/catalog/">Посмотреть все модели</a>
        </div>
        <div id="catalog-industry" class="catalog-section">
          <h2 class="catalog-section__title">Промышленные кондиционеры</h2>
          <p class="catalog-section__description">Климат-контроль помещений любых площадей и сложности по индивидуальному проекту</p>
          <div class="catalog-section__items">
            <div class="product-card">
              <img class="product-card__image" src="/local/templates/gree/images/e39f753c587e47108643ec6d0d09750fb40b83fd.png" alt="" />
              <div class="product-card__name">Кондиционер Gree Pular GWH07AGA-K3NNA1B</div>
              <div class="product-card-meta">
                <div class="product-card-meta__text">Площадь — 30 м²</div>
              </div>
              <div class="product-card__price">от 250 000 000 000 UZS</div>
              <a class="product-card__button" href="/product.html">Подробнее</a>
            </div>
          </div>
          <a class="catalog-section__button" href="/catalog/">Посмотреть все модели</a>
        </div>
      </section>

      {{-- Почему выбирают Gree: карточки + статистика --}}
      <section class="gree container">
        <h2 class="gree__title">Почему выбирают Gree</h2>
        <p class="gree__description">
          Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем качества и
          решениями для разных сценариев использования.
        </p>
        <a class="gree__button" href="/brand/gree/">Узнать больше о Gree</a>

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

        <div class="gree-items gree-items-columns-3">
          @foreach ($greeStats as $stat)
            <div class="gree-item">
              <div class="gree-item-title">
                {{ $stat->numberPrefix }}
                <number-flow data-value="{{ $stat->numberValue }}"></number-flow>
                {{ $stat->numberSuffix }}
              </div>
              <div class="gree-item__description">{{ $stat->description }}</div>
            </div>
          @endforeach
        </div>
      </section>

      {{-- Приложение + фичи --}}
      <section class="gree-app container">
        <h2 class="gree-app__title">Управляйте кондиционером со смартфона</h2>
        <p class="gree-app__description">
          Меняйте температуру, режим, таймер и скорость вентиляции из любой точки мира. Удобное управление и контроль
          энергопотребления в одном приложении.
        </p>
        <div
          class="gree-app-wrapper"
          style="--background-image: url('/images/e601a45048fdf16bef9fc2cb2f7e10115b005a31.png')"
        >
          @foreach ($appFeatures as $feature)
            <div class="gree-app-card">
              <div class="gree-app-card__icon">
                @include('partials.icon', ['code' => $feature->iconCode])
              </div>
              <div class="gree-app-card__title">{{ $feature->name }}</div>
              <div class="gree-app-card__description">{{ $feature->description }}</div>
            </div>
          @endforeach
        </div>
      </section>

      {{-- Технологии --}}
      <section class="technologies container">
        <h2 class="technologies__title">Технологии для вашего комфорта</h2>
        <p class="technologies__description">Ключевые преимущества кондиционеров Gree</p>
        <div class="technologies-items technologies-items-columns-3">
          @foreach ($technologies as $tech)
            <div class="technologies-item">
              @if ($tech->image)
                <img class="technologies-item__image" src="{{ $tech->image }}" alt="{{ $tech->name }}" />
              @endif
              <div class="technologies-item__title">{{ $tech->name }}</div>
              <div class="technologies-item__description">{{ $tech->description }}</div>
            </div>
          @endforeach
        </div>
      </section>

    </main>
@endsection
