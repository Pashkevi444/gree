@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    $selectedTypes = array_map(fn($t) => $t->value, $filter->types);
    $selectedAreas = $filter->areas;
    $selectedColors = array_map(fn($c) => $c->value, $filter->colors);
    $bestsellerValue = $filter->bestseller === null ? null : ($filter->bestseller ? 'yes' : 'no');
    $inverterValue = $filter->inverterMotor === null ? null : ($filter->inverterMotor ? 'yes' : 'no');
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
      @if ($lockedType !== null)
        <h1 class="main__title container">{{ Language::t('catalog.section.' . $lockedType->value . '.title') }}</h1>
        <p class="main__description container">{{ Language::t('catalog.section.' . $lockedType->value . '.description') }}</p>
      @else
        <h1 class="main__title container">{{ Language::t('catalog.title') }}</h1>
        <p class="main__description container">{{ Language::t('catalog.description') }}</p>
      @endif
      <section class="catalog container">
        <form id="filters-form" class="catalog-sidebar" action="/api/catalog" autocomplete="off">
          <div class="catalog-sidebar__title">{{ Language::t('catalog.filters') }}</div>
          @if ($lockedType === null)
          {{-- Type group hidden on section pages — URL already pins the type --}}
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.type') }}</div>
            <div class="catalog-sidebar-filters__items">
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="type[]" value="wall" @checked(in_array('wall', $selectedTypes, true)) />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('product.type.wall') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="type[]" value="column" @checked(in_array('column', $selectedTypes, true)) />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('product.type.column') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="type[]" value="industrial" @checked(in_array('industrial', $selectedTypes, true)) />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('product.type.industrial') }}</div>
              </label>
            </div>
          </div>
          @else
            {{-- The locked type is kept in a hidden field so the filter API
                 receives it even though there is no visible checkbox. --}}
            <input type="hidden" name="type[]" value="{{ $lockedType->value }}" />
          @endif
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.price') }}</div>
            <div class="catalog-sidebar-filters__items">
              <div class="range-input">
                <input class="range-input__control form-control" type="text" placeholder="UZS" readonly />
                <input
                  class="range-input__control range-input__control--lower"
                  type="number"
                  min="0"
                  max="123000000"
                  value="{{ $filter->priceMin }}"
                  name="price[]"
                />
                <input
                  class="range-input__control range-input__control--upper"
                  type="number"
                  min="0"
                  max="123000000"
                  value="{{ $filter->priceMax === PHP_INT_MAX ? 123000000 : $filter->priceMax }}"
                  name="price[]"
                />
                <div class="range-input-noUi"></div>
              </div>
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.area') }}</div>
            <div class="catalog-sidebar-filters__items">
              @foreach ([20, 30, 50, 100] as $value)
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="area[]" value="{{ $value }}" @checked(in_array($value, $selectedAreas, true)) />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('catalog.area.up_to', ['area' => $value]) }}</div>
              </label>
              @endforeach
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.inverter') }}</div>
            <div class="catalog-sidebar-filters__items">
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="inverter_motor[]" value="yes" @checked($inverterValue === 'yes') />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('catalog.filter.yes') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="inverter_motor[]" value="no" @checked($inverterValue === 'no') />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('catalog.filter.no') }}</div>
              </label>
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.bestseller') }}</div>
            <div class="catalog-sidebar-filters__items">
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="bestseller[]" value="yes" @checked($bestsellerValue === 'yes') />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('catalog.filter.yes') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="bestseller[]" value="no" @checked($bestsellerValue === 'no') />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('catalog.filter.no') }}</div>
              </label>
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.color') }}</div>
            <div class="catalog-sidebar-filters__items">
              @foreach (\Gree\Enum\Color::cases() as $color)
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="color[]" value="{{ $color->value }}" @checked(in_array($color->value, $selectedColors, true)) />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('color.' . $color->value) }}</div>
              </label>
              @endforeach
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.refrigerant') }}</div>
            <div class="catalog-sidebar-filters__items">
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="refrigerant_type[]" value="r32" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">r32</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="refrigerant_type[]" value="r410a" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">r410a</div>
              </label>
            </div>
          </div>
          <div class="catalog-sidebar-filters">
            <div class="catalog-sidebar-filters__title">{{ Language::t('catalog.filter.functions') }}</div>
            <div class="catalog-sidebar-filters__items">
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="wifi" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.wifi') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="130v" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.130v') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="energy-saving" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.energy_saving') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="turbo-mode" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.turbo') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="silent-mode" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.silent') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="eco-mode" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.eco') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="smart-home" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.smart_home') }}</div>
              </label>
              <label class="checkbox">
                <input class="checkbox__control" type="checkbox" name="functions[]" value="ai" />
                <div class="checkbox__icon">
                  <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.5 0.5L3 6L0.5 3.5"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <div class="checkbox__text">{{ Language::t('function.ai') }}</div>
              </label>
            </div>
          </div>
          <div class="catalog-sidebar-buttons">
            <button class="catalog-sidebar__submit-button" type="submit"></button>
            <button class="catalog-sidebar__reset-button" type="reset" disabled>{{ Language::t('catalog.reset') }}</button>
          </div>
        </form>
        <div class="catalog-wrapper">
          <div class="catalog-header">
            <div class="catalog-header__title">{{ Language::t('catalog.found', ['count' => $total]) }}</div>
            <div class="catalog-sort-button">
              <div class="catalog-sort-button__text"></div>
              <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0.75 0.75L4.75 4.75L8.75 0.75"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <select class="catalog-sort-button__control" name="sort" form="filters-form">
                <option value="popular" @selected($filter->sortField === \Gree\Enum\SortField::Popular)>{{ Language::t('catalog.sort.popular') }}</option>
                <option value="price_asc" @selected($filter->sortField === \Gree\Enum\SortField::PriceAsc)>{{ Language::t('catalog.sort.price_asc') }}</option>
                <option value="price_desc" @selected($filter->sortField === \Gree\Enum\SortField::PriceDesc)>{{ Language::t('catalog.sort.price_desc') }}</option>
              </select>
            </div>
          </div>
          <div class="catalog-items">
            @foreach ($products as $product)
              @include('partials.product-card', ['product' => $product])
            @endforeach
          </div>
          @php $pages = max(1, ($total > 0 && $filter->perPage > 0) ? (int) ceil($total / $filter->perPage) : 1); @endphp
          <div class="catalog-pagination pagination" data-total-pages="{{ $pages }}" data-current-page="{{ max(1, $filter->page) }}">
            <button class="pagination__button pagination__button--previous" type="button">
              <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M3.5625 6.5625L0.5625 3.5625L3.5625 0.5625"
                  stroke="currentColor"
                  stroke-width="1.125"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
            <button class="pagination__button pagination__button--next" type="button">
              <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0.5625 6.5625L3.5625 3.5625L0.5625 0.5625"
                  stroke="currentColor"
                  stroke-width="1.125"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </div>
        </div>
      </section>
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
    </main>
@endsection
