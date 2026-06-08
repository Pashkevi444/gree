@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    /** @var \Gree\DTO\BlogArticleDto $article */
    /** @var \Gree\Collection\BlogArticleCollection $related */
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        <article class="article container">
            <h1 class="article__title">{{ $article->title }}</h1>
            <div class="article-meta">
                @if ($article->date)
                    <div class="article-meta__item">{{ Language::date($article->date) }}</div>
                @endif
                <div class="article-meta__item">{{ $categoryLabel }}</div>
                @if ($article->readingTime > 0)
                    <div class="article-meta__item">
                        {{ Language::t('blog.reading_minutes', ['minutes' => $article->readingTime]) }}
                    </div>
                @endif
            </div>
            @if ($article->image)
                <img class="article__image" src="{{ $article->image }}" alt="{{ $article->title }}" />
            @endif
            <div class="article__content">
                {!! $article->description !!}
            </div>
        </article>
        @if ($related->count())
            <section class="blog container">
                @foreach ($related as $card)
                    <a class="blog-card" href="{{ $card->url }}">
                        @if ($card->image)
                            <img class="blog-card__image" src="{{ $card->image }}" alt="">
                        @endif
                        <div class="blog-card__title">{{ $card->title }}</div>
                        <div class="blog-card__description">{{ $card->description }}</div>
                        <div class="blog-card-footer">
                            <div class="blog-card__date">{{ Language::date($card->date) }}</div>
                            <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.16406 10H15.8307" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 4.16687L15.8333 10.0002L10 15.8335" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </section>
        @endif
    </main>
@endsection
