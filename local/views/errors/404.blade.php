@extends('layouts.app')

@php use Gree\Helpers\Language; @endphp

@section('content')
<main class="main">
    <section class="error-page">
        <div class="container">
            <div class="error-page__inner">
                <h1 class="error-page__code">404</h1>
                <p class="error-page__title">{{ Language::t('404.title') }}</p>
                <p class="error-page__description">{{ Language::t('404.description') }}</p>
                <a href="/" class="btn btn--primary">{{ Language::t('404.home_link') }}</a>
            </div>
        </div>
    </section>
</main>
@endsection
