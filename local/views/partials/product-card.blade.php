@php
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
@endphp
<div class="product-card">
  @if ($product->isBestseller)
  <div class="product-card__badge product-card__badge--bestseller">{{ Language::t('product.bestseller') }}</div>
  @endif
  <img class="product-card__image" src="{{ $product->image }}" alt="{{ $product->name }}" />
  <div class="product-card__name">{{ $product->name }}</div>
  <div class="product-card-meta">
    @if ($product->area > 0)
    <div class="product-card-meta__text">{{ Language::t('product.area', ['area' => $product->area]) }}</div>
    @endif
    @if ($product->colors)
    <div class="product-card-meta-colors">
      @foreach ($product->colors as $color)
      <div class="product-card-meta-colors__item @if ($color === \Gree\Enum\Color::White)product-card-meta-colors__item--white @endif" style="--background-color: {{ $color->hex() }}"></div>
      @endforeach
    </div>
    @endif
  </div>
  <div class="product-card__price">{{ Language::t('product.price_from', ['price' => number_format($product->price, 0, '.', ' ')]) }}</div>
  <a class="product-card__button" href="{{ Route::to('catalog.product', ['section' => $product->type->slug(), 'code' => $product->code]) }}">{{ Language::t('product.details') }}</a>
</div>
