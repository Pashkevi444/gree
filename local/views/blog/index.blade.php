@extends('layouts.app')

@php
    use Gree\Enum\BlogCategory;
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        <section class="hero container" style="background-image: url('/dist/images/f836c0d91ded99c33b3c8e7fb5bcc0fa6f6da8d3.png')">
            <h1 class="hero__title">{{ Language::t('blog.hero.title') }}</h1>
            <p class="hero__description">{{ Language::t('blog.hero.description') }}</p>
        </section>

        @include('blog.partials.section', [
            'title'    => Language::t('blog.title'),
            'items'    => $tips,
            'anchorId' => BlogCategory::Tips->urlSlug(),
            'moreUrl'  => Route::to('blog.category', ['category' => BlogCategory::Tips->urlSlug()]),
        ])

        @include('blog.partials.section', [
            'title'    => Language::t('blog.news'),
            'items'    => $news,
            'anchorId' => BlogCategory::News->urlSlug(),
            'moreUrl'  => Route::to('blog.category', ['category' => BlogCategory::News->urlSlug()]),
        ])
    </main>
@endsection
