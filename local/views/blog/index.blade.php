@extends('layouts.app')

@php
    use Gree\Helpers\Language;
    use Gree\Helpers\Route;
    /** @var \Gree\Collection\BlogArticleCollection $tips */
    /** @var \Gree\Collection\BlogArticleCollection $news */

    $blogApiUrl = Route::to('api.v1.blog.paginate');
@endphp

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
        @include('blog.partials.section', [
            'title'    => Language::t('blog.title'),
            'items'    => $tips,
            'hasMore'  => $hasMoreTips,
            'category' => 'tips',
            'pageSize' => $pageSize,
        ])

        @include('blog.partials.section', [
            'title'    => Language::t('blog.news'),
            'items'    => $news,
            'hasMore'  => $hasMoreNews,
            'category' => 'news',
            'pageSize' => $pageSize,
        ])
    </main>

    <script>
        (function () {
            const arrowSvg = `<svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4.16406 10H15.8307" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10 4.16687L15.8333 10.0002L10 15.8335" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;

            function renderCard(item) {
                const el = document.createElement('a');
                el.className = 'blog-card';
                el.href = item.url;
                el.innerHTML = `
                    <img class="blog-card__image" src="${item.image}" alt="">
                    <div class="blog-card__title">${item.title}</div>
                    <div class="blog-card__description">${item.description}</div>
                    <div class="blog-card-footer">
                        <div class="blog-card__date">${item.date}</div>
                        ${arrowSvg}
                    </div>`;
                return el;
            }

            document.querySelectorAll('[data-load-more]').forEach(function (btn) {
                btn.addEventListener('click', async function () {
                    const category = btn.dataset.loadMore;
                    const limit = parseInt(btn.dataset.pageSize || '3', 10);
                    const container = document.querySelector(`[data-blog-items="${category}"]`);
                    const offset = container.querySelectorAll('.blog-card').length;

                    btn.setAttribute('disabled', 'disabled');
                    try {
                        const apiUrl = @json($blogApiUrl);
                        const res = await fetch(`${apiUrl}?category=${category}&offset=${offset}&limit=${limit}`);
                        const data = await res.json();
                        data.items.forEach(function (item) {
                            container.appendChild(renderCard(item));
                        });
                        if (!data.hasMore) {
                            btn.remove();
                        }
                    } catch (e) {
                        console.error('blog load more failed', e);
                    } finally {
                        btn.removeAttribute('disabled');
                    }
                });
            });
        })();
    </script>
@endsection
