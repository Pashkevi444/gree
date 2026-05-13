@extends('layouts.app')

@section('content')
    <main class="main">
      <section
        class="hero container"
        style="background-image: url('/images/f836c0d91ded99c33b3c8e7fb5bcc0fa6f6da8d3.png')"
      >
        <h1 class="hero__title">Блог Gree</h1>
        <p class="hero__description">Полезные советы и новости от экспертов Gree для вашего дома.</p>
      </section>
      <section class="blog-section container">
        <h2 class="blog-section__title">Полезные советы для вашего дома</h2>
        <div class="blog-section-items">
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/48de8d6ac080bfc7c9be1642b08ed2cf14105329.png" alt="" />
            <div class="blog-card__title">Как выбрать кондиционер для квартиры?</div>
            <div class="blog-card__description">
              Разбираемся на что обратить внимание при выборе кондиционера для разных типов помещения
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/1034d62d090ccaaef63ee76e1c210a928268f4b0.png" alt="" />
            <div class="blog-card__title">Как часто нужно чистить фильтры кондиционера?</div>
            <div class="blog-card__description">
              Регулярная чистка - залог свежего воздуха и долгой службы вашего кондиционера
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/3adb711bbffa7924dfe31cefd17d931439d4b069.png" alt="" />
            <div class="blog-card__title">Почему кондиционер шумит и как это исправить?</div>
            <div class="blog-card__description">
              Основные причины шума и способы устранения распространённых неполадок
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
        </div>
        <div class="blog-section__button">Показать ещё</div>
      </section>
      <section class="blog-section container">
        <h2 class="blog-section__title">Новости Gree</h2>
        <div class="blog-section-items">
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/7a7e2502abf5ebb4684a96f8bcfc153339a5c70f.png" alt="" />
            <div class="blog-card__title">Gree расширяет линейку инверторных сплит-систем</div>
            <div class="blog-card__description">
              Представляем новые модели с улучшенной энергоэффективностью и тихой работой
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/5e957da7f90fa21f6480e94fedfe68e07675086a.png" alt="" />
            <div class="blog-card__title">5 лет гарантии на ключевые компоненты</div>
            <div class="blog-card__description">
              Мы уверены в качестве нашей техники и предоставлем расширенную гарантия
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
          <a class="blog-card" href="/blog-item.html">
            <img class="blog-card__image" src="/local/templates/gree/images/507c01b60af6db2dc4ebf3bd0c8e3dc6832d04d2.png" alt="" />
            <div class="blog-card__title">Gree на выставке Climate World 2026</div>
            <div class="blog-card__description">
              Подводим итоги участия в международной выставке климатического оборудования
            </div>
            <div class="blog-card-footer">
              <div class="blog-card__date">1 мая 2026</div>
              <svg class="blog-card__icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.16406 10H15.8307"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10 4.16687L15.8333 10.0002L10 15.8335"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
          </a>
        </div>
        <div class="blog-section__button">Показать ещё</div>
      </section>
    </main>
@endsection
