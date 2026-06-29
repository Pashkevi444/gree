/**
 * Корзина (/cart/): обработка ± количества через PATCH /api/v1/cart/items/{id}.
 *
 * Конфиг приходит из blade через JSON-блок #cart-page-config:
 *   { "patchTpl": "/api/v1/cart/items/__ID__" }
 *
 * Локализованный постфикс цены читается из data-postfix («UZS» / «UZS dan»),
 * префикс рисуется CSS-правилом ::before (custom.css) и textContent не трогает.
 */
(function () {
    const configEl = document.getElementById('cart-page-config');
    if (!configEl) return;
    let config;
    try {
        config = JSON.parse(configEl.textContent || '{}');
    } catch (e) {
        return;
    }

    const fmt = new Intl.NumberFormat('ru-RU');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    async function api(method, url, body) {
        const opts = {
            method,
            headers: { 'Accept': 'application/json', 'X-CSRF-Token': csrf },
            credentials: 'same-origin',
        };
        if (body !== undefined) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        const r = await fetch(url, opts);
        if (!r.ok) throw new Error('Cart API ' + r.status);
        return r.json();
    }

    const priceText = (el, value) => fmt.format(value) + ' ' + (el.dataset.postfix || 'UZS');

    function repaintSummary(data) {
        document.querySelectorAll('[data-cart-total]').forEach(el => el.textContent = priceText(el, data.total));
        document.querySelectorAll('[data-cart-grand-total]').forEach(el => el.textContent = priceText(el, data.total));
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            const tpl = el.dataset.countTemplate || '__COUNT__';
            el.textContent = tpl.replace('__COUNT__', String(data.count));
        });
        // Бейдж количества в шапке.
        const total = Number(data.count || 0);
        document.querySelectorAll('[data-cart-count-badge]').forEach(el => {
            el.textContent = String(total);
            if (total > 0) { el.removeAttribute('hidden'); } else { el.setAttribute('hidden', ''); }
        });
    }

    function repaintLineTotal(form, line) {
        const el = form.querySelector('[data-line-total]');
        if (el) el.textContent = priceText(el, line.total_price);
        const qty = form.querySelector('[data-cart-qty]');
        if (qty) qty.value = line.quantity;
    }

    // ± кнопки уже обрабатывает фронт main.js (.number-input → valueAsNumber±1
    // → dispatch 'change'). Если повесить свой click — будет двойной инкремент.
    // Слушаем итоговый change на input[data-cart-qty] и шлём PATCH с уже
    // готовым значением.
    document.querySelectorAll('[data-cart-item]').forEach(form => {
        const qtyEl = form.querySelector('[data-cart-qty]');
        if (!qtyEl) return;
        const itemId = parseInt(form.dataset.cartItem, 10);
        let pending = false;
        let lastSent = parseInt(qtyEl.value || '0', 10);

        qtyEl.addEventListener('change', async () => {
            const next = Math.max(0, parseInt(qtyEl.value || '0', 10));
            if (next === lastSent || pending) return;
            pending = true;
            try {
                const patchUrl = config.patchTpl.replace('__ID__', String(itemId));
                const data = await api('PATCH', patchUrl, { quantity: next });
                lastSent = next;
                const line = data.lines.find(l => l.id === itemId);
                if (!line) {
                    form.remove();
                    if (!data.lines.length) location.reload();
                } else {
                    repaintLineTotal(form, line);
                }
                repaintSummary(data);
            } catch (e) {
                console.error(e);
                qtyEl.value = String(lastSent); // откат UI к предыдущему значению
            } finally {
                pending = false;
            }
        });
    });
})();
