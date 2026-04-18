@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;gap:8px;justify-content:center;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;
            font-size:13px;font-weight:600;border:1.5px solid var(--cream-dark);background:#fff;
            color:var(--cream-mid);cursor:not-allowed;user-select:none;">
            <i class="fa-solid fa-chevron-left" style="font-size:11px;"></i> Previous
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;
            font-size:13px;font-weight:600;border:1.5px solid var(--cream-dark);background:#fff;
            color:var(--text-mid);text-decoration:none;transition:all .18s;"
            onmouseover="this.style.background='var(--brown)';this.style.color='var(--gold)';this.style.borderColor='var(--brown)';"
            onmouseout="this.style.background='#fff';this.style.color='var(--text-mid)';this.style.borderColor='var(--cream-dark)';">
            <i class="fa-solid fa-chevron-left" style="font-size:11px;"></i> Previous
        </a>
    @endif

    {{-- Current page --}}
    <span style="display:inline-flex;align-items:center;justify-content:center;
        min-width:40px;height:40px;padding:0 14px;border-radius:10px;
        font-size:13px;font-weight:700;
        background:var(--brown);color:var(--gold);
        border:1.5px solid var(--brown);">
        {{ $paginator->currentPage() }}
    </span>

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;
            font-size:13px;font-weight:600;border:1.5px solid var(--cream-dark);background:#fff;
            color:var(--text-mid);text-decoration:none;transition:all .18s;"
            onmouseover="this.style.background='var(--brown)';this.style.color='var(--gold)';this.style.borderColor='var(--brown)';"
            onmouseout="this.style.background='#fff';this.style.color='var(--text-mid)';this.style.borderColor='var(--cream-dark)';">
            Next <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
        </a>
    @else
        <span style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;
            font-size:13px;font-weight:600;border:1.5px solid var(--cream-dark);background:#fff;
            color:var(--cream-mid);cursor:not-allowed;user-select:none;">
            Next <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
        </span>
    @endif
</nav>
@endif