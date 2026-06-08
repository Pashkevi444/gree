@php
    use Gree\Helpers\Language;
@endphp
{{-- Попап обратной связи. Триггер — любой <button data-popup="feedback"> на странице (в верстке main.js повешен слушатель на data-popup). --}}
<div class="popup" data-popup="feedback">
    <div class="popup__backdrop"></div>
    <div class="popup-wrapper">
        <div class="popup-content">
            <div class="feedback">
                <form class="feedback-form" action="/api/v1/feedback/catalog-help" method="post" autocomplete="off" data-feedback-form="catalog-help">
                    <div class="feedback-form-header">
                        <div class="feedback-form-header-wrapper">
                            <div class="feedback-form-header__title">{{ Language::t('feedback.title') }}</div>
                            <div class="feedback-form-header__subtitle">{{ Language::t('feedback.subtitle') }}</div>
                        </div>
                        <button class="feedback-form-header__close-button" type="button" data-popup-close>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18" stroke="#282828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 6L18 18" stroke="#282828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="feedback-form-body">
                        <label class="feedback-form-label">
                            <div class="feedback-form-label__title">{{ Language::t('feedback.name') }} <span style="color:#e53935">*</span></div>
                            <input class="feedback-form-label__control form-control" type="text" name="name"
                                   placeholder="{{ Language::t('feedback.name_placeholder') }}" required maxlength="100" />
                        </label>
                        <label class="feedback-form-label">
                            <div class="feedback-form-label__title">{{ Language::t('feedback.phone') }} <span style="color:#e53935">*</span></div>
                            <input class="feedback-form-label__control form-control" type="tel" name="phone"
                                   placeholder="{{ Language::t('feedback.phone_placeholder') }}" required />
                        </label>
                    </div>
                    <button class="feedback-form__button" type="submit">{{ Language::t('feedback.submit') }}</button>
                </form>
                <div class="feedback-alert" style="display: none">
                    <div class="feedback-alert__icon">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M58.6664 29.5465V31.9998C58.6631 37.7503 56.801 43.3456 53.3579 47.9513C49.9148 52.5571 45.0751 55.9264 39.5606 57.5569C34.0462 59.1873 28.1524 58.9915 22.7583 56.9987C17.3642 55.0059 12.7588 51.3227 9.6289 46.4986C6.49905 41.6746 5.01245 35.968 5.39081 30.23C5.76917 24.492 7.99223 19.03 11.7284 14.6587C15.4646 10.2873 20.5138 7.24082 26.1228 5.97352C31.7319 4.70623 37.6004 5.28603 42.853 7.62647"
                                  stroke="#2F40D5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M58.6667 10.6665L32 37.3598L24 29.3598"
                                  stroke="#2F40D5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="feedback-alert__title">{{ Language::t('feedback.success.title') }}</div>
                    <div class="feedback-alert__description">{{ Language::t('feedback.success.description') }}</div>
                    <button class="feedback-alert__button" type="button" data-popup-close>{{ Language::t('feedback.success.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // AJAX-submit feedback-форм. Решает только проблему CSRF: нативный submit
    // и фронтовый product.js (он тоже навешивает submit и шлёт
    // fetch(form.action, {body: FormData})) идут БЕЗ X-CSRF-Token → ApiGuard
    // возвращает 403. Capture-фаза + stopImmediatePropagation глушит фронтовый
    // bubble-handler, наш JSON-fetch уходит уже с заголовком.
    //
    // Маска/валидация телефона — целиком на фронте (HTML required + type="tel" +
    // placeholder, плюс IMask если фронт его подключит). Сами тут не делаем,
    // чтобы не дублировать чужую логику и не расходиться с дизайном.
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

                    // Спрятать форму, показать success-блок (уже есть в DOM).
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
</script>
