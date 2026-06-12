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

{{-- Header --}}
<div class="flex-between mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-bell" style="color:var(--gold);margin-right:8px;"></i>Order Aktif</p>
        <p class="ph-sub">{{ $orderTerbaru->count() }} order menunggu tindakan</p>
    </div>
    <a href="{{ route('kasir.order') }}" class="btn-primary">
        <i class="fa-solid fa-clipboard-list"></i> Kelola Semua Order
    </a>
</div>

{{-- Order Cards --}}
@forelse($orderTerbaru as $order)
@php
    $ket     = strtolower($order->keterangan ?? '');
    $isQris  = str_contains($ket, 'qris');
    $isKasir = str_contains($ket, 'kasir');
    $isDebit = str_contains($ket, 'debit');
    $total   = $order->detailOrders->sum('sub_total');
    $sudahBayar = $order->transaksi !== null;
@endphp
<div class="order-card" id="ocard-{{ $order->kd_order }}">
    {{-- Header --}}
    <div class="order-card-header">
        <span class="meja-badge">
            <i class="fa-solid fa-chair" style="font-size:10px;margin-right:4px;"></i>Meja {{ $order->no_meja }}
        </span>
        <span class="order-code">
            {{ $order->kd_order }}
            &middot;
            {{ \Carbon\Carbon::parse($order->waktu)->format('H:i') }}
        </span>
        <div style="display:flex;gap:6px;align-items:center;">
            {{-- Badge metode --}}
            @if($isQris)
            <span class="badge" style="background:rgba(30,100,200,.1);color:#1d4ed8;border:1px solid rgba(30,100,200,.2);font-size:10px;">
                <i class="fa-solid fa-qrcode"></i> QRIS
            </span>
            @elseif($isDebit)
            <span class="badge" style="background:rgba(124,58,237,.1);color:#6d28d9;border:1px solid rgba(124,58,237,.2);font-size:10px;">
                <i class="fa-solid fa-credit-card"></i> Debit
            </span>
            @elseif($isKasir)
            <span class="badge" style="background:rgba(26,122,74,.1);color:#1a7a4a;border:1px solid rgba(26,122,74,.2);font-size:10px;">
                <i class="fa-solid fa-money-bill"></i> Cash
            </span>
            @endif

            {{-- Badge status --}}
            @php
                $stColor = match($order->status_order) {
                    'pending'  => ['bg'=>'rgba(37,99,235,.1)', 'border'=>'#2563eb', 'text'=>'#2563eb', 'label'=>'Menunggu'],
                    'diproses' => ['bg'=>'rgba(201,162,39,.12)', 'border'=>'var(--gold)', 'text'=>'var(--gold-dark)', 'label'=>'Diproses'],
                    'siap'     => ['bg'=>'rgba(26,122,74,.1)', 'border'=>'#1a7a4a', 'text'=>'#1a7a4a', 'label'=>'Siap Bayar'],
                    default    => ['bg'=>'#f0f0f0', 'border'=>'#ccc', 'text'=>'#666', 'label'=>ucfirst($order->status_order)],
                };
            @endphp
            <span class="status-badge" style="background:{{ $stColor['bg'] }};border-color:{{ $stColor['border'] }};color:{{ $stColor['text'] }};">
                {{ $stColor['label'] }}
            </span>
        </div>
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
            <span style="font-weight:800;color:var(--gold-dark);">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Footer aksi --}}
    <div class="order-card-footer">
        @if($sudahBayar)
            <a href="{{ route('kasir.struk', $order->transaksi->kd_transaksi) }}" target="_blank"
                class="btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> Cetak Struk
            </a>
        @elseif($order->status_order === 'siap' || $order->status_order === 'pending')
            @if($isQris)
            {{-- QRIS: konfirmasi langsung dari dashboard --}}
            <form method="POST" action="{{ route('kasir.proses-bayar', $order->kd_order) }}" style="margin:0;"
                onsubmit="return confirm('Konfirmasi pembayaran QRIS untuk order ini?')">
                @csrf
                <input type="hidden" name="metode" value="qris">
                <button type="submit" class="btn-gold btn-sm">
                    <i class="fa-solid fa-qrcode"></i> Konfirmasi QRIS
                </button>
            </form>
            @else
            {{-- Kasir: ke halaman detail untuk proses bayar --}}
            <a href="{{ route('kasir.order.detail', $order->kd_order) }}" class="btn-gold btn-sm">
                <i class="fa-solid fa-cash-register"></i> Konfirmasi
            </a>
            @endif
        @elseif($order->status_order === 'diproses')
            <a href="{{ route('kasir.order.detail', $order->kd_order) }}" class="btn-secondary btn-sm">
                <i class="fa-solid fa-eye"></i> Lihat Detail
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