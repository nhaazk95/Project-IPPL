@extends('layouts.app')
@section('title', 'Kelola Order — Dapur Nusantara')
@section('page-title', 'Kelola Order')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Transaksi</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-clipboard-list" style="margin-right:8px;color:var(--gold);"></i>Daftar Order</p>
        <p class="ph-sub">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <div class="search-box">
            <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" id="searchInput" placeholder="Cari order..." oninput="filterCards()">
        </div>
    </div>
</div>

{{-- Filter Tabs --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    @foreach([
        ['semua', 'Semua', 'fa-list'],
        ['pending', 'Pending', 'fa-clock'],
        ['diproses', 'Diproses', 'fa-fire'],
        ['selesai', 'Selesai', 'fa-circle-check'],
    ] as [$val, $label, $icon])
    <a href="{{ route('kasir.order', ['status' => $val]) }}"
        style="
            display:inline-flex;align-items:center;gap:7px;
            padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;
            text-decoration:none;transition:var(--transition);
            background:{{ $status === $val ? 'var(--brown)' : '#fff' }};
            color:{{ $status === $val ? 'var(--gold)' : 'var(--text-mid)' }};
            border:1.5px solid {{ $status === $val ? 'var(--brown)' : 'var(--cream-dark)' }};
        ">
        <i class="fa-solid {{ $icon }}" style="font-size:12px;"></i> {{ $label }}
    </a>
    @endforeach
</div>

{{-- Order Cards --}}
<div id="orderContainer">
@forelse($orders as $order)
<div class="order-card order-item" data-search="{{ strtolower($order->kd_order . $order->no_meja . ($order->nama_user ?? '')) }}">
    <div class="order-card-header">
        <span class="meja-badge"><i class="fa-solid fa-chair" style="font-size:10px;margin-right:3px;"></i>Meja {{ $order->no_meja }}</span>
        <span class="order-code">{{ $order->kd_order }} · {{ \Carbon\Carbon::parse($order->waktu)->format('H:i') }}</span>
        <span class="status-badge" style="
            background:{{ $order->status_order === 'pending'   ? 'rgba(37,99,235,.1)' :
                         ($order->status_order === 'diproses'  ? 'rgba(201,162,39,.12)' :
                          'rgba(26,122,74,.1)') }};
            border-color:{{ $order->status_order === 'pending'  ? '#2563eb' :
                           ($order->status_order === 'diproses' ? 'var(--gold)' :
                            'var(--success)') }};
            color:{{ $order->status_order === 'pending'  ? '#2563eb' :
                    ($order->status_order === 'diproses'  ? 'var(--gold-dark)' :
                     'var(--success)') }};
        ">
            {{ ucfirst($order->status_order) }}
        </span>
    </div>

    <div class="order-card-body">
        @if($order->nama_user)
        <div style="font-size:12px;color:var(--text-light);margin-bottom:8px;">
            <i class="fa-solid fa-user" style="margin-right:5px;"></i>{{ $order->nama_user }}
        </div>
        @endif

        @foreach($order->detailOrders as $item)
        <div class="order-item-row">
            <span>{{ $item->menu->name_menu ?? '-' }}</span>
            <span style="color:var(--text-light);">x{{ $item->total }}</span>
            <span style="font-weight:600;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
        </div>
        @endforeach

        <div class="order-item-total">
            <span>Total</span>
            <span style="font-weight:800;color:var(--gold-dark);">
                Rp {{ number_format($order->detailOrders->sum('sub_total'), 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div class="order-card-footer">
        @if($order->transaksi)
            <a href="{{ route('kasir.struk', $order->transaksi->kd_transaksi) }}" target="_blank"
                class="btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> Struk
            </a>
        @else
            <a href="{{ route('kasir.order.detail', $order->kd_order) }}" class="btn-gold btn-sm">
                <i class="fa-solid fa-cash-register"></i> Proses Bayar
            </a>
        @endif
    </div>
</div>
@empty
    <div class="card" style="padding:48px 24px;text-align:center;">
        <div class="empty-icon">📋</div>
        <p style="font-weight:700;color:var(--brown);margin-bottom:4px;">Tidak ada order</p>
        <p style="color:var(--text-light);font-size:13px;">Tidak ada order dengan status "{{ $status }}" saat ini</p>
    </div>
@endforelse
</div>

<div class="pagination mt-24">{{ $orders->withQueryString()->links('vendor.pagination.simple') }}</div>

@endsection

@push('scripts')
<script>
function filterCards() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.order-item').forEach(el => {
        el.style.display = el.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
@endpush