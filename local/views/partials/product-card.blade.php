@php use Gree\Helpers\Language; @endphp
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
      @foreach ($product->colors as $hex)
      @php $isWhite = in_array(strtolower($hex), ['#fff', '#ffffff'], true); @endphp
      <div class="product-card-meta-colors__item @if ($isWhite)product-card-meta-colors__item--white @endif" style="--background-color: {{ $hex }}"></div>
      @endforeach
    </div>
    @endif
  </div>
  <div class="product-card__price">{{ Language::t('product.price_from', ['price' => number_format($product->price, 0, '.', ' ')]) }}</div>
  <a class="product-card__button" href="/catalog/{{ $product->code }}/">{{ Language::t('product.details') }}</a>
</div>
