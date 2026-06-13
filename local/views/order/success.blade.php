@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
    /** @var \Gree\DTO\OrderDto $order */
@endphp

@section('content')
    <main class="main">
        <section class="hero container">
            <img class="hero__image" src="/dist/images/9d6a1af34169ceecd352ce6bf8955f4460d32bd6.png" alt="">
            <h1 class="hero__title">{{ Language::t('order_success.title') }}</h1>
            <p class="hero__description">{{ Language::t('order_success.description') }}</p>
            <a class="hero__button" href="{{ Route::to('home') }}">{{ Language::t('order_success.cta') }}</a>
        </section>
    </main>
@endsection
