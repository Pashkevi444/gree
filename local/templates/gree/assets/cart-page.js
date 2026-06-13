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
    }

    function repaintLineTotal(form, line) {
        const el = form.querySelector('[data-line-total]');
        if (el) el.textContent = priceText(el, line.total_price);
        const qty = form.querySelector('[data-cart-qty]');
        if (qty) qty.value = line.quantity;
    }

    document.querySelectorAll('[data-cart-step]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const form = btn.closest('[data-cart-item]');
            if (!form) return;
            const step = parseInt(btn.dataset.cartStep || '0', 10);
            const qtyEl = form.querySelector('[data-cart-qty]');
            const current = parseInt(qtyEl.value || '0', 10);
            const next = current + step;
            const itemId = parseInt(form.dataset.cartItem, 10);

            btn.disabled = true;
            try {
                const patchUrl = config.patchTpl.replace('__ID__', String(itemId));
                const data = await api('PATCH', patchUrl, { quantity: next });
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
            } finally {
                btn.disabled = false;
            }
        });
    });
})();
