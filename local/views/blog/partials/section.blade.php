@php
    use Gree\Helpers\Language;
@endphp
<section class="blog-section container">
    <h2 class="blog-section__title">{{ $title }}</h2>
    <div class="blog-section-items" data-blog-items="{{ $category }}">
        @foreach ($items as $article)
            <a class="blog-card" href="{{ $article->url }}">
                @if ($article->image)
                    <img class="blog-card__image" src="{{ $article->image }}" alt="">
                @endif
                <div class="blog-card__title">{{ $article->title }}</div>
                <div class="blog-card__description">{{ $article->description }}</div>
                <div class="blog-card-footer">
                    <div class="blog-card__date">{{ $article->date }}</div>
                    <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.16406 10H15.8307" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 4.16687L15.8333 10.0002L10 15.8335" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
    @if ($hasMore)
        <button class="blog-section__button" type="button" data-load-more="{{ $category }}" data-page-size="{{ $pageSize }}">
            {{ Language::t('blog.show_more') }}
        </button>
    @endif
</section>
