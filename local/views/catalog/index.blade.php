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
    <nav class="breadcrumbs container">
      <a class="breadcrumbs__item" href="/">{{ Language::t('breadcrumbs.home') }}</a>
      &nbsp;
      <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M0.5625 6.5625L3.5625 3.5625L0.5625 0.5625"
          stroke="white"
          stroke-width="1.125"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
      &nbsp;
      <a class="breadcrumbs__item" href="/catalog/">{{ Language::t('breadcrumbs.catalog') }}</a>
      &nbsp;
      <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M0.5625 6.5625L3.5625 3.5625L0.5625 0.5625"
          stroke="white"
          stroke-width="1.125"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
      &nbsp;
      <span class="breadcrumbs__item">{{ Language::t('breadcrumbs.wall') }}</span>
    </nav>
    <main class="main">
      <h1 class="main__title container">{{ Language::t('catalog.title') }}</h1>
      <p class="main__description container">{{ Language::t('catalog.description') }}</p>
      <section class="catalog container">
        <form id="filters-form" class="catalog-sidebar" action="/api/catalog" autocomplete="off">
          <div class="catalog-sidebar__title">{{ Language::t('catalog.filters') }}</div>
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
                <div class="checkbox__text">Нет</div>
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
                <div class="checkbox__text">Нет</div>
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
        <h2 class="gree__title">Почему выбирают Gree</h2>
        <p class="gree__description">
          Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем качества и
          решениями для разных сценариев использования.
        </p>
        <a class="gree__button" href="/brand/gree/">Узнать больше о Gree</a>
        <div class="gree-cards">
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M6 10L10 1C10.7956 1 11.5587 1.31607 12.1213 1.87868C12.6839 2.44129 13 3.20435 13 4V8H18.66C18.9499 7.99672 19.2371 8.0565 19.5016 8.17522C19.7661 8.29393 20.0016 8.46873 20.1919 8.68751C20.3821 8.90629 20.5225 9.16382 20.6033 9.44225C20.6842 9.72068 20.7035 10.0134 20.66 10.3L19.28 19.3C19.2077 19.7769 18.9654 20.2116 18.5979 20.524C18.2304 20.8364 17.7623 21.0055 17.28 21H6M6 10V21M6 10H3C2.46957 10 1.96086 10.2107 1.58579 10.5858C1.21071 10.9609 1 11.4696 1 12V19C1 19.5304 1.21071 20.0391 1.58579 20.4142C1.96086 20.7893 2.46957 21 3 21H6"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Гарантия</div>
            <div class="gree-card__description">10 лет гарантии на инвертор кондиционера</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M16 3H1V16H16V3Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M16 8H20L23 11V16H16V8Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M5.5 21C6.88071 21 8 19.8807 8 18.5C8 17.1193 6.88071 16 5.5 16C4.11929 16 3 17.1193 3 18.5C3 19.8807 4.11929 21 5.5 21Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M18.5 21C19.8807 21 21 19.8807 21 18.5C21 17.1193 19.8807 16 18.5 16C17.1193 16 16 17.1193 16 18.5C16 19.8807 17.1193 21 18.5 21Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Доставка</div>
            <div class="gree-card__description">Бесплатно доставим в любую точку города</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_108_377)">
                  <path
                    d="M12 1V23"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </g>
                <defs>
                  <clipPath id="clip0_108_377">
                    <rect width="24" height="24" fill="currentColor" />
                  </clipPath>
                </defs>
              </svg>
            </div>
            <div class="gree-card__title">Рассрочка</div>
            <div class="gree-card__description">Приобретайте комфорт сейчас, а платите потом</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M12.7013 5.30364C12.5181 5.49057 12.4155 5.74189 12.4155 6.00364C12.4155 6.26539 12.5181 6.51671 12.7013 6.70364L14.3013 8.30364C14.4882 8.48687 14.7396 8.5895 15.0013 8.5895C15.2631 8.5895 15.5144 8.48687 15.7013 8.30364L19.4713 4.53364C19.9742 5.64483 20.1264 6.88288 19.9078 8.08279C19.6892 9.2827 19.11 10.3875 18.2476 11.2499C17.3852 12.1124 16.2804 12.6915 15.0805 12.9101C13.8806 13.1287 12.6425 12.9765 11.5313 12.4736L4.62132 19.3836C4.2235 19.7815 3.68393 20.005 3.12132 20.005C2.55871 20.005 2.01914 19.7815 1.62132 19.3836C1.2235 18.9858 1 18.4462 1 17.8836C1 17.321 1.2235 16.7815 1.62132 16.3836L8.53132 9.47364C8.02848 8.36245 7.87624 7.12441 8.09486 5.9245C8.31349 4.72459 8.89261 3.6198 9.75504 2.75736C10.6175 1.89493 11.7223 1.31581 12.9222 1.09718C14.1221 0.878558 15.3601 1.03081 16.4713 1.53364L12.7113 5.29364L12.7013 5.30364Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Сервисный центр</div>
            <div class="gree-card__description">Свой сервисный центр — быстро решаем все вопросы</div>
          </div>
        </div>
        <div class="gree-items gree-items-columns-3">
          <div class="gree-item">
            <div class="gree-item-title">
              №
              <number-flow data-value="1"></number-flow>
              в мире
            </div>
            <div class="gree-item__description">По производству сплит-систем в 2024 году</div>
          </div>
          <div class="gree-item">
            <div class="gree-item-title">
              <number-flow data-value="46"></number-flow>
              технологий
            </div>
            <div class="gree-item__description">Их используют другие бренды в своих кондиционерах</div>
          </div>
          <div class="gree-item">
            <div class="gree-item-title">
              <number-flow data-value="18"></number-flow>
              заводов
            </div>
            <div class="gree-item__description">По всему миру, а также 1411 лабораторий</div>
          </div>
        </div>
      </section>
    </main>
@endsection
