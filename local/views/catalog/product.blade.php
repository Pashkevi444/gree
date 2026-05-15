@extends('layouts.app')

@php
    use Gree\Enum\Color;
    use Gree\Enum\ProductType;
    use Gree\Helpers\Language;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">

      @if ($product === null)
        <div class="wrapper container">
          <section class="catalog-section">
            <h1 class="catalog-section__title">{{ Language::t('product.not_found.title') }}</h1>
            <a class="catalog-section__button" href="/catalog/">{{ Language::t('product.not_found.back') }}</a>
          </section>
        </div>
      @else
        <div class="wrapper container">
          {{-- Type switch — quick access to other catalog sections --}}
          <div class="catalog">
            <div class="catalog__title">{{ Language::t('product.types.' . $product->type->value) }}</div>
            <div class="catalog-items">
              @foreach (ProductType::cases() as $type)
                <a
                  class="catalog__item @if ($product->type === $type)catalog__item--active @endif"
                  href="/catalog/{{ $type->slug() }}/"
                >
                  <svg viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M0.727539 0.72728H3.63663L5.58572 10.4655C5.65223 10.8003 5.83438 11.1011 6.1003 11.3151C6.36622 11.5292 6.69896 11.6429 7.04027 11.6364H14.1094C14.4507 11.6429 14.7834 11.5292 15.0493 11.3151C15.3152 11.1011 15.4974 10.8003 15.5639 10.4655L16.7275 4.36364H4.3639M7.27299 15.2727C7.27299 15.6744 6.94738 16 6.54572 16C6.14406 16 5.81845 15.6744 5.81845 15.2727C5.81845 14.8711 6.14406 14.5455 6.54572 14.5455C6.94738 14.5455 7.27299 14.8711 7.27299 15.2727ZM15.273 15.2727C15.273 15.6744 14.9474 16 14.5457 16C14.1441 16 13.8184 15.6744 13.8184 15.2727C13.8184 14.8711 14.1441 14.5455 14.5457 14.5455C14.9474 14.5455 15.273 14.8711 15.273 15.2727Z"
                      stroke="currentColor"
                      stroke-width="1.45455"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
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
                    $slides = $product->gallery ?: ($product->image !== '' ? [$product->image] : []);
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
                @endif
              </div>

              <form id="product-form" class="product-right" method="post" autocomplete="off">
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
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
                  <div class="product-price__text" data-price-suffix=" UZS">{{ number_format($product->price, 0, '.', ' ') }} UZS</div>
                </div>

                <div class="product-stock product-stock--in-stock" @unless ($product->inStock) style="display:none" @endunless>{{ Language::t('product.in_stock') }}</div>

                <div class="product-buttons">
                  <button class="product-buttons__item product-buttons__item--add-to-cart" type="submit">
                    {{ Language::t('product.add_to_cart') }}
                  </button>
                  <a class="product-buttons__item product-buttons__item--help" href="">{{ Language::t('product.help') }}</a>
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
                      <div class="product-functions-items">
                        @foreach ($product->functions as $fn)
                          <div class="product-functions-item">
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
                      <button class="product-functions__button" type="button"></button>
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
          <a class="gree__button" href="/brand/gree/">{{ Language::t('home.gree.cta') }}</a>

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

    @if ($product !== null && $product->offers !== null && $product->offers->count() > 0)
      @php
        $offersJson = array_map(fn($o) => $o->toArray(), $product->offers->toArray());
      @endphp
      <script type="application/json" id="product-offers-json">@json($offersJson)</script>
      <script>
        (function () {
          const dataEl = document.getElementById('product-offers-json');
          if (!dataEl) return;
          let offers;
          try {
            offers = JSON.parse(dataEl.textContent || '[]');
          } catch (e) {
            return;
          }
          if (!Array.isArray(offers) || offers.length === 0) return;

          const form = document.getElementById('product-form');
          if (!form) return;

          const priceEl = form.querySelector('.product-price__text');
          const stockEl = form.querySelector('.product-stock');
          const submitBtn = form.querySelector('button[type="submit"]');
          const specCells = document.querySelectorAll('[data-spec]');
          const lang = document.documentElement.lang || 'ru';
          const fmt = new Intl.NumberFormat(lang.startsWith('en') ? 'en-US' : 'ru-RU');

          function findOffer(color, area) {
            // 1) exact match
            let m = offers.find(o => o.color === color && Number(o.area) === Number(area));
            if (m) return m;
            // 2) same color, any area
            m = offers.find(o => o.color === color);
            if (m) return m;
            // 3) same area, any color
            m = offers.find(o => Number(o.area) === Number(area));
            return m || offers[0];
          }

          function update() {
            const color = form.querySelector('input[name="color"]:checked')?.value;
            const area  = form.querySelector('input[name="area"]:checked')?.value;
            const offer = findOffer(color, area);
            if (!offer) return;

            if (priceEl) {
              const suffix = priceEl.dataset.priceSuffix || '';
              priceEl.textContent = fmt.format(offer.price) + suffix;
            }
            if (stockEl) {
              stockEl.style.display = offer.in_stock ? '' : 'none';
            }
            if (submitBtn) {
              submitBtn.disabled = !offer.in_stock;
            }
            specCells.forEach(td => {
              const key = td.dataset.spec;
              if (key in offer && offer[key] !== '' && offer[key] !== null) {
                const tpl = td.dataset.specTemplate;
                td.textContent = tpl ? tpl.replace('__VAL__', String(offer[key])) : String(offer[key]);
              }
            });
          }

          form.addEventListener('change', e => {
            if (e.target.name === 'color' || e.target.name === 'area') update();
          });
          update();
        })();
      </script>
    @endif
@endsection
