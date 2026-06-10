/**
 * Оформление заказа (/order/): AJAX-submit формы на POST /api/v1/order/
 * с CSRF; обработка 422 (валидация / пустая корзина) и сетевых ошибок.
 *
 * Конфиг приходит из blade через JSON-блок #checkout-page-config:
 *   {
 *     "apiUrl": "...", "cartUrl": "...",
 *     "text": { "invalid": "...", "network": "...", "empty": "..." }
 *   }
 */
(function () {
    const configEl = document.getElementById('checkout-page-config');
    if (!configEl) return;
    let config;
    try {
        config = JSON.parse(configEl.textContent || '{}');
    } catch (e) {
        return;
    }

    const form = document.getElementById('order-form');
    const submit = document.getElementById('order-submit');
    const errorBox = document.getElementById('order-error');
    if (!form || !submit || !errorBox) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const text = config.text || {};

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.style.display = '';
    }
    function clearError() {
        errorBox.textContent = '';
        errorBox.style.display = 'none';
    }
    function highlightFields(fields) {
        form.querySelectorAll('.form-control--error').forEach(el => el.classList.remove('form-control--error'));
        for (const code of Object.keys(fields || {})) {
            const el = form.querySelector(`[name="${code}"]`);
            if (el) el.classList.add('form-control--error');
        }
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearError();
        if (!form.reportValidity()) return;

        submit.disabled = true;
        try {
            const data = Object.fromEntries(new FormData(form));
            const r = await fetch(config.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify(data),
            });

            if (r.ok) {
                const body = await r.json();
                location.href = body.redirect_url;
                return;
            }

            if (r.status === 422) {
                const body = await r.json().catch(() => ({}));
                if (body.error === 'empty_cart') {
                    showError(text.empty);
                    setTimeout(() => { location.href = config.cartUrl; }, 1500);
                } else {
                    highlightFields(body.fields || {});
                    showError(text.invalid);
                }
                return;
            }

            showError(text.network + ' (' + r.status + ')');
        } catch (err) {
            console.error(err);
            showError(text.network);
        } finally {
            submit.disabled = false;
        }
    });
})();
