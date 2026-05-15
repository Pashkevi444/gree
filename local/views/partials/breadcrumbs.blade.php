{{--
    Breadcrumb trail renderer. Caller passes `$breadcrumbs` — a
    Gree\Collection\BreadcrumbCollection. Items with empty url render as <span>
    (current page), others as <a>. SVG separator goes between items.
--}}
@if ($breadcrumbs && $breadcrumbs->count())
<nav class="breadcrumbs container">
  @foreach ($breadcrumbs as $i => $crumb)
    @if ($crumb->isCurrent())
      <span class="breadcrumbs__item">{{ $crumb->label }}</span>
    @else
      <a class="breadcrumbs__item" href="{{ $crumb->url }}">{{ $crumb->label }}</a>
    @endif

    @if (!$loop->last)
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
    @endif
  @endforeach
</nav>
@endif
