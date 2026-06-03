<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

use Gree\Contract\Service\MenuServiceInterface;
use Gree\Core\App;
use Gree\Helpers\Language;
use Gree\Helpers\Route;

$footerMenu = App::get(MenuServiceInterface::class)->getFooterMenu();
?>
<footer class="footer container">
      <div class="footer-columns">
        <div class="footer-column">
          <a class="footer__logotype" href="<?= Route::to('home') ?>">
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
          <div class="footer__description">
            <?= Language::t('footer.description') ?>
          </div>
          <div class="footer-social">
            <div class="footer-social__numbers">
              <a href="tel:+998 71 500 00 00">+998 71 500 00 00</a>
              •
              <a href="tel:+998 91 772 72 72">+998 91 772 72 72</a>
            </div>
            <div class="footer-social-items">
              <a class="footer-social__item" href="https://t.me/username" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24Z"
                    fill="url(#paint0_linear_87_1351)"
                  />
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M5.43201 11.8735C8.93026 10.3493 11.263 9.34452 12.4301 8.85905C15.7627 7.47294 16.4551 7.23216 16.9065 7.22421C17.0058 7.22246 17.2277 7.24706 17.3715 7.36372C17.4929 7.46223 17.5263 7.5953 17.5423 7.6887C17.5583 7.78209 17.5782 7.99485 17.5623 8.1611C17.3817 10.0586 16.6003 14.6633 16.2028 16.7885C16.0346 17.6877 15.7034 17.9893 15.3827 18.0188C14.6858 18.0829 14.1567 17.5582 13.4817 17.1158C12.4256 16.4235 11.8289 15.9925 10.8037 15.3169C9.61896 14.5362 10.387 14.1071 11.0622 13.4058C11.2389 13.2222 14.3093 10.4295 14.3687 10.1761C14.3762 10.1444 14.3831 10.0263 14.3129 9.96397C14.2427 9.90161 14.1392 9.92293 14.0644 9.93989C13.9585 9.96394 12.2713 11.0791 9.00276 13.2855C8.52385 13.6143 8.09007 13.7746 7.70141 13.7662C7.27295 13.7569 6.44876 13.5239 5.83606 13.3247C5.08456 13.0805 4.48728 12.9513 4.53929 12.5364C4.56638 12.3203 4.86395 12.0993 5.43201 11.8735Z"
                    fill="white"
                  />
                  <defs>
                    <linearGradient
                      id="paint0_linear_87_1351"
                      x1="12"
                      y1="0"
                      x2="12"
                      y2="23.822"
                      gradientUnits="userSpaceOnUse"
                    >
                      <stop stop-color="#2AABEE" />
                      <stop offset="1" stop-color="#229ED9" />
                    </linearGradient>
                  </defs>
                </svg>
              </a>
              <a class="footer-social__item" href="mailto:mailto:example@example.com">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24Z"
                    fill="white"
                  />
                  <path
                    d="M12.0002 14.6668C13.4729 14.6668 14.6668 13.4729 14.6668 12.0002C14.6668 10.5274 13.4729 9.3335 12.0002 9.3335C10.5274 9.3335 9.3335 10.5274 9.3335 12.0002C9.3335 13.4729 10.5274 14.6668 12.0002 14.6668Z"
                    stroke="black"
                    stroke-width="1.33333"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M14.6668 9.33357V12.6669C14.6668 13.1973 14.8775 13.706 15.2526 14.0811C15.6277 14.4562 16.1364 14.6669 16.6668 14.6669C17.1973 14.6669 17.706 14.4562 18.0811 14.0811C18.4561 13.706 18.6668 13.1973 18.6668 12.6669V12.0002C18.6667 10.4956 18.1577 9.03522 17.2224 7.85659C16.287 6.67796 14.9805 5.85039 13.5153 5.50844C12.05 5.16648 10.5121 5.33027 9.15173 5.97315C7.79134 6.61603 6.68843 7.70021 6.02234 9.04939C5.35625 10.3986 5.16615 11.9334 5.48295 13.4043C5.79975 14.8752 6.60482 16.1957 7.76726 17.1511C8.92969 18.1064 10.3811 18.6405 11.8855 18.6663C13.39 18.6922 14.8589 18.2084 16.0535 17.2936"
                    stroke="black"
                    stroke-width="1.33333"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>
        <nav class="footer-column">
          <?php foreach ($footerMenu as $column): ?>
            <div class="footer-navigation">
              <div class="footer-navigation__title"><?= htmlspecialchars($column->label) ?></div>
              <?php if ($column->hasChildren()): ?>
                <div class="footer-navigation-items">
                  <?php foreach ($column->children as $item): ?>
                    <?php if ($item->hasUrl()): ?>
                      <a class="footer-navigation__item" href="<?= htmlspecialchars($item->url, ENT_QUOTES) ?>"><?= htmlspecialchars($item->label) ?></a>
                    <?php else: ?>
                      <span class="footer-navigation__item"><?= htmlspecialchars($item->label) ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </nav>
      </div>
      <iframe
        id="footer-map"
        class="footer__map"
        data-default-src="https://yandex.ru/map-widget/v1/org/magazin_konditsionerov_gree/162570630328/?ll=64.399900%2C39.784117&utm_source=share&z=17"
        src="https://yandex.ru/map-widget/v1/org/magazin_konditsionerov_gree/162570630328/?ll=64.399900%2C39.784117&utm_source=share&z=17"
      ></iframe>
    </footer>
    <script>
        // Глобальный обработчик «Показать на карте». Любая кнопка с
        // data-show-on-map="lat,lon" скроллит к iframe карты в футере и
        // меняет его src на yandex map-widget URL с pin'ом на этих координатах.
        // Используется на странице /contacts/ для каналов и адресов.
        (function () {
            const iframe = document.getElementById('footer-map');
            if (!iframe) return;
            document.addEventListener('click', function (ev) {
                const btn = ev.target.closest('[data-show-on-map]');
                if (!btn) return;
                const raw = (btn.dataset.showOnMap || '').trim();
                const [lat, lon] = raw.split(',').map(s => s.trim());
                if (!lat || !lon) return;
                ev.preventDefault();
                const ll = `${lon},${lat}`;
                const pt = `${lon},${lat},pm2rdm`;
                iframe.src = `https://yandex.ru/map-widget/v1/?ll=${ll}&z=17&pt=${pt}`;
                iframe.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        })();
    </script>
  </body>
</html>
