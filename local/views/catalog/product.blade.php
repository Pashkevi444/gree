@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <main class="main">
      <div class="wrapper container">
        <div class="catalog">
          <div class="catalog__title">Кондиционеры</div>
          <div class="catalog-items">
            <a class="catalog__item catalog__item--active" href="/catalog/">
              <svg viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0.727539 0.72728H3.63663L5.58572 10.4655C5.65223 10.8003 5.83438 11.1011 6.1003 11.3151C6.36622 11.5292 6.69896 11.6429 7.04027 11.6364H14.1094C14.4507 11.6429 14.7834 11.5292 15.0493 11.3151C15.3152 11.1011 15.4974 10.8003 15.5639 10.4655L16.7275 4.36364H4.3639M7.27299 15.2727C7.27299 15.6744 6.94738 16 6.54572 16C6.14406 16 5.81845 15.6744 5.81845 15.2727C5.81845 14.8711 6.14406 14.5455 6.54572 14.5455C6.94738 14.5455 7.27299 14.8711 7.27299 15.2727ZM15.273 15.2727C15.273 15.6744 14.9474 16 14.5457 16C14.1441 16 13.8184 15.6744 13.8184 15.2727C13.8184 14.8711 14.1441 14.5455 14.5457 14.5455C14.9474 14.5455 15.273 14.8711 15.273 15.2727Z"
                  stroke="currentColor"
                  stroke-width="1.45455"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Настенные
            </a>
            <a class="catalog__item" href="/catalog/">
              <svg viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0.727539 0.72728H3.63663L5.58572 10.4655C5.65223 10.8003 5.83438 11.1011 6.1003 11.3151C6.36622 11.5292 6.69896 11.6429 7.04027 11.6364H14.1094C14.4507 11.6429 14.7834 11.5292 15.0493 11.3151C15.3152 11.1011 15.4974 10.8003 15.5639 10.4655L16.7275 4.36364H4.3639M7.27299 15.2727C7.27299 15.6744 6.94738 16 6.54572 16C6.14406 16 5.81845 15.6744 5.81845 15.2727C5.81845 14.8711 6.14406 14.5455 6.54572 14.5455C6.94738 14.5455 7.27299 14.8711 7.27299 15.2727ZM15.273 15.2727C15.273 15.6744 14.9474 16 14.5457 16C14.1441 16 13.8184 15.6744 13.8184 15.2727C13.8184 14.8711 14.1441 14.5455 14.5457 14.5455C14.9474 14.5455 15.273 14.8711 15.273 15.2727Z"
                  stroke="currentColor"
                  stroke-width="1.45455"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Колонные
            </a>
            <a class="catalog__item" href="/catalog/">
              <svg viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0.727539 0.72728H3.63663L5.58572 10.4655C5.65223 10.8003 5.83438 11.1011 6.1003 11.3151C6.36622 11.5292 6.69896 11.6429 7.04027 11.6364H14.1094C14.4507 11.6429 14.7834 11.5292 15.0493 11.3151C15.3152 11.1011 15.4974 10.8003 15.5639 10.4655L16.7275 4.36364H4.3639M7.27299 15.2727C7.27299 15.6744 6.94738 16 6.54572 16C6.14406 16 5.81845 15.6744 5.81845 15.2727C5.81845 14.8711 6.14406 14.5455 6.54572 14.5455C6.94738 14.5455 7.27299 14.8711 7.27299 15.2727ZM15.273 15.2727C15.273 15.6744 14.9474 16 14.5457 16C14.1441 16 13.8184 15.6744 13.8184 15.2727C13.8184 14.8711 14.1441 14.5455 14.5457 14.5455C14.9474 14.5455 15.273 14.8711 15.273 15.2727Z"
                  stroke="currentColor"
                  stroke-width="1.45455"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Промышленные
            </a>
          </div>
        </div>
        <div class="product-wrapper">
          <div class="product">
            <div class="product-left">
              <div class="product__sku">Артикул: 1234567</div>
              <div class="product-carousel-main">
                <div class="product-carousel product-carousel--main">
                  <div class="product-carousel-wrapper">
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                  </div>
                </div>
              </div>
              <div class="product-carousel-thumbs">
                <button
                  class="product-carousel-thumbs__navigation-button product-carousel-thumbs__navigation-button--previous"
                  type="button"
                >
                  <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M3.5625 6.5625L0.5625 3.5625L3.5625 0.5625"
                      stroke="currentColor"
                      stroke-width="1.125"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </button>
                <div class="product-carousel product-carousel--thumbs">
                  <div class="product-carousel-wrapper">
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                    <img
                      class="product-carousel__slide"
                      src="/local/templates/gree/images/11bf4d324216e9be17bf9d02e78c6f0c13831844.png"
                      alt=""
                    />
                  </div>
                </div>
                <button
                  class="product-carousel-thumbs__navigation-button product-carousel-thumbs__navigation-button--next"
                  type="button"
                >
                  <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M0.5625 6.5625L3.5625 3.5625L0.5625 0.5625"
                      stroke="currentColor"
                      stroke-width="1.125"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </button>
              </div>
            </div>
            <form id="product-form" class="product-right" method="post" autocomplete="off">
              <input type="hidden" name="product_id" value="1234567" />
              <h1 class="product__title">Gree Pular GWH07AGA-K3NNA1B</h1>
              <div class="product-colors">
                <div class="product-colors__title">Цвет</div>
                <div class="product-colors-items">
                  <label class="product-colors-item" style="background-color: #fff">
                    <input class="product-colors-item__control" type="radio" name="color" value="white" />
                  </label>
                  <label class="product-colors-item" style="background-color: #000">
                    <input class="product-colors-item__control" type="radio" name="color" value="black" checked />
                  </label>
                  <label class="product-colors-item" style="background-color: #6d6e72">
                    <input class="product-colors-item__control" type="radio" name="color" value="gray" />
                  </label>
                </div>
              </div>
              <div class="product-area">
                <div class="product-area__title">Мощность (площадь применения)</div>
                <div class="product-area-items">
                  <label class="product-area-item">
                    <input class="product-area-item__control" type="radio" name="area" value="30" />
                    <div class="product-area-item__text">до 30 м²</div>
                  </label>
                  <label class="product-area-item">
                    <input class="product-area-item__control" type="radio" name="area" value="30" />
                    <div class="product-area-item__text">до 30 м²</div>
                  </label>
                  <label class="product-area-item">
                    <input class="product-area-item__control" type="radio" name="area" value="30" />
                    <div class="product-area-item__text">до 30 м²</div>
                  </label>
                  <label class="product-area-item">
                    <input class="product-area-item__control" type="radio" name="area" value="30" checked />
                    <div class="product-area-item__text">до 30 м²</div>
                  </label>
                </div>
              </div>
              <div class="product-price">
                <div class="product-price__title">Цена</div>
                <div class="product-price__text">от 7 200 000 000 UZS</div>
              </div>
              <div class="product-stock product-stock--in-stock">В наличии</div>
              <div class="product-buttons">
                <button class="product-buttons__item product-buttons__item--add-to-cart" type="submit">
                  Добавить в корзину
                </button>
                <a class="product-buttons__item product-buttons__item--help" href="">Нужна помощь</a>
              </div>
            </form>
          </div>
          <div class="product-tabs">
            <div class="product-tabs-header">
              <button class="product-tabs-header__button product-tabs-header__button--active" type="button">
                Технические характеристики
              </button>
              <button class="product-tabs-header__button" type="button">Функции</button>
              <button class="product-tabs-header__button" type="button">Комплектация</button>
              <button class="product-tabs-header__button" type="button">Гарантия</button>
              <button class="product-tabs-header__button" type="button">Установка</button>
            </div>
            <div class="product-tabs-body">
              <div class="product-tabs-item">
                Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем
                качества и решениями для разных сценариев использования.Gree — мировой лидер в производстве
                кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев
                использования.Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим
                контролем качества и решениями для разных сценариев использования.
                <br />
                <br />
                <table class="product-table">
                  <tbody>
                    <tr>
                      <td>Модель</td>
                      <td>GWH24FGD-K6DNA1A</td>
                    </tr>
                    <tr>
                      <td>Тип</td>
                      <td>Настенный кондиционер</td>
                    </tr>
                    <tr>
                      <td>Мощность охлаждения</td>
                      <td>7.1 кВт</td>
                    </tr>
                    <tr>
                      <td>Мощность обогрева</td>
                      <td>7.3 кВт</td>
                    </tr>
                    <tr>
                      <td>Площадь помещения</td>
                      <td>50-70 м²</td>
                    </tr>
                    <tr>
                      <td>Уровень шума (внутренний блок)</td>
                      <td>22-42 дБ</td>
                    </tr>
                    <tr>
                      <td>Инвертор</td>
                      <td>Да</td>
                    </tr>
                    <tr>
                      <td>Класс энергоэффективности</td>
                      <td>А++</td>
                    </tr>
                    <tr>
                      <td>Хладагент</td>
                      <td>R32</td>
                    </tr>
                    <tr>
                      <td>Габариты внутреннего блока</td>
                      <td>1101 х 327 х 249 мм</td>
                    </tr>
                    <tr>
                      <td>Габариты внутреннего блока</td>
                      <td>958 х 660 х 402 мм</td>
                    </tr>
                    <tr>
                      <td>Вес внутреннего блока</td>
                      <td>15,5 кг</td>
                    </tr>
                    <tr>
                      <td>Вес наружного блока</td>
                      <td>43 кг</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="product-tabs-item">
                <div class="product-functions">
                  <div class="product-functions-items">
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 1
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 2
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 3
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 4
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 5
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 6
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 7
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 8
                    </div>
                    <div class="product-functions-item">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 9
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 10
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 11
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 12
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 13
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 14
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 15
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 16
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 17
                    </div>
                    <div class="product-functions-item product-functions-item--hidden">
                      <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.75 9.75L9.75 0.75C10.5456 0.75 11.3087 1.06607 11.8713 1.62868C12.4339 2.19129 12.75 2.95435 12.75 3.75V7.75H18.41C18.6999 7.74672 18.9871 7.8065 19.2516 7.92522C19.5161 8.04393 19.7516 8.21873 19.9419 8.43751C20.1321 8.65629 20.2725 8.91382 20.3533 9.19225C20.4342 9.47068 20.4535 9.76336 20.41 10.05L19.03 19.05C18.9577 19.5269 18.7154 19.9616 18.3479 20.274C17.9804 20.5864 17.5123 20.7555 17.03 20.75H5.75M5.75 9.75V20.75M5.75 9.75H2.75C2.21957 9.75 1.71086 9.96071 1.33579 10.3358C0.960714 10.7109 0.75 11.2196 0.75 11.75V18.75C0.75 19.2804 0.960714 19.7891 1.33579 20.1642C1.71086 20.5393 2.21957 20.75 2.75 20.75H5.75"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      Функция 18
                    </div>
                  </div>
                  <button class="product-functions__button" type="button"></button>
                </div>
              </div>
              <div class="product-tabs-item">
                Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем
                качества и решениями для разных сценариев использования.Gree — мировой лидер в производстве
                кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев
                использования.Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим
                контролем качества и решениями для разных сценариев использования.
              </div>
              <div class="product-tabs-item">
                Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем
                качества и решениями для разных сценариев использования.Gree — мировой лидер в производстве
                кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев
                использования.Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим
                контролем качества и решениями для разных сценариев использования.
              </div>
              <div class="product-tabs-item">
                Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем
                качества и решениями для разных сценариев использования.Gree — мировой лидер в производстве
                кондиционеров с собственными технологиями, строгим контролем качества и решениями для разных сценариев
                использования.Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим
                контролем качества и решениями для разных сценариев использования.
              </div>
            </div>
          </div>
        </div>
      </div>
      <section class="gree container">
        <h2 class="gree__title">Почему выбирают Gree</h2>
        <p class="gree__description">
          Gree — мировой лидер в производстве кондиционеров с собственными технологиями, строгим контролем качества и
          решениями для разных сценариев использования.
        </p>
        <a class="gree__button" href="/brand/gree/">Узнать больше о Gree</a>
        <div class="gree-cards">
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M6 10L10 1C10.7956 1 11.5587 1.31607 12.1213 1.87868C12.6839 2.44129 13 3.20435 13 4V8H18.66C18.9499 7.99672 19.2371 8.0565 19.5016 8.17522C19.7661 8.29393 20.0016 8.46873 20.1919 8.68751C20.3821 8.90629 20.5225 9.16382 20.6033 9.44225C20.6842 9.72068 20.7035 10.0134 20.66 10.3L19.28 19.3C19.2077 19.7769 18.9654 20.2116 18.5979 20.524C18.2304 20.8364 17.7623 21.0055 17.28 21H6M6 10V21M6 10H3C2.46957 10 1.96086 10.2107 1.58579 10.5858C1.21071 10.9609 1 11.4696 1 12V19C1 19.5304 1.21071 20.0391 1.58579 20.4142C1.96086 20.7893 2.46957 21 3 21H6"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Гарантия</div>
            <div class="gree-card__description">10 лет гарантии на инвертор кондиционера</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M16 3H1V16H16V3Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M16 8H20L23 11V16H16V8Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M5.5 21C6.88071 21 8 19.8807 8 18.5C8 17.1193 6.88071 16 5.5 16C4.11929 16 3 17.1193 3 18.5C3 19.8807 4.11929 21 5.5 21Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M18.5 21C19.8807 21 21 19.8807 21 18.5C21 17.1193 19.8807 16 18.5 16C17.1193 16 16 17.1193 16 18.5C16 19.8807 17.1193 21 18.5 21Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Доставка</div>
            <div class="gree-card__description">Бесплатно доставим в любую точку города</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_108_377)">
                  <path
                    d="M12 1V23"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </g>
                <defs>
                  <clipPath id="clip0_108_377">
                    <rect width="24" height="24" fill="currentColor" />
                  </clipPath>
                </defs>
              </svg>
            </div>
            <div class="gree-card__title">Рассрочка</div>
            <div class="gree-card__description">Приобретайте комфорт сейчас, а платите потом</div>
          </div>
          <div class="gree-card">
            <div class="gree-card__icon">
              <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M12.7013 5.30364C12.5181 5.49057 12.4155 5.74189 12.4155 6.00364C12.4155 6.26539 12.5181 6.51671 12.7013 6.70364L14.3013 8.30364C14.4882 8.48687 14.7396 8.5895 15.0013 8.5895C15.2631 8.5895 15.5144 8.48687 15.7013 8.30364L19.4713 4.53364C19.9742 5.64483 20.1264 6.88288 19.9078 8.08279C19.6892 9.2827 19.11 10.3875 18.2476 11.2499C17.3852 12.1124 16.2804 12.6915 15.0805 12.9101C13.8806 13.1287 12.6425 12.9765 11.5313 12.4736L4.62132 19.3836C4.2235 19.7815 3.68393 20.005 3.12132 20.005C2.55871 20.005 2.01914 19.7815 1.62132 19.3836C1.2235 18.9858 1 18.4462 1 17.8836C1 17.321 1.2235 16.7815 1.62132 16.3836L8.53132 9.47364C8.02848 8.36245 7.87624 7.12441 8.09486 5.9245C8.31349 4.72459 8.89261 3.6198 9.75504 2.75736C10.6175 1.89493 11.7223 1.31581 12.9222 1.09718C14.1221 0.878558 15.3601 1.03081 16.4713 1.53364L12.7113 5.29364L12.7013 5.30364Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </div>
            <div class="gree-card__title">Сервисный центр</div>
            <div class="gree-card__description">Свой сервисный центр — быстро решаем все вопросы</div>
          </div>
        </div>
        <div class="gree-items gree-items-columns-3">
          <div class="gree-item">
            <div class="gree-item-title">
              №
              <number-flow data-value="1"></number-flow>
              в мире
            </div>
            <div class="gree-item__description">По производству сплит-систем в 2024 году</div>
          </div>
          <div class="gree-item">
            <div class="gree-item-title">
              <number-flow data-value="46"></number-flow>
              технологий
            </div>
            <div class="gree-item__description">Их используют другие бренды в своих кондиционерах</div>
          </div>
          <div class="gree-item">
            <div class="gree-item-title">
              <number-flow data-value="18"></number-flow>
              заводов
            </div>
            <div class="gree-item__description">По всему миру, а также 1411 лабораторий</div>
          </div>
        </div>
      </section>
    </main>
@endsection
