@extends('layouts.app')

@php
    use Gree\Enum\Color;
    use Gree\Enum\ProductType;
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">

      @if ($product === null)
        <div class="wrapper container">
          <section class="catalog-section">
            <h1 class="catalog-section__title">{{ Language::t('product.not_found.title') }}</h1>
            <a class="catalog-section__button" href="{{ Route::to('catalog.index') }}">{{ Language::t('product.not_found.back') }}</a>
          </section>
        </div>
      @else
        <div class="wrapper container">
          {{-- Type switch — quick access to other catalog sections --}}
          <div class="catalog">
            <div class="catalog__title">{{ Language::t('product.types_heading') }}</div>
            <div class="catalog-items">
              @foreach (ProductType::cases() as $type)
                <a
                  class="catalog__item @if ($product->type === $type)catalog__item--active @endif"
                  href="{{ Route::to('catalog.section', ['section' => $type->slug()]) }}"
                >
                  @include('partials.product-type-icon', ['type' => $type])
                  {{ Language::t('product.types.' . $type->value) }}
                </a>
              @endforeach
            </div>
          </div>

          <div class="product-wrapper">
            <div class="product">
              <div class="product-left">
                @if ($product->sku !== '')
                  <div class="product__sku">{{ Language::t('product.article') }}: {{ $product->sku }}</div>
                @endif

                @php
                    // Initial slides — из галереи ПЕРВОГО ТП (выбран по умолчанию).
                    // У iblock products галереи нет сознательно (см. ProductDto-доку):
                    // фото живут на торговом предложении и должны меняться при
                    // выборе цвета/мощности. Fallback: одиночный PREVIEW_PICTURE.
                    $firstOffer = $product->offers->first() ?? null;
                    $slides = ($firstOffer?->gallery ?? [])
                        ?: ($product->image !== '' ? [$product->image] : []);
                @endphp
                @if ($slides)
                  <div class="product-carousel-main">
                    <div class="product-carousel product-carousel--main">
                      <div class="product-carousel-wrapper">
                        @foreach ($slides as $src)
                          <img class="product-carousel__slide" src="{{ $src }}" alt="{{ $product->name }}" />
                        @endforeach
                      </div>
                    </div>
                  </div>
                  <div class="product-carousel-thumbs">
                    <button class="product-carousel-thumbs__navigation-button product-carousel-thumbs__navigation-button--previous" type="button">
                      <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.5625 6.5625L0.5625 3.5625L3.5625 0.5625" stroke="currentColor" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </button>
                    <div class="product-carousel product-carousel--thumbs">
                      <div class="product-carousel-wrapper">
                        @foreach ($slides as $src)
                          <img class="product-carousel__slide" src="{{ $src }}" alt="{{ $product->name }}" />
                        @endforeach
                      </div>
                    </div>
                    <button class="product-carousel-thumbs__navigation-button product-carousel-thumbs__navigation-button--next" type="button">
                      <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.5625 6.5625L3.5625 3.5625L0.5625 0.5625" stroke="currentColor" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </button>
                  </div>
                  {{-- Bullet-пагинация Swiper на mobile.
                       product.js: pagination.el = '.product-carousel-mobile-pagination',
                       Swiper сам наполняет <span class="product-carousel-mobile-pagination__item">
                       по числу слайдов и подсвечивает активный --active-классом. --}}
                  <div class="product-carousel-mobile-pagination"></div>
                @endif
              </div>

              <form id="product-form" class="product-right" method="post" autocomplete="off">
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="offer_id" value="" data-offer-id />
                <h1 class="product__title">{{ $product->name }}</h1>

                @if ($product->colors)
                  <div class="product-colors">
                    <div class="product-colors__title">{{ Language::t('product.color') }}</div>
                    <div class="product-colors-items">
                      @foreach ($product->colors as $color)
                        <label class="product-colors-item" style="background-color: {{ $color->hex() }}">
                          <input
                            class="product-colors-item__control"
                            type="radio"
                            name="color"
                            value="{{ $color->value }}"
                            @if ($loop->first)checked @endif
                          />
                        </label>
                      @endforeach
                    </div>
                  </div>
                @endif

                @php $availableAreas = $product->offers?->uniqueAreas() ?? []; @endphp
                @if ($availableAreas)
                  <div class="product-area">
                    <div class="product-area__title">{{ Language::t('product.power_area') }}</div>
                    <div class="product-area-items">
                      @foreach ($availableAreas as $area)
                        <label class="product-area-item">
                          <input class="product-area-item__control" type="radio" name="area" value="{{ $area }}" @if ($loop->first)checked @endif />
                          <div class="product-area-item__text">{{ Language::t('product.area_unit', ['area' => $area]) }}</div>
                        </label>
                      @endforeach
                    </div>
                  </div>
                @endif

                <div class="product-price">
                  <div class="product-price__title">{{ Language::t('product.price') }}</div>
                  {{-- data-price-template — i18n-шаблон цены с плейсхолдером __PRICE__.
                       JS при смене offer делает: el.textContent = template.replace('__PRICE__', fmt(price)).
                       Работает и для RU («от 123 UZS»), и для UZ («123 UZS dan»). --}}
                  <div class="product-price__text" data-price-template="{{ Language::t('product.price_from', ['price' => '__PRICE__']) }}">{{ Language::t('product.price_from', ['price' => number_format($product->price, 0, '.', ' ')]) }}</div>
                </div>

                <div class="product-stock product-stock--in-stock" @unless ($product->inStock) style="display:none" @endunless>{{ Language::t('product.in_stock') }}</div>

                <div class="product-buttons">
                  <button class="product-buttons__item product-buttons__item--add-to-cart" type="submit" data-add-to-cart>
                    {{ Language::t('product.add_to_cart') }}
                  </button>
                  {{-- Counter заменяет кнопку «Купить» после добавления в корзину.
                       Инлайн style:display:none нужен потому что CSS .number-input
                       объявлен с display:flex и перекрывает атрибут hidden. JS
                       тоже управляет видимостью через style.display, не hidden. --}}
                  <div class="product__number-input number-input" data-cart-counter style="display:none">
                    <button class="number-input__button number-input__button--minus" type="button">
                      <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33398 8H12.6673" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                    <input class="number-input__control" type="number" name="amount" value="0" min="0" readonly />
                    <button class="number-input__button number-input__button--plus" type="button" data-counter-increase>
                      <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.33398 8H12.6673" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                  </div>
                  <button class="product-buttons__item product-buttons__item--help" type="button" data-popup="feedback">{{ Language::t('product.help') }}</button>
                </div>
              </form>
            </div>

            <div class="product-tabs">
              <div class="product-tabs-header">
                <button class="product-tabs-header__button product-tabs-header__button--active" type="button">{{ Language::t('product.tab.specs') }}</button>
                @if ($product->functions)
                  <button class="product-tabs-header__button" type="button">{{ Language::t('product.tab.functions') }}</button>
                @endif
                @if ($product->kitText !== '')
                  <button class="product-tabs-header__button" type="button">{{ Language::t('product.tab.kit') }}</button>
                @endif
                @if ($product->warrantyText !== '')
                  <button class="product-tabs-header__button" type="button">{{ Language::t('product.tab.warranty') }}</button>
                @endif
                @if ($product->installationText !== '')
                  <button class="product-tabs-header__button" type="button">{{ Language::t('product.tab.installation') }}</button>
                @endif
              </div>

              <div class="product-tabs-body">
                {{-- ── Specs tab ──────────────────────────────────────────── --}}
                <div class="product-tabs-item">
                  @if ($product->description !== '')
                    {!! $product->description !!}
                    <br /><br />
                  @endif

                  <table class="product-table">
                    <tbody>
                      @if ($product->model !== '')
                        <tr><td>{{ Language::t('spec.model') }}</td><td>{{ $product->model }}</td></tr>
                      @endif
                      <tr><td>{{ Language::t('spec.type') }}</td><td>{{ Language::t('product.type.' . $product->type->value) }}</td></tr>
                      @if ($product->coolingPower !== '')
                        <tr><td>{{ Language::t('spec.cooling_power') }}</td><td data-spec="cooling_power">{{ $product->coolingPower }}</td></tr>
                      @endif
                      @if ($product->heatingPower !== '')
                        <tr><td>{{ Language::t('spec.heating_power') }}</td><td data-spec="heating_power">{{ $product->heatingPower }}</td></tr>
                      @endif
                      @if ($product->area > 0)
                        <tr><td>{{ Language::t('spec.area') }}</td><td data-spec="area" data-spec-template="{{ Language::t('product.area_unit', ['area' => '__VAL__']) }}">{{ Language::t('product.area_unit', ['area' => $product->area]) }}</td></tr>
                      @endif
                      @if ($product->noise !== '')
                        <tr><td>{{ Language::t('spec.noise') }}</td><td data-spec="noise">{{ $product->noise }}</td></tr>
                      @endif
                      <tr><td>{{ Language::t('spec.inverter') }}</td><td>{{ Language::t($product->isInverter ? 'spec.inverter.yes' : 'spec.inverter.no') }}</td></tr>
                      @if ($product->energyClass !== '')
                        <tr><td>{{ Language::t('spec.energy_class') }}</td><td>{{ $product->energyClass }}</td></tr>
                      @endif
                      @if ($product->refrigerant !== '')
                        <tr><td>{{ Language::t('spec.refrigerant') }}</td><td>{{ $product->refrigerant }}</td></tr>
                      @endif
                      @if ($product->indoorDimensions !== '')
                        <tr><td>{{ Language::t('spec.indoor_dimensions') }}</td><td data-spec="indoor_dimensions">{{ $product->indoorDimensions }}</td></tr>
                      @endif
                      @if ($product->outdoorDimensions !== '')
                        <tr><td>{{ Language::t('spec.outdoor_dimensions') }}</td><td data-spec="outdoor_dimensions">{{ $product->outdoorDimensions }}</td></tr>
                      @endif
                      @if ($product->indoorWeight !== '')
                        <tr><td>{{ Language::t('spec.indoor_weight') }}</td><td data-spec="indoor_weight">{{ $product->indoorWeight }}</td></tr>
                      @endif
                      @if ($product->outdoorWeight !== '')
                        <tr><td>{{ Language::t('spec.outdoor_weight') }}</td><td data-spec="outdoor_weight">{{ $product->outdoorWeight }}</td></tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                {{-- ── Functions tab ───────────────────────────────────────── --}}
                @if ($product->functions)
                  <div class="product-tabs-item">
                    <div class="product-functions">
                      {{-- Первые 9 функций (3 ряда × 3 колонки grid-а) видимы,
                           остальные получают --hidden. Фронтовый product.js
                           показывает кнопку «Показать ещё» ТОЛЬКО если на
                           странице есть .product-functions-item--hidden, и
                           по клику тоглит их видимость. --}}
                      <div class="product-functions-items">
                        @foreach ($product->functions as $fn)
                          <div class="product-functions-item @if ($loop->index >= 9)product-functions-item--hidden @endif">
                            <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                              />
                            </svg>
                            {{ Language::t('function.' . $fn) }}
                          </div>
                        @endforeach
                      </div>
                      <button
                        class="product-functions__button"
                        type="button"
                        data-show-text="{{ Language::t('product.functions.show_more') }}"
                        data-hide-text="{{ Language::t('product.functions.hide') }}"
                      ></button>
                    </div>
                  </div>
                @endif

                {{-- ── Kit tab ─────────────────────────────────────────────── --}}
                @if ($product->kitText !== '')
                  <div class="product-tabs-item">
                    {!! $product->kitText !!}
                  </div>
                @endif

                {{-- ── Warranty tab ────────────────────────────────────────── --}}
                @if ($product->warrantyText !== '')
                  <div class="product-tabs-item">
                    {!! $product->warrantyText !!}
                  </div>
                @endif

                {{-- ── Installation tab ────────────────────────────────────── --}}
                @if ($product->installationText !== '')
                  <div class="product-tabs-item">
                    {!! $product->installationText !!}
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- ── Why Gree ───────────────────────────────────────────────────── --}}
        <section class="gree container">
          <h2 class="gree__title">{{ Language::t('home.gree.title') }}</h2>
          <p class="gree__description">{{ Language::t('home.gree.description') }}</p>
          <a class="gree__button" href="{{ Route::to('brand.show', ['code' => 'gree']) }}">{{ Language::t('home.gree.cta') }}</a>

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
          @endif
        </section>
      @endif
    </main>

    @include('partials.feedback-popup')

    @if ($product !== null && $product->offers !== null && $product->offers->count() > 0)
      @php
        $offersJson = array_map(fn($o) => $o->toArray(), $product->offers->toArray());
        $pageConfig = [
            'addUrl'          => Route::to('api.v1.cart.items.add'),
            'cartGetUrl'      => Route::to('api.v1.cart.get'),
            'itemUrlTemplate' => preg_replace('/0$/', '', Route::to('api.v1.cart.items.update', ['id' => 0])),
            'initialGallery'  => $slides ?? [],
        ];
      @endphp
      <script type="application/json" id="product-offers-json">@json($offersJson)</script>
      <script type="application/json" id="product-page-config">@json($pageConfig)</script>
      <script defer src="/local/templates/gree/assets/product-page.js"></script>
    @endif
@endsection
