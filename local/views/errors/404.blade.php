@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
@endphp

@section('content')
<main class="main">
    <section class="hero container">
        <div class="hero-wrapper">
            <h1 class="hero__title">{{ Language::t('404.title') }}</h1>
            <p class="hero__description">{{ Language::t('404.description') }}</p>
            <a class="hero__button" href="{{ Route::to('home') }}">{{ Language::t('404.home_link') }}</a>
            <img class="hero__image" src="/dist/images/img(1).png" alt="" />
        </div>
    </section>
</main>
@endsection
