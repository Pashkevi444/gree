{{-- Иконки способов оплаты. Inline SVG для карты + img для банковских лого
     (PNG лежат в dist/images, прилетели из верстки). Цвет SVG = currentColor,
     наследует from .payment-label-style. --}}
@switch($method->value)
    @case('card')
        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5007 3.33331H2.50065C1.58018 3.33331 0.833984 4.07951 0.833984 4.99998V15C0.833984 15.9205 1.58018 16.6666 2.50065 16.6666H17.5007C18.4211 16.6666 19.1673 15.9205 19.1673 15V4.99998C19.1673 4.07951 18.4211 3.33331 17.5007 3.33331Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M0.833984 8.33331H19.1673" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('uzum_bank')
        <img src="/dist/images/7826ddb78d82e1266f1f5112272acbf6bc3ede02.png" alt="" />
        @break
    @case('anor_bank')
        <img src="/dist/images/4f180dc3895669a58afed60aac74fb98fb9f5fc8.png" alt="" />
        @break
@endswitch
