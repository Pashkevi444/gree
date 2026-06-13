{{-- SVG-иконки для типов товара (боковое меню «Кондиционеры» на детальной товара).
     Каждая иконка тематична: настенный блок / напольно-потолочная колонна /
     промышленный модуль с двумя секциями. Маркап взят 1-в-1 из dist/product.html. --}}
@switch($type->value)
    @case('wall')
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="0.5" y="7.24542" width="23" height="8.78073" rx="0.5" stroke="currentColor" />
            <rect y="12" width="24" height="1" fill="currentColor" />
            <rect x="4" y="14" width="16" height="1" rx="0.5" fill="currentColor" />
            <rect x="20" y="11" width="3" height="1" rx="0.5" transform="rotate(-180 20 11)" fill="currentColor" />
            <circle cx="21.5" cy="10.5" r="0.5" fill="currentColor" />
        </svg>
        @break
    @case('column')
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8.5" y="0.5" width="7" height="23" rx="0.5" stroke="currentColor" />
            <rect x="7.25" y="23.25" width="9.5" height="0.5" rx="0.25" stroke="currentColor" stroke-width="0.5" />
            <rect x="10.5" y="2.5" width="3" height="3" rx="0.5" stroke="currentColor" />
            <rect x="10.25" y="9.25" width="3.5" height="0.5" rx="0.25" stroke="currentColor" stroke-width="0.5" />
            <rect x="10" y="11" width="2" height="1" rx="0.5" fill="currentColor" />
            <rect x="13" y="11" width="1" height="1" rx="0.5" fill="currentColor" />
            <rect x="8" y="7" width="8" height="1" fill="currentColor" />
        </svg>
        @break
    @case('industrial')
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="7" height="1" rx="0.5" fill="currentColor" />
            <rect x="15" y="13" width="7" height="1" rx="0.5" fill="currentColor" />
            <rect x="0.5" y="3.0831" width="23" height="15.3132" rx="0.5" stroke="currentColor" />
            <rect x="2.5" y="5.5" width="11" height="11" rx="0.5" stroke="currentColor" />
            <rect x="15.5" y="5.5" width="6" height="11" rx="0.5" stroke="currentColor" />
            <rect x="2.02761" y="19.558" width="3.08897" height="0.754448" rx="0.377224" stroke="currentColor" stroke-width="0.754448" />
            <rect x="18.7405" y="19.558" width="3.08897" height="0.754448" rx="0.377224" stroke="currentColor" stroke-width="0.754448" />
            <circle cx="8" cy="11" r="3.5" stroke="currentColor" />
        </svg>
        @break
@endswitch
