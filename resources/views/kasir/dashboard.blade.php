@extends('layouts.app')
@section('title', 'Dashboard Kasir — Dapur Nusantara')
@section('page-title', 'Dashboard Kasir')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Home</a>
    <span class="sep">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid-4 mb-20">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value" style="color:#2563eb;">{{ $orderPending }}</div>
            <div class="stat-label">Order Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-fire"></i></div>
        <div>
            <div class="stat-value">{{ $orderDiproses }}</div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $transaksiHari }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-coins"></i></div>
        <div>
            <div class="stat-value" style="font-size:16px;">Rp {{ number_format($pendapatanHari, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
</div>

{{-- Header Order Section --}}
<div class="flex-between mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-bell" style="color:var(--gold);margin-right:8px;"></i>Order Aktif</p>
        <p class="ph-sub">{{ $orderPending + $orderDiproses }} order menunggu tindakan</p>
    </div>
    <a href="{{ route('kasir.order') }}" class="btn-primary">
        <i class="fa-solid fa-clipboard-list"></i> Kelola Semua Order
    </a>
</div>

{{-- Order Cards --}}
@forelse($orderTerbaru as $order)
<div class="order-card">
    {{-- Header --}}
    <div class="order-card-header">
        <span class="meja-badge"><i class="fa-solid fa-chair" style="font-size:10px;margin-right:4px;"></i>Meja {{ $order->no_meja }}</span>
        <span class="order-code">{{ $order->kd_order }} · {{ \Carbon\Carbon::parse($order->waktu)->format('H:i') }}</span>
        <span class="status-badge" style="
            background:{{ $order->status_order === 'pending' ? 'rgba(37,99,235,.1)' : 'rgba(201,162,39,.12)' }};
            border-color:{{ $order->status_order === 'pending' ? '#2563eb' : 'var(--gold)' }};
            color:{{ $order->status_order === 'pending' ? '#2563eb' : 'var(--gold-dark)' }};
        ">
            {{ $order->status_order === 'pending' ? 'Menunggu' : 'Diproses' }}
        </span>
    </div>

    {{-- Items --}}
    <div class="order-card-body">
        @foreach($order->detailOrders as $item)
            <div class="order-item-row">
                <span>{{ $item->menu->name_menu ?? '-' }}</span>
                <span style="color:var(--text-light);">x{{ $item->total }}</span>
                <span style="font-weight:600;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="order-item-total">
            <span>Total Order</span>
            <span style="font-weight:800;color:var(--gold-dark);">
                Rp {{ number_format($order->detailOrders->sum('sub_total'), 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="order-card-footer">
        @if($order->transaksi)
            <a href="{{ route('kasir.struk', $order->transaksi->kd_transaksi) }}" target="_blank"
                class="btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> Cetak Struk
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
        <div style="font-size:3rem;margin-bottom:12px;">✅</div>
        <p style="font-weight:700;color:var(--brown);font-size:16px;margin-bottom:4px;">Semua beres!</p>
        <p style="color:var(--text-light);font-size:13px;">Tidak ada order aktif saat ini</p>
    </div>
@endforelse

@endsection