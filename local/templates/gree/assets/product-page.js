/**
 * Детальная товара (/catalog/{section}/{code}/): переключение ТП
 * (цена/наличие/характеристики/галерея), доступность комбинаций цвет×мощность,
 * добавление в корзину с counter-ом вместо кнопки.
 *
 * Данные приходят из blade через два JSON-блока:
 *   #product-offers-json — массив offers (OfferDto::toArray() каждого ТП)
 *   #product-page-config — { addUrl, cartGetUrl, itemUrlTemplate, initialGallery }
 */
(function () {
    const dataEl = document.getElementById('product-offers-json');
    const configEl = document.getElementById('product-page-config');
    if (!dataEl || !configEl) return;

    let offers, config;
    try {
        offers = JSON.parse(dataEl.textContent || '[]');
        config = JSON.parse(configEl.textContent || '{}');
    } catch (e) {
        return;
    }
    if (!Array.isArray(offers) || offers.length === 0) return;

    const form = document.getElementById('product-form');
    if (!form) return;

    const priceEl = form.querySelector('.product-price__text');
    const stockEl = form.querySelector('.product-stock');
    const submitBtn = form.querySelector('[data-add-to-cart]');
    const counterEl = form.querySelector('[data-cart-counter]');
    const counterValue = counterEl?.querySelector('input[name="amount"]');
    const specCells = document.querySelectorAll('[data-spec]');
    const fmt = new Intl.NumberFormat('ru-RU');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const addUrl = config.addUrl;
    const cartGetUrl = config.cartGetUrl;
    const itemUrlTemplate = config.itemUrlTemplate;

    function findOffer(color, area) {
        // 1) exact match
        let m = offers.find(o => o.color === color && Number(o.area) === Number(area));
        if (m) return m;
        // 2) same color, any area
        m = offers.find(o => o.color === color);
        if (m) return m;
        // 3) same area, any color
        m = offers.find(o => Number(o.area) === Number(area));
        return m || offers[0];
    }

    const offerIdInput = form.querySelector('[data-offer-id]');

    // Перерисовка слайдеров галереи при смене offer. Фронтовый product.js
    // инициализирует Swiper на DOMContentLoaded (el.swiper). Стратегия:
    //   - кол-во слайдов совпадает → меняем только .src существующих <img>
    //     (Swiper не страдает, навигация работает);
    //   - кол-во изменилось → fallback: innerHTML wrapper-а + swiper.update().
    // lastGallerySig инициализирован server-rendered набором (initialGallery),
    // чтобы первый update() на load не трогал только что отрисованный Swiper.
    const mainCarouselEl = document.querySelector('.product-carousel--main');
    const thumbsCarouselEl = document.querySelector('.product-carousel--thumbs');
    const initialGallery = Array.isArray(config.initialGallery) ? config.initialGallery : [];
    let lastGallerySig = initialGallery.join('|');

    function renderGallery(gallery) {
        const list = (gallery && gallery.length) ? gallery : [];
        if (list.length === 0) return;

        const sig = list.join('|');
        if (sig === lastGallerySig) return;
        lastGallerySig = sig;

        for (const el of [mainCarouselEl, thumbsCarouselEl]) {
            if (!el) continue;
            const wrapper = el.querySelector('.product-carousel-wrapper');
            if (!wrapper) continue;

            const imgs = wrapper.querySelectorAll('.product-carousel__slide');
            if (imgs.length === list.length) {
                imgs.forEach((img, i) => { img.src = list[i]; });
            } else {
                wrapper.innerHTML = list.map(src => `<img class="product-carousel__slide" src="${src}" alt="" />`).join('');
                const sw = el.swiper;
                if (sw) { sw.update(); sw.slideTo(0, 0); }
            }
        }
    }

    function update({ touchGallery = false } = {}) {
        const color = form.querySelector('input[name="color"]:checked')?.value;
        const area = form.querySelector('input[name="area"]:checked')?.value;
        const offer = findOffer(color, area);
        if (!offer) return;

        if (offerIdInput) offerIdInput.value = offer.id;

        if (priceEl) {
            const tpl = priceEl.dataset.priceTemplate || '__PRICE__ UZS';
            priceEl.textContent = tpl.replace('__PRICE__', fmt.format(offer.price));
        }
        if (stockEl) {
            stockEl.style.display = offer.in_stock ? '' : 'none';
        }
        if (submitBtn) {
            submitBtn.disabled = !offer.in_stock;
        }
        specCells.forEach(td => {
            const key = td.dataset.spec;
            if (key in offer && offer[key] !== '' && offer[key] !== null) {
                const tpl = td.dataset.specTemplate;
                td.textContent = tpl ? tpl.replace('__VAL__', String(offer[key])) : String(offer[key]);
            }
        });

        // Слайдер трогаем ТОЛЬКО на пользовательском change: на initial load
        // server-rendered DOM уже совпадает с firstOffer.gallery, фронтовый
        // Swiper уже инициализировался.
        if (touchGallery) {
            renderGallery(offer.gallery);
        }
    }

    // Запрещаем выбор несуществующих комбинаций цвет×мощность.
    // Модель «ведущая/ведомая ось»: цвет — ведущая (все цвета кликабельны
    // всегда), мощность — ведомая (дизейблим area без ТП с текущим цветом;
    // autoswitch если выбранная area стала недоступной). Двунаправленный
    // пересчёт не делаем сознательно: он зацикливается на initial, когда сервер
    // чекнул color из offers[0], а area — первую по сортировке.
    function refreshAvailability() {
        const color = form.querySelector('input[name="color"]:checked')?.value;
        if (!color) return;

        const validAreas = new Set(
            offers.filter(o => o.color === color).map(o => Number(o.area))
        );
        form.querySelectorAll('input[name="area"]').forEach(inp => {
            inp.disabled = !validAreas.has(Number(inp.value));
            inp.closest('label')?.classList.toggle('is-disabled', inp.disabled);
        });

        const checked = form.querySelector('input[name="area"]:checked');
        if (!checked || checked.disabled) {
            const fallback = form.querySelector('input[name="area"]:not(:disabled)');
            if (fallback) fallback.checked = true;
        }
    }

    form.addEventListener('change', e => {
        if (e.target.name === 'color' || e.target.name === 'area') {
            if (e.target.name === 'color') refreshAvailability();
            update({ touchGallery: true });
            syncCart();
        }
    });
    refreshAvailability();
    update();

    // Текущая «строка корзины» для выбранного offer — { id, quantity } или null.
    let currentLine = null;

    function renderCounter() {
        // CSS .number-input { display:flex } перекрывает [hidden] — управляем
        // через style.display напрямую.
        if (currentLine && currentLine.quantity > 0) {
            if (submitBtn) submitBtn.style.display = 'none';
            if (counterEl) counterEl.style.display = '';
            if (counterValue) counterValue.value = String(currentLine.quantity);
        } else {
            if (submitBtn) submitBtn.style.display = '';
            if (counterEl) counterEl.style.display = 'none';
            if (counterValue) counterValue.value = '0';
        }
    }

    function applyLines(lines, offerId) {
        const match = (lines || []).find(l => Number(l.offer_id) === Number(offerId));
        currentLine = match ? { id: Number(match.id), quantity: Number(match.quantity) } : null;
        renderCounter();
        // Бейдж в шапке: сумма quantity всех строк корзины.
        const total = (lines || []).reduce((s, l) => s + Number(l.quantity || 0), 0);
        document.querySelectorAll('[data-cart-count-badge]').forEach(el => {
            el.textContent = String(total);
            if (total > 0) { el.removeAttribute('hidden'); } else { el.setAttribute('hidden', ''); }
        });
    }

    async function fetchJson(url, options = {}) {
        const r = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(options.method && options.method !== 'GET' ? { 'X-CSRF-Token': csrf } : {}),
                ...(options.headers || {}),
            },
            ...options,
        });
        if (!r.ok) throw new Error(`${options.method || 'GET'} ${url} ${r.status}`);
        return r.json();
    }

    async function syncCart() {
        const offerId = parseInt(offerIdInput?.value || '0', 10);
        if (!offerId) return;
        try {
            const data = await fetchJson(cartGetUrl);
            applyLines(data.lines, offerId);
        } catch (err) {
            console.error(err);
        }
    }

    // 1) «Купить» — POST на наш API, без редиректа.
    //
    // Capture-фаза + stopImmediatePropagation — обязательно. Фронтовый
    // product.js навешивает свой submit-handler (bubble), который делает
    // fetch(form.action, {body: FormData}) — а action пустой, POST уходит на
    // текущий URL страницы и возвращает 404.
    //
    // Programmatic form.requestSubmit() от фронтового change-handler даёт
    // submitter=null — игнорируем: за ± отвечает change-handler ниже.
    form.addEventListener('submit', async e => {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (!e.submitter || !('addToCart' in e.submitter.dataset)) return;

        const offerId = parseInt(offerIdInput?.value || '0', 10);
        if (!offerId) return;
        if (submitBtn) submitBtn.disabled = true;
        try {
            const data = await fetchJson(addUrl, {
                method: 'POST',
                body: JSON.stringify({ offer_id: offerId, quantity: 1 }),
            });
            applyLines(data.lines, offerId);
        } catch (err) {
            console.error(err);
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    }, true);

    // 2) «+»/«−» — DOM-инкремент вешает фронтовый main.js (click →
    //    valueAsNumber±1 → dispatchEvent('change')). Мы слушаем только
    //    итоговый change и дёргаем API. Свой click вешать НЕЛЬЗЯ — будет
    //    двойной inc/dec за один клик.
    counterValue?.addEventListener('change', async () => {
        const offerId = parseInt(offerIdInput?.value || '0', 10);
        if (!offerId || !currentLine) return;
        const next = Math.max(0, parseInt(counterValue.value, 10) || 0);
        if (next === currentLine.quantity) return;
        try {
            const data = next <= 0
                ? await fetchJson(itemUrlTemplate + currentLine.id, { method: 'DELETE' })
                : await fetchJson(itemUrlTemplate + currentLine.id, {
                    method: 'PATCH',
                    body: JSON.stringify({ quantity: next }),
                });
            applyLines(data.lines, offerId);
        } catch (err) {
            console.error(err);
            renderCounter(); // сервер отказал — откатываем UI к прежнему quantity
        }
    });

    // 3) Initial sync — если offer уже в корзине (после refresh), сразу counter.
    syncCart();
})();
