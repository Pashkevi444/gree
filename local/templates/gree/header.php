<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

use Gree\Contract\Service\ContactsServiceInterface;
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

// Контакты для социалок шапки. fail-soft: при пустых каналах строки не рендерим.
$contacts = App::get(ContactsServiceInterface::class);
$phoneChannel    = $contacts->findChannelByCode('office');
$telegramChannel = $contacts->findChannelByCode('orders-telegram');
$emailChannel    = $contacts->findChannelByCode('email');
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
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <?php $APPLICATION->ShowHead(); ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
  </head>
  <body>
<header class="header">
    <div class="container">
      <div class="header-top">
        <div class="header-social">
          <?php if ($phoneChannel?->phone): ?>
            <a class="header-social-item" href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $phoneChannel->phone), ENT_QUOTES) ?>">
              <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.9252 10.6134V12.6134C13.926 12.7991 13.888 12.9828 13.8136 13.153C13.7392 13.3231 13.6301 13.4758 13.4933 13.6013C13.3565 13.7268 13.195 13.8224 13.0191 13.8819C12.8432 13.9413 12.6568 13.9634 12.4719 13.9467C10.4205 13.7238 8.44991 13.0228 6.71858 11.9001C5.1078 10.8765 3.74214 9.51084 2.71858 7.90006C1.5919 6.16087 0.890744 4.18073 0.671915 2.12006C0.655255 1.93571 0.677165 1.7499 0.736249 1.57448C0.795332 1.39905 0.890296 1.23785 1.01509 1.10114C1.13989 0.964431 1.29178 0.855202 1.46111 0.78041C1.63043 0.705618 1.81348 0.666903 1.99858 0.666729H3.99858C4.32212 0.663544 4.63578 0.778114 4.88109 0.989084C5.1264 1.20005 5.28663 1.49303 5.33192 1.8134C5.41633 2.45344 5.57288 3.08188 5.79858 3.68673C5.88828 3.92534 5.90769 4.18467 5.85452 4.43398C5.80135 4.68329 5.67782 4.91214 5.49858 5.0934L4.65192 5.94006C5.60095 7.60909 6.98288 8.99103 8.65192 9.94006L9.49858 9.0934C9.67984 8.91415 9.90868 8.79063 10.158 8.73746C10.4073 8.68429 10.6666 8.7037 10.9052 8.7934C11.5101 9.0191 12.1385 9.17565 12.7786 9.26006C13.1024 9.30575 13.3982 9.46887 13.6096 9.71839C13.821 9.96792 13.9334 10.2864 13.9252 10.6134Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <?= htmlspecialchars($phoneChannel->phone) ?>
            </a>
          <?php endif; ?>
          <?php if ($telegramChannel?->buttonUrl): ?>
            <a class="header-social-item" href="<?= htmlspecialchars($telegramChannel->buttonUrl, ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">
              <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_87_1413)">
                  <path d="M14.6668 1.33334L7.3335 8.66667" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14.6668 1.33334L10.0002 14.6667L7.3335 8.66667L1.3335 6L14.6668 1.33334Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_87_1413"><rect width="16" height="16" fill="currentColor" /></clipPath>
                </defs>
              </svg>
              <?= htmlspecialchars($telegramChannel->description ?: $telegramChannel->name) ?>
            </a>
          <?php endif; ?>
          <?php if ($emailChannel?->emailAddress()): ?>
            <a class="header-social-item" href="<?= htmlspecialchars($emailChannel->buttonUrl, ENT_QUOTES) ?>">
              <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_87_1418)">
                  <path d="M8.00016 10.6667C9.47292 10.6667 10.6668 9.47276 10.6668 8C10.6668 6.52724 9.47292 5.33334 8.00016 5.33334C6.5274 5.33334 5.3335 6.52724 5.3335 8C5.3335 9.47276 6.5274 10.6667 8.00016 10.6667Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10.6668 5.33333V8.66666C10.6668 9.19709 10.8775 9.7058 11.2526 10.0809C11.6277 10.4559 12.1364 10.6667 12.6668 10.6667C13.1973 10.6667 13.706 10.4559 14.0811 10.0809C14.4561 9.7058 14.6668 9.19709 14.6668 8.66666V7.99999C14.6667 6.49535 14.1577 5.03498 13.2224 3.85635C12.287 2.67772 10.9805 1.85014 9.51526 1.50819C8.04999 1.16624 6.51212 1.33002 5.15173 1.9729C3.79134 2.61579 2.68843 3.69996 2.02234 5.04914C1.35625 6.39832 1.16615 7.93315 1.48295 9.40407C1.79975 10.875 2.60482 12.1955 3.76726 13.1508C4.92969 14.1062 6.38112 14.6402 7.88555 14.6661C9.38997 14.692 10.8589 14.2082 12.0535 13.2933" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_87_1418"><rect width="16" height="16" fill="currentColor" /></clipPath>
                </defs>
              </svg>
              <?= htmlspecialchars($emailChannel->emailAddress()) ?>
            </a>
          <?php endif; ?>
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
      </div>
      <?php
      // ВАЖНО: .header-mobile должна быть СЕСТРОЙ .header-body (не вложенной).
      // CSS на mobile (max-width:1319px) скрывает .header-body { display:none } —
      // если .header-mobile внутри неё, она тоже исчезнет, и на мобилке вообще
      // нет шапки.
      ?>
      <div class="header-mobile">
        <a class="header-mobile__logotype" href="<?= Route::to('home') ?>" aria-label="Gree">
          <svg width="124" height="24" viewBox="0 0 124 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M28.9335 10.502C29.0082 10.9977 29.0644 11.4921 29.0644 11.9992C29.0644 18.618 22.5243 24.0007 14.5222 24.0007C13.3447 24.0007 12.195 23.8947 11.0972 23.6576C11.5531 22.7091 12.5732 20.5932 14.2275 16.7878C16.6463 11.2215 12.2759 10.9888 12.2759 10.9888L12.3733 10.502H28.9335Z" fill="currentColor"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5227 0C20.9831 0 26.4709 3.50839 28.3542 8.3254H12.6616L6.25111 21.8658C2.48211 19.6805 0 16.0775 0 11.9978C0 5.38587 6.51987 0 14.5227 0ZM123.24 4.87441H111.427C111.427 4.87441 103.457 4.51745 102.453 12.9967C101.449 21.4773 105.766 22.8616 109.949 22.8616H119.241L119.844 20.5124H111.516C111.516 20.5124 107.511 20.1693 108.115 14.4315C108.122 14.3842 108.128 14.3382 108.128 14.3054H120.187L120.842 12.2557H108.458C109.323 8.23837 111.207 6.96001 113.914 6.96001H122.585L123.24 4.87441ZM103.007 4.87441H91.2036C91.2036 4.87441 83.2261 4.51745 82.2238 12.9967C81.2189 21.4773 85.5273 22.8616 89.7181 22.8616H99.0022L99.6055 20.5124H91.282C91.282 20.5124 87.2727 20.1693 87.8747 14.4315C87.8906 14.3913 87.8992 14.3486 87.9 14.3054H99.9596L100.608 12.2557H88.2264C89.0915 8.23837 90.9854 6.96001 93.692 6.96001H102.356L103.007 4.87441ZM62.1462 4.87441H76.3407C76.3407 4.87441 82.1485 4.64737 81.2107 9.43411C80.2558 14.2379 76.2047 14.4201 75.2638 14.4201L79.8802 22.8906H75.6811C75.6811 22.8906 73.4678 23.5925 71.5359 20.023C69.602 16.4465 67.3451 12.2153 67.3451 12.2153H71.5359C71.5359 12.2153 74.5523 12.3011 75.4041 9.96198C76.2503 7.60644 74.5062 7.08425 73.0852 7.08425H67.5727L63.232 22.9877H57.2946L62.1462 4.87441ZM59.0634 4.82395H44.9991C44.9991 4.82395 37.4725 4.68142 35.139 13.2559C35.139 13.2559 32.1358 21.8368 40.9759 22.8591H51.4039C51.4039 22.8591 54.6253 23.4172 55.9735 18.1045C57.3338 12.7886 57.4236 12.1882 57.4236 12.1882H47.2846L46.4947 14.6125H51.2553L50.0329 18.8506C50.0329 18.8506 49.7148 20.3988 48.4456 20.4777C47.1872 20.5786 43.9146 20.4777 43.9146 20.4777C43.9146 20.4777 40.9759 20.6328 40.6926 17.3698C40.4144 14.0941 42.0984 7.34724 47.3719 7.34724H58.2084L59.0634 4.82395Z" fill="currentColor"/>
          </svg>
        </a>
        <div class="header-mobile-buttons">
          <a class="header-mobile__button" href="<?= Route::to('cart.index') ?>" aria-label="<?= Language::t('header.cart') ?>">
            <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.727051 0.727539H3.63614L5.58523 10.4657C5.65174 10.8006 5.83389 11.1013 6.09981 11.3154C6.36573 11.5295 6.69847 11.6432 7.03978 11.6366H14.1089C14.4502 11.6432 14.7829 11.5295 15.0488 11.3154C15.3148 11.1013 15.4969 10.8006 15.5634 10.4657L16.7271 4.3639H4.36341M7.27251 15.273C7.27251 15.6747 6.94689 16.0003 6.54523 16.0003C6.14357 16.0003 5.81796 15.6747 5.81796 15.273C5.81796 14.8713 6.14357 14.5457 6.54523 14.5457C6.94689 14.5457 7.27251 14.8713 7.27251 15.273ZM15.2725 15.273C15.2725 15.6747 14.9469 16.0003 14.5452 16.0003C14.1436 16.0003 13.818 15.6747 13.818 15.273C13.818 14.8713 14.1436 14.5457 14.5452 14.5457C14.9469 14.5457 15.2725 14.8713 15.2725 15.273Z" stroke="white" stroke-width="1.45455" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <button class="header-mobile__button" type="button" data-hamburger-menu-button aria-label="Menu">
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H13C13.2652 0 13.5196 0.105357 13.7071 0.292893C13.8946 0.48043 14 0.734784 14 1C14 1.26522 13.8946 1.51957 13.7071 1.70711C13.5196 1.89464 13.2652 2 13 2H1C0.734784 2 0.48043 1.89464 0.292893 1.70711C0.105357 1.51957 0 1.26522 0 1ZM0 6C0 5.73478 0.105357 5.48043 0.292893 5.29289C0.48043 5.10536 0.734784 5 1 5H13C13.2652 5 13.5196 5.10536 13.7071 5.29289C13.8946 5.48043 14 5.73478 14 6C14 6.26522 13.8946 6.51957 13.7071 6.70711C13.5196 6.89464 13.2652 7 13 7H1C0.734784 7 0.48043 6.89464 0.292893 6.70711C0.105357 6.51957 0 6.26522 0 6ZM0 11C0 10.7348 0.105357 10.4804 0.292893 10.2929C0.48043 10.1054 0.734784 10 1 10H13C13.2652 10 13.5196 10.1054 13.7071 10.2929C13.8946 10.4804 14 10.7348 14 11C14 11.2652 13.8946 11.5196 13.7071 11.7071C13.5196 11.8946 13.2652 12 13 12H1C0.734784 12 0.48043 11.8946 0.292893 11.7071C0.105357 11.5196 0 11.2652 0 11Z" fill="white"/>
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
    // Mobile hamburger drawer.
    //
    // Меню items с children → <button data-drawer="<code>"> открывает sub-drawer
    // (catalog/brand/help). Items без children → <a href> на свою страницу
    // (где купить / партнёрам / контакты).
    //
    // main.js навешивает обработчики на:
    //   - .header-hamburger-menu-header__close-button (закрыть основной drawer)
    //   - [data-drawer="<X>"] (открыть .drawer[data-drawer="<X>"])
    // Без узлов JS падает TypeError — сами drawer-ы лежат после хедера.
    $hamburgerCatalogTag = ($catalogItem !== null && $catalogItem->hasChildren()) ? 'button' : 'a';
    ?>
    <div class="header-hamburger-menu">
      <div class="header-hamburger-menu-header">
        <a class="header-hamburger-menu-header__logotype" href="<?= Route::to('home') ?>" aria-label="Gree">
          <svg width="155" height="30" viewBox="0 0 155 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M36.1692 13.126C36.2624 13.7456 36.3328 14.3637 36.3328 14.9975C36.3328 23.2713 28.1574 29.9999 18.1544 29.9999C16.6825 29.9999 15.2454 29.8674 13.873 29.571C14.443 28.3853 15.7181 25.7404 17.786 20.9835C20.8097 14.0255 15.3465 13.7346 15.3465 13.7346L15.4683 13.126H36.1692Z" fill="white"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1539 0C26.2297 0 33.0896 4.38561 35.4438 10.4071H15.8274L7.81412 27.333C3.10272 24.6014 0 20.0975 0 14.9976C0 6.73254 8.15008 0 18.1539 0ZM154.054 6.09318H139.287C139.287 6.09318 129.325 5.64698 128.07 16.2464C126.815 26.8474 132.211 28.5778 137.441 28.5778H149.056L149.809 25.6412H139.4C139.4 25.6412 134.393 25.2123 135.148 18.0399C135.156 17.9808 135.164 17.9232 135.164 17.8822H150.238L151.056 15.3201H135.577C136.658 10.2983 139.012 8.70027 142.397 8.70027H153.236L154.054 6.09318ZM128.763 6.09318H114.008C114.008 6.09318 104.036 5.64698 102.783 16.2464C101.527 26.8474 106.912 28.5778 112.151 28.5778H123.756L124.51 25.6412H114.106C114.106 25.6412 109.094 25.2123 109.847 18.0399C109.866 17.9896 109.877 17.9362 109.878 17.8822H124.953L125.764 15.3201H110.286C111.368 10.2983 113.735 8.70027 117.118 8.70027H127.949L128.763 6.09318ZM77.685 6.09318H95.4286C95.4286 6.09318 102.689 5.80938 101.516 11.793C100.323 17.7979 95.2587 18.0257 94.0824 18.0257L99.8531 28.6141H94.6042C94.6042 28.6141 91.8374 29.4915 89.4224 25.0294C87.0051 20.5587 84.1838 15.2696 84.1838 15.2696H89.4224C89.4224 15.2696 93.1931 15.3768 94.2579 12.4528C95.3156 9.50833 93.1354 8.85557 91.3591 8.85557H84.4683L79.0423 28.7355H71.6203L77.685 6.09318ZM73.8313 6.03012H56.2506C56.2506 6.03012 46.842 5.85195 43.9251 16.5704C43.9251 16.5704 40.171 27.2967 51.2214 28.5747H64.2568C64.2568 28.5747 68.2836 29.2724 69.9689 22.6313C71.6693 15.9862 71.7816 15.2357 71.7816 15.2357H59.1074L58.1201 18.2661H64.071L62.543 23.5639C62.543 23.5639 62.1453 25.4993 60.5588 25.5978C58.9857 25.724 54.8949 25.5978 54.8949 25.5978C54.8949 25.5978 51.2214 25.7918 50.8672 21.7128C50.5194 17.6181 52.6245 9.18432 59.2165 9.18432H72.7626L73.8313 6.03012Z" fill="white"/>
          </svg>
        </a>
        <button class="header-hamburger-menu-header__close-button" type="button" aria-label="Close">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
      <?php if ($catalogItem !== null): ?>
        <?php if ($catalogItem->hasChildren()): ?>
          <button class="header-hamburger-menu__catalog-button" type="button" data-drawer="catalog">
        <?php else: ?>
          <a class="header-hamburger-menu__catalog-button" href="<?= htmlspecialchars($catalogItem->url, ENT_QUOTES) ?>">
        <?php endif; ?>
          <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.45 0.45 0 1 0H13C13.55 0 14 0.45 14 1S13.55 2 13 2H1C0.45 2 0 1.55 0 1ZM0 6C0 5.45 0.45 5 1 5H13C13.55 5 14 5.45 14 6S13.55 7 13 7H1C0.45 7 0 6.55 0 6ZM0 11C0 10.45 0.45 10 1 10H13C13.55 10 14 10.45 14 11S13.55 12 13 12H1C0.45 12 0 11.55 0 11Z" fill="currentColor"/>
          </svg>
          <?= htmlspecialchars($catalogItem->label) ?>
        <?= $catalogItem->hasChildren() ? '</button>' : '</a>' ?>
      <?php endif; ?>
      <div class="header-hamburger-menu-navigation">
        <?php foreach ($navItems as $item): ?>
          <?php if ($item->hasChildren()): ?>
            <button class="header-hamburger-menu-navigation__button" type="button" data-drawer="<?= htmlspecialchars($item->code, ENT_QUOTES) ?>"><?= htmlspecialchars($item->label) ?></button>
          <?php else: ?>
            <a class="header-hamburger-menu-navigation__button" href="<?= htmlspecialchars($item->hasUrl() ? $item->url : '#', ENT_QUOTES) ?>"><?= htmlspecialchars($item->label) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php
        // Кнопка «Язык сайта» — открывает language-drawer (см. ниже). Внутри
        // показываем флаг текущей локали + полное название. Скрипт ниже скрывает
        // svg других локалей через data-id (тот же приём что и top-bar select).
        ?>
        <button class="header-hamburger-menu-navigation__button header-hamburger-menu-navigation__button--language" type="button" data-drawer="language">
          <?= Language::t('language.drawer.title') ?>
          <span class="header-hamburger-menu-navigation-language">
            <span class="header-hamburger-menu-navigation-language__icon">
              <?php foreach (Locale::cases() as $loc): ?>
                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-id="<?= $loc->value ?>"<?= $loc === $currentLocale ? '' : ' style="display:none"' ?>>
                  <?php if ($loc === Locale::Ru): ?>
                    <g clip-path="url(#clip_lang_ru_h)"><path d="M0 0H18V6.00117H0V0Z" fill="white"/><path d="M0 6.00098H18V11.9986H0V6.00098Z" fill="#729AE6"/><path d="M0 11.999H18V18.0002H0V11.999Z" fill="#C64F45"/></g>
                    <defs><clipPath id="clip_lang_ru_h"><rect width="18" height="18" fill="white"/></clipPath></defs>
                  <?php else: ?>
                    <g clip-path="url(#clip_lang_uz_h)"><path d="M0 0H18V6H0V0Z" fill="#1EB53A"/><path d="M0 6H18V7H0V6Z" fill="#CE1126"/><path d="M0 7H18V11H0V7Z" fill="white"/><path d="M0 11H18V12H0V11Z" fill="#CE1126"/><path d="M0 12H18V18H0V12Z" fill="#0099B5"/></g>
                    <defs><clipPath id="clip_lang_uz_h"><rect width="18" height="18" fill="white"/></clipPath></defs>
                  <?php endif; ?>
                </svg>
              <?php endforeach; ?>
            </span>
            <span class="header-hamburger-menu-navigation-language__text"><?= Language::t('language.full.' . $currentLocale->value) ?></span>
          </span>
        </button>
      </div>
      <div class="header-hamburger-menu-contacts">
        <?php if ($phoneChannel?->phone): ?>
          <a class="header-hamburger-menu-contacts__item" href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $phoneChannel->phone), ENT_QUOTES) ?>">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.9248 10.6137V12.6137C13.9255 12.7994 13.8875 12.9832 13.8131 13.1533C13.7387 13.3234 13.6296 13.4761 13.4928 13.6016C13.356 13.7272 13.1945 13.8227 13.0186 13.8822C12.8427 13.9417 12.6563 13.9638 12.4714 13.9471C10.42 13.7242 8.44943 13.0232 6.71809 11.9004C5.10731 10.8768 3.74165 9.51117 2.71809 7.90039C1.59141 6.16119 0.890255 4.18105 0.671427 2.12039C0.654767 1.93603 0.676677 1.75023 0.73576 1.57481C0.794844 1.39938 0.889807 1.23818 1.0146 1.10147C1.1394 0.964759 1.2913 0.85553 1.46062 0.780738C1.62994 0.705947 1.81299 0.667231 1.99809 0.667057H3.99809C4.32163 0.663873 4.63529 0.778443 4.8806 0.989412C5.12591 1.20038 5.28614 1.49335 5.33143 1.81372C5.41584 2.45377 5.57239 3.08221 5.79809 3.68706C5.88779 3.92567 5.9072 4.185 5.85403 4.43431C5.80086 4.68362 5.67734 4.91246 5.49809 5.09372L4.65143 5.94039C5.60046 7.60942 6.9824 8.99135 8.65143 9.94039L9.49809 9.09372C9.67935 8.91448 9.9082 8.79096 10.1575 8.73779C10.4068 8.68462 10.6661 8.70403 10.9048 8.79372C11.5096 9.01942 12.1381 9.17598 12.7781 9.26039C13.1019 9.30608 13.3977 9.46919 13.6091 9.71872C13.8205 9.96825 13.9329 10.2868 13.9248 10.6137Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?= htmlspecialchars($phoneChannel->phone) ?>
          </a>
        <?php endif; ?>
        <?php if ($telegramChannel?->buttonUrl): ?>
          <a class="header-hamburger-menu-contacts__item" href="<?= htmlspecialchars($telegramChannel->buttonUrl, ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14.6673 1.33301L7.33398 8.66634" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14.6673 1.33301L10.0007 14.6663L7.33398 8.66634L1.33398 5.99967L14.6673 1.33301Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?= htmlspecialchars($telegramChannel->description ?: $telegramChannel->name) ?>
          </a>
        <?php endif; ?>
        <?php if ($emailChannel?->emailAddress()): ?>
          <a class="header-hamburger-menu-contacts__item" href="<?= htmlspecialchars($emailChannel->buttonUrl, ENT_QUOTES) ?>">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.00065 10.6663C9.47341 10.6663 10.6673 9.47243 10.6673 7.99967C10.6673 6.52692 9.47341 5.33301 8.00065 5.33301C6.52789 5.33301 5.33398 6.52692 5.33398 7.99967C5.33398 9.47243 6.52789 10.6663 8.00065 10.6663Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10.6673 5.33357V8.6669C10.6673 9.19734 10.878 9.70605 11.2531 10.0811C11.6282 10.4562 12.1369 10.6669 12.6673 10.6669C13.1978 10.6669 13.7065 10.4562 14.0815 10.0811C14.4566 9.70605 14.6673 9.19734 14.6673 8.6669V8.00024C14.6672 6.49559 14.1581 5.03522 13.2228 3.85659C12.2875 2.67796 10.981 1.85039 9.51575 1.50844C8.05048 1.16648 6.51261 1.33027 5.15222 1.97315C3.79183 2.61603 2.68892 3.70021 2.02283 5.04939C1.35674 6.39856 1.16663 7.9334 1.48344 9.40431C1.80024 10.8752 2.60531 12.1957 3.76774 13.1511C4.93018 14.1064 6.38161 14.6405 7.88604 14.6663C9.39046 14.6922 10.8594 14.2084 12.054 13.2936" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?= htmlspecialchars($emailChannel->emailAddress()) ?>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php
    // Sub-drawer-ы. Открываются нажатием на [data-drawer="<X>"] в основном
    // hamburger-меню. Структура drawer-а — стандартная (drawer/wrapper/header/content).
    // Контент — список ссылок на разделы (header-drawer-links__item).
    ?>
    <?php if ($catalogItem !== null && $catalogItem->hasChildren()): ?>
      <div class="drawer" data-drawer="catalog">
        <div class="drawer__backdrop"></div>
        <div class="drawer-wrapper">
          <div class="drawer-header">
            <div class="drawer-header__title"><?= htmlspecialchars($catalogItem->label) ?></div>
          </div>
          <div class="drawer-content">
            <div class="header-drawer-links">
              <?php foreach ($catalogItem->children as $child): ?>
                <a class="header-drawer-links__item" href="<?= htmlspecialchars($child->hasUrl() ? $child->url : '#', ENT_QUOTES) ?>"><?= htmlspecialchars($child->label) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php foreach ($navItems as $item): ?>
      <?php if (!$item->hasChildren()) continue; ?>
      <div class="drawer" data-drawer="<?= htmlspecialchars($item->code, ENT_QUOTES) ?>">
        <div class="drawer__backdrop"></div>
        <div class="drawer-wrapper">
          <div class="drawer-header">
            <div class="drawer-header__title"><?= htmlspecialchars($item->label) ?></div>
          </div>
          <div class="drawer-content">
            <div class="header-drawer-links">
              <?php foreach ($item->children as $child): ?>
                <a class="header-drawer-links__item" href="<?= htmlspecialchars($child->hasUrl() ? $child->url : '#', ENT_QUOTES) ?>"><?= htmlspecialchars($child->label) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="drawer" data-drawer="language">
      <div class="drawer__backdrop"></div>
      <div class="drawer-wrapper">
        <div class="drawer-header">
          <div class="drawer-header__title"><?= Language::t('language.drawer.title') ?></div>
        </div>
        <div class="drawer-content">
          <div class="header-drawer-language">
            <?php foreach (Locale::cases() as $loc): ?>
              <a class="header-drawer-language-button<?= $loc === $currentLocale ? ' header-drawer-language-button--active' : '' ?>" href="<?= Route::to('lang.switch', ['locale' => $loc->value]) ?>" data-id="<?= $loc->value ?>">
                <span class="header-drawer-language-button__icon">
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <?php if ($loc === Locale::Ru): ?>
                      <g clip-path="url(#clip_drw_ru)"><path d="M0 0H18V6.00117H0V0Z" fill="white"/><path d="M0 6.00098H18V11.9986H0V6.00098Z" fill="#729AE6"/><path d="M0 11.999H18V18.0002H0V11.999Z" fill="#C64F45"/></g>
                      <defs><clipPath id="clip_drw_ru"><rect width="18" height="18" fill="white"/></clipPath></defs>
                    <?php else: ?>
                      <g clip-path="url(#clip_drw_uz)"><path d="M0 0H18V6H0V0Z" fill="#1EB53A"/><path d="M0 6H18V7H0V6Z" fill="#CE1126"/><path d="M0 7H18V11H0V7Z" fill="white"/><path d="M0 11H18V12H0V11Z" fill="#CE1126"/><path d="M0 12H18V18H0V12Z" fill="#0099B5"/></g>
                      <defs><clipPath id="clip_drw_uz"><rect width="18" height="18" fill="white"/></clipPath></defs>
                    <?php endif; ?>
                  </svg>
                </span>
                <span class="header-drawer-language-button__text"><?= Language::t('language.full.' . $loc->value) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </header>
