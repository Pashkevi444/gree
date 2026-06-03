@extends('layouts.app')

@php
    use Gree\Enum\BlogCategory;
    use Gree\Helpers\Language;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        <section class="hero container" style="background-image: url('/dist/images/f836c0d91ded99c33b3c8e7fb5bcc0fa6f6da8d3.png')">
            <h1 class="hero__title">{{ Language::t('blog.hero.title') }}</h1>
            <p class="hero__description">{{ Language::t('blog.hero.description') }}</p>
        </section>

        @php
            $titleKey = $category === BlogCategory::News ? 'blog.news' : 'blog.title';
        @endphp

        @include('blog.partials.section', [
            'title'    => Language::t($titleKey),
            'items'    => $items,
            'anchorId' => $category->urlSlug(),
            'moreUrl'  => null,
        ])
    </main>
@endsection
