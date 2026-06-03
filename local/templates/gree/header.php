<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\MenuServiceInterface;
use Gree\Core\App;
use Gree\Enum\Locale;
use Gree\Helpers\Language;
use Gree\Helpers\Route;

$currentLocale = App::get(LanguageServiceInterface::class)->get();
$csrfToken = App::get(CsrfServiceInterface::class)->readOrIssue();
$menu = App::get(MenuServiceInterface::class)->getHeaderMenu();
$catalogItem = null;
$navItems = [];
foreach ($menu as $item) {
    if ($item->code === 'catalog') {
        $catalogItem = $item;
    } else {
        $navItems[] = $item;
    }
}
?>
<!doctype html>
<html lang="<?= $currentLocale->value ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>" />
    <?php $APPLICATION->ShowHead(); ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
  </head>
  <body>
<header class="header">
    <div class="container">
      <div class="header-top">
        <div class="header-social">
          <a class="header-social-item" href="tel:+998 00 000 00 00">
            <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M13.9252 10.6134V12.6134C13.926 12.7991 13.888 12.9828 13.8136 13.153C13.7392 13.3231 13.6301 13.4758 13.4933 13.6013C13.3565 13.7268 13.195 13.8224 13.0191 13.8819C12.8432 13.9413 12.6568 13.9634 12.4719 13.9467C10.4205 13.7238 8.44991 13.0228 6.71858 11.9001C5.1078 10.8765 3.74214 9.51084 2.71858 7.90006C1.5919 6.16087 0.890744 4.18073 0.671915 2.12006C0.655255 1.93571 0.677165 1.7499 0.736249 1.57448C0.795332 1.39905 0.890296 1.23785 1.01509 1.10114C1.13989 0.964431 1.29178 0.855202 1.46111 0.78041C1.63043 0.705618 1.81348 0.666903 1.99858 0.666729H3.99858C4.32212 0.663544 4.63578 0.778114 4.88109 0.989084C5.1264 1.20005 5.28663 1.49303 5.33192 1.8134C5.41633 2.45344 5.57288 3.08188 5.79858 3.68673C5.88828 3.92534 5.90769 4.18467 5.85452 4.43398C5.80135 4.68329 5.67782 4.91214 5.49858 5.0934L4.65192 5.94006C5.60095 7.60909 6.98288 8.99103 8.65192 9.94006L9.49858 9.0934C9.67984 8.91415 9.90868 8.79063 10.158 8.73746C10.4073 8.68429 10.6666 8.7037 10.9052 8.7934C11.5101 9.0191 12.1385 9.17565 12.7786 9.26006C13.1024 9.30575 13.3982 9.46887 13.6096 9.71839C13.821 9.96792 13.9334 10.2864 13.9252 10.6134Z"
                stroke="currentColor"
                stroke-width="1.33333"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            +998 00 000 00 00
          </a>
          <a class="header-social-item" href="https://t.me/username" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_87_1413)">
                <path
                  d="M14.6668 1.33334L7.3335 8.66667"
                  stroke="currentColor"
                  stroke-width="1.33333"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M14.6668 1.33334L10.0002 14.6667L7.3335 8.66667L1.3335 6L14.6668 1.33334Z"
                  stroke="currentColor"
                  stroke-width="1.33333"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </g>
              <defs>
                <clipPath id="clip0_87_1413">
                  <rect width="16" height="16" fill="currentColor" />
                </clipPath>
              </defs>
            </svg>
            username
          </a>
          <a class="header-social-item" href="mailto:example@example.com">
            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_87_1418)">
                <path
                  d="M8.00016 10.6667C9.47292 10.6667 10.6668 9.47276 10.6668 8C10.6668 6.52724 9.47292 5.33334 8.00016 5.33334C6.5274 5.33334 5.3335 6.52724 5.3335 8C5.3335 9.47276 6.5274 10.6667 8.00016 10.6667Z"
                  stroke="currentColor"
                  stroke-width="1.33333"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M10.6668 5.33333V8.66666C10.6668 9.19709 10.8775 9.7058 11.2526 10.0809C11.6277 10.4559 12.1364 10.6667 12.6668 10.6667C13.1973 10.6667 13.706 10.4559 14.0811 10.0809C14.4561 9.7058 14.6668 9.19709 14.6668 8.66666V7.99999C14.6667 6.49535 14.1577 5.03498 13.2224 3.85635C12.287 2.67772 10.9805 1.85014 9.51526 1.50819C8.04999 1.16624 6.51212 1.33002 5.15173 1.9729C3.79134 2.61579 2.68843 3.69996 2.02234 5.04914C1.35625 6.39832 1.16615 7.93315 1.48295 9.40407C1.79975 10.875 2.60482 12.1955 3.76726 13.1508C4.92969 14.1062 6.38112 14.6402 7.88555 14.6661C9.38997 14.692 10.8589 14.2082 12.0535 13.2933"
                  stroke="currentColor"
                  stroke-width="1.33333"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </g>
              <defs>
                <clipPath id="clip0_87_1418">
                  <rect width="16" height="16" fill="currentColor" />
                </clipPath>
              </defs>
            </svg>
            example@example.com
          </a>
        </div>
        <?php
        $isRu = $currentLocale === Locale::Ru;
        $otherLocale = $isRu ? Locale::Uz : Locale::Ru;
        $otherLocaleUrl = Route::to('lang.switch', ['locale' => $otherLocale->value]);
        $currentLabel = Language::t('header.lang.' . $currentLocale->value);
        ?>
        <?php
        // ВНИМАНИЕ: тут <div>, не <a>. HTML-spec запрещает <select> внутри <a>,
        // браузер выкидывает его наружу — main.js не находит .language-select__control,
        // падает с TypeError на selectedIndex, и handler header-popup-menu не вешается.
        ?>
        <div class="language-select" title="<?= Language::t('header.lang.' . $otherLocale->value) ?>">
          <div class="language-select__country-icon">
            <?php
            // main.js скрывает все svg с data-id !== select.value — поэтому
            // рендерим ОБА флага сразу, JS оставит видимым нужный.
            ?>
            <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-id="ru">
              <g clip-path="url(#clip0_lang_ru)">
                <path d="M0 0H18V6.00117H0V0Z" fill="white" />
                <path d="M0 6.00119H18V11.9988H0V6.00119Z" fill="#729AE6" />
                <path d="M0 11.9988H18V18H0V11.9988Z" fill="#C64F45" />
              </g>
              <defs>
                <clipPath id="clip0_lang_ru"><rect width="18" height="18" fill="white" /></clipPath>
              </defs>
            </svg>
            <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-id="uz">
              <g clip-path="url(#clip0_lang_uz)">
                <path d="M0 0H18V6H0V0Z" fill="#1EB53A" />
                <path d="M0 6H18V7H0V6Z" fill="#CE1126" />
                <path d="M0 7H18V11H0V7Z" fill="white" />
                <path d="M0 11H18V12H0V11Z" fill="#CE1126" />
                <path d="M0 12H18V18H0V12Z" fill="#0099B5" />
              </g>
              <defs>
                <clipPath id="clip0_lang_uz"><rect width="18" height="18" fill="white" /></clipPath>
              </defs>
            </svg>
          </div>
          <div class="language-select__text"><?= $currentLabel ?></div>
          <div class="language-select__icon">
            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.75 0.75L4.75 4.75L8.75 0.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <select class="language-select__control" autocomplete="off" onchange="window.location='/lang/'+this.value+'/'">
            <?php foreach (Locale::cases() as $loc): ?>
              <option value="<?= $loc->value ?>"<?= $loc === $currentLocale ? ' selected' : '' ?>><?= Language::t('header.lang.' . $loc->value) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="header-body">
        <a class="header__logotype" href="<?= Route::to('home') ?>">
          <svg width="155" height="30" viewBox="0 0 155 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M36.1682 13.1261C36.2615 13.7457 36.3318 14.3638 36.3318 14.9976C36.3318 23.2714 28.1564 30 18.1534 30C16.6815 30 15.2444 29.8675 13.8721 29.5711C14.442 28.3854 15.7171 25.7405 17.7851 20.9836C20.8087 14.0256 15.3456 13.7347 15.3456 13.7347L15.4673 13.1261H36.1682Z"
              fill="currentColor"
            />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M18.1539 0C26.2297 0 33.0896 4.38561 35.4438 10.4071H15.8274L7.81412 27.333C3.10272 24.6014 0 20.0975 0 14.9976C0 6.73254 8.15008 0 18.1539 0ZM154.054 6.09318H139.287C139.287 6.09318 129.325 5.64698 128.07 16.2464C126.815 26.8474 132.211 28.5778 137.441 28.5778H149.056L149.809 25.6412H139.4C139.4 25.6412 134.393 25.2123 135.148 18.0399C135.156 17.9808 135.164 17.9232 135.164 17.8822H150.238L151.056 15.3201H135.577C136.658 10.2983 139.012 8.70027 142.397 8.70027H153.236L154.054 6.09318ZM128.763 6.09318H114.008C114.008 6.09318 104.036 5.64698 102.783 16.2464C101.527 26.8474 106.912 28.5778 112.151 28.5778H123.756L124.51 25.6412H114.106C114.106 25.6412 109.094 25.2123 109.847 18.0399C109.866 17.9896 109.877 17.9362 109.878 17.8822H124.953L125.764 15.3201H110.286C111.368 10.2983 113.735 8.70027 117.118 8.70027H127.949L128.763 6.09318ZM77.685 6.09318H95.4286C95.4286 6.09318 102.689 5.80938 101.516 11.793C100.323 17.7979 95.2587 18.0257 94.0824 18.0257L99.8531 28.6141H94.6042C94.6042 28.6141 91.8374 29.4915 89.4224 25.0294C87.0051 20.5587 84.1838 15.2696 84.1838 15.2696H89.4224C89.4224 15.2696 93.1931 15.3768 94.2579 12.4528C95.3156 9.50833 93.1354 8.85557 91.3591 8.85557H84.4683L79.0423 28.7355H71.6203L77.685 6.09318ZM73.8313 6.03012H56.2506C56.2506 6.03012 46.842 5.85195 43.9251 16.5704C43.9251 16.5704 40.171 27.2967 51.2214 28.5747H64.2568C64.2568 28.5747 68.2836 29.2724 69.9689 22.6313C71.6693 15.9862 71.7816 15.2357 71.7816 15.2357H59.1074L58.1201 18.2661H64.071L62.543 23.5639C62.543 23.5639 62.1453 25.4993 60.5588 25.5978C58.9857 25.724 54.8949 25.5978 54.8949 25.5978C54.8949 25.5978 51.2214 25.7918 50.8672 21.7128C50.5194 17.6181 52.6245 9.18432 59.2165 9.18432H72.7626L73.8313 6.03012Z"
              fill="currentColor"
            />
          </svg>
        </a>
<?php if ($catalogItem !== null): ?>
          <?php
          $catalogTag = $catalogItem->hasUrl() ? 'a' : 'button';
          $catalogAttr = $catalogItem->hasUrl()
              ? 'href="' . htmlspecialchars($catalogItem->url, ENT_QUOTES) . '"'
              : 'type="button"';
          $catalogPopup = $catalogItem->hasChildren() ? ' data-popup-menu="catalog"' : '';
          ?>
          <<?= $catalogTag ?> class="header__catalog-button" <?= $catalogAttr ?><?= $catalogPopup ?>>
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M0 1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H13C13.2652 0 13.5196 0.105357 13.7071 0.292893C13.8946 0.48043 14 0.734784 14 1C14 1.26522 13.8946 1.51957 13.7071 1.70711C13.5196 1.89464 13.2652 2 13 2H1C0.734784 2 0.48043 1.89464 0.292893 1.70711C0.105357 1.51957 0 1.26522 0 1ZM0 6C0 5.73478 0.105357 5.48043 0.292893 5.29289C0.48043 5.10536 0.734784 5 1 5H13C13.2652 5 13.5196 5.10536 13.7071 5.29289C13.8946 5.48043 14 5.73478 14 6C14 6.26522 13.8946 6.51957 13.7071 6.70711C13.5196 6.89464 13.2652 7 13 7H1C0.734784 7 0.48043 6.89464 0.292893 6.70711C0.105357 6.51957 0 6.26522 0 6ZM0 11C0 10.7348 0.105357 10.4804 0.292893 10.2929C0.48043 10.1054 0.734784 10 1 10H13C13.2652 10 13.5196 10.1054 13.7071 10.2929C13.8946 10.4804 14 10.7348 14 11C14 11.2652 13.8946 11.5196 13.7071 11.7071C13.5196 11.8946 13.2652 12 13 12H1C0.734784 12 0.48043 11.8946 0.292893 11.7071C0.105357 11.5196 0 11.2652 0 11Z"
                fill="currentColor"
              />
            </svg>
            <?= htmlspecialchars($catalogItem->label) ?>
          </<?= $catalogTag ?>>
        <?php endif; ?>
        <nav class="header-navigation">
          <?php foreach ($navItems as $item): ?>
            <a
              class="header-navigation__item"
              href="<?= htmlspecialchars($item->hasUrl() ? $item->url : '#', ENT_QUOTES) ?>"
              <?= $item->hasChildren() ? ' data-popup-menu="' . htmlspecialchars($item->code, ENT_QUOTES) . '"' : '' ?>
            ><?= htmlspecialchars($item->label) ?></a>
          <?php endforeach; ?>
        </nav>
        <a class="header__cart-button" href="<?= Route::to('cart.index') ?>">
          <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M0.727051 0.727272H3.63614L5.58523 10.4655C5.65174 10.8003 5.83389 11.1011 6.09981 11.3151C6.36573 11.5292 6.69847 11.6429 7.03978 11.6364H14.1089C14.4502 11.6429 14.7829 11.5292 15.0488 11.3151C15.3148 11.1011 15.4969 10.8003 15.5634 10.4655L16.7271 4.36364H4.36341M7.27251 15.2727C7.27251 15.6744 6.94689 16 6.54523 16C6.14357 16 5.81796 15.6744 5.81796 15.2727C5.81796 14.8711 6.14357 14.5455 6.54523 14.5455C6.94689 14.5455 7.27251 14.8711 7.27251 15.2727ZM15.2725 15.2727C15.2725 15.6744 14.9469 16 14.5452 16C14.1436 16 13.818 15.6744 13.818 15.2727C13.818 14.8711 14.1436 14.5455 14.5452 14.5455C14.9469 14.5455 15.2725 14.8711 15.2725 15.2727Z"
              stroke="white"
              stroke-width="1.45455"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <?= Language::t('header.cart') ?>
        </a>
        <?php
        // Минимальная mobile-кнопка (гамбургер). main.js делает
        // d.querySelector("[data-hamburger-menu-button]")?.addEventListener(...)
        // — без этого ничего страшного, но кнопку всё равно положим для mobile.
        ?>
        <div class="header-mobile">
          <a class="header-mobile__cart" href="<?= Route::to('cart.index') ?>" aria-label="<?= Language::t('header.cart') ?>">
            <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.727051 0.727272H3.63614L5.58523 10.4655C5.65174 10.8003 5.83389 11.1011 6.09981 11.3151C6.36573 11.5292 6.69847 11.6429 7.03978 11.6364H14.1089C14.4502 11.6429 14.7829 11.5292 15.0488 11.3151C15.3148 11.1011 15.4969 10.8003 15.5634 10.4655L16.7271 4.36364H4.36341" stroke="currentColor" stroke-width="1.45455" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <button class="header-mobile__button" type="button" data-hamburger-menu-button aria-label="Menu">
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.45 0.45 0 1 0H13C13.55 0 14 0.45 14 1S13.55 2 13 2H1C0.45 2 0 1.55 0 1ZM0 6C0 5.45 0.45 5 1 5H13C13.55 5 14 5.45 14 6S13.55 7 13 7H1C0.45 7 0 6.55 0 6ZM0 11C0 10.45 0.45 10 1 10H13C13.55 10 14 10.45 14 11S13.55 12 13 12H1C0.45 12 0 11.55 0 11Z" fill="currentColor"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <?php foreach ($menu as $item): ?>
      <?php if (!$item->hasChildren()) continue; ?>
      <div class="header-popup-menu" data-popup-menu="<?= htmlspecialchars($item->code, ENT_QUOTES) ?>">
        <?php foreach ($item->children as $child): ?>
          <a class="header-popup-menu__item" href="<?= htmlspecialchars($child->hasUrl() ? $child->url : '#', ENT_QUOTES) ?>"><?= htmlspecialchars($child->label) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <?php
    // Минимальный hamburger-drawer. main.js делает:
    //   c = document.querySelector(".header-hamburger-menu")
    //   c.querySelector(".header-hamburger-menu-header__close-button")
    // Без этих узлов JS падает с TypeError и handler header-popup-menu не вешается.
    // Полную мобильную навигацию допилим отдельно — пока пустышка, чтобы main.js не ломался.
    ?>
    <div class="header-hamburger-menu">
      <div class="header-hamburger-menu-header">
        <a class="header-hamburger-menu-header__logotype" href="<?= Route::to('home') ?>" aria-label="Gree"></a>
        <button class="header-hamburger-menu-header__close-button" type="button" aria-label="Close">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>
  </header>
