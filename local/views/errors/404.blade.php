@extends('layouts.app')

@section('content')
<main class="main">
    <section class="error-page">
        <div class="container">
            <div class="error-page__inner">
                <h1 class="error-page__code">404</h1>
                <p class="error-page__title">Страница не найдена</p>
                <p class="error-page__description">Запрашиваемая страница не существует или была удалена.</p>
                <a href="/" class="btn btn--primary">На главную</a>
            </div>
        </div>
    </section>
</main>
@endsection
