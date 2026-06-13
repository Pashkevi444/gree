/**
 * AJAX-submit feedback-форм (partials/feedback-popup.blade.php).
 *
 * Решает только проблему CSRF: нативный submit и фронтовый product.js (он тоже
 * навешивает submit и шлёт fetch(form.action, {body: FormData})) идут БЕЗ
 * X-CSRF-Token → ApiGuard возвращает 403. Capture-фаза + stopImmediatePropagation
 * глушит фронтовый bubble-handler, наш JSON-fetch уходит уже с заголовком.
 *
 * Маска/валидация телефона — целиком на фронте (HTML required + type="tel" +
 * placeholder). Сами тут не делаем, чтобы не дублировать чужую логику.
 */
(function () {
    document.querySelectorAll('form.feedback-form').forEach(function (form) {
        if (form.dataset.feedbackBound) return;
        form.dataset.feedbackBound = '1';

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const payload = Object.fromEntries(new FormData(form).entries());
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            try {
                const r = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-Token': csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                if (!r.ok) throw new Error('feedback ' + r.status);

                const alert = form.parentElement?.querySelector('.feedback-alert');
                form.style.display = 'none';
                if (alert) alert.style.display = '';
                form.reset();
            } catch (err) {
                console.error(err);
                if (submitBtn) submitBtn.disabled = false;
            }
        }, true);
    });
})();
