@extends('layouts.guest')

@section('title', 'Pesanan Saya — Dapur Nusantara')

@section('content')
@push('styles')
<style>
    .pesanan-body { padding: 1rem; }

    .pesanan-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(44,24,16,.07);
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .pesanan-header {
        background: var(--coklat);
        padding: .75rem 1rem;
        display: flex; justify-content: space-between; align-items: center;
    }
    .pesanan-header .kode { color: var(--emas); font-weight: 700; font-size: .85rem; }
    .pesanan-header .waktu { color: rgba(255,255,255,.6); font-size: .75rem; }

    .status-badge {
        padding: .3em .9em; border-radius: 20px;
        font-size: .7rem; font-weight: 700;
    }
    .status-pending  { background: #fff3cd; color: #856404; }
    .status-diproses { background: #cfe2ff; color: #084298; }
    .status-selesai  { background: #d1e7dd; color: #0a3622; }

    .pesanan-body-inner { padding: .85rem 1rem; }
    .pesanan-item { display: flex; justify-content: space-between; margin-bottom: .3rem; font-size: .875rem; }
    .pesanan-item .n { color: var(--teks-muted); }
    .pesanan-item .p { font-weight: 600; color: var(--coklat); }
    .pesanan-total {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: .5rem; padding-top: .5rem;
        border-top: 1.5px dashed #e8ddd0;
    }
    .pesanan-total .label { font-weight: 700; font-size: .88rem; color: var(--coklat); }
    .pesanan-total .amount { font-weight: 800; color: var(--emas); font-size: 1rem; }

    .btn-lihat-nota {
        display: flex; align-items: center; gap: .4rem;
        background: var(--emas); color: var(--coklat);
        border: none; border-radius: 10px;
        padding: .5rem 1rem; font-size: .8rem; font-weight: 700;
        text-decoration: none; margin-top: .75rem;
        cursor: pointer;
    }

    .meja-info {
        font-size: .78rem; color: var(--teks-muted);
        display: flex; align-items: center; gap: .3rem;
        margin-bottom: .6rem;
    }

    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-state i { font-size: 3.5rem; color: var(--emas); display: block; margin-bottom: .75rem; }
</style>
@endpush

{{-- Topbar --}}
<div class="topbar">
    <a href="{{ route('pelanggan.beranda') }}" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
    <span class="topbar-icon"><i class="bi bi-receipt"></i></span>
    <span class="topbar-title">Pesanan Saya</span>
</div>

<div class="pesanan-body">

    @if(session('success'))
        <div class="p-2 mb-2 rounded-3 alert-auto" style="background:#d4edda;color:#155724;font-size:.8rem;">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        </div>
    @endif

    @forelse($orders as $order)
        <div class="pesanan-card">
            {{-- Header --}}
            <div class="pesanan-header">
                <div>
                    <div class="kode">{{ $order->kd_order }}</div>
                    <div class="waktu">{{ \Carbon\Carbon::parse($order->waktu)->format('d/m/Y H:i') }}</div>
                </div>
                @php
                    $sc = match($order->status_order) {
                        'pending' => 'pending', 'diproses' => 'diproses', 'selesai' => 'selesai', default => 'pending'
                    };
                @endphp
                <span class="status-badge status-{{ $sc }} text-capitalize">{{ $order->status_order }}</span>
            </div>

            <div class="pesanan-body-inner">
                <div class="meja-info">
                    <i class="bi bi-grid-3x3-gap"></i> Meja {{ $order->no_meja }}
                </div>

                {{-- Item --}}
                @foreach($order->detailOrders as $item)
                    <div class="pesanan-item">
                        <span class="n">{{ $item->menu->name_menu ?? '-' }} × {{ $item->total }}</span>
                        <span class="p">Rp. {{ number_format($item->sub_total,0,',','.') }}</span>
                    </div>
                @endforeach

                {{-- Total --}}
                <div class="pesanan-total">
                    <span class="label">Total</span>
                    <span class="amount">Rp. {{ number_format($order->detailOrders->sum('sub_total'),0,',','.') }}</span>
                </div>

                {{-- Status info --}}
                @if($order->status_order === 'pending')
                    <div style="background:#fff3cd;border-radius:8px;padding:.5rem .75rem;margin-top:.6rem;font-size:.78rem;color:#856404;">
                        <i class="bi bi-hourglass-split me-1"></i>Pesanan sedang menunggu konfirmasi kasir
                    </div>
                @elseif($order->status_order === 'diproses')
                    <div style="background:#cfe2ff;border-radius:8px;padding:.5rem .75rem;margin-top:.6rem;font-size:.78rem;color:#084298;">
                        <i class="bi bi-fire me-1"></i>Pesanan sedang dimasak, sebentar lagi!
                    </div>
                @elseif($order->status_order === 'selesai')
                    <div style="background:#d1e7dd;border-radius:8px;padding:.5rem .75rem;margin-top:.6rem;font-size:.78rem;color:#0a3622;">
                        <i class="bi bi-check-circle me-1"></i>Pesanan siap! Silakan ke kasir untuk pembayaran
                    </div>
                @endif

                {{-- Tombol --}}
                <a href="{{ route('pelanggan.pembayaran', $order->kd_order) }}" class="btn-lihat-nota">
                    <i class="bi bi-wallet2"></i> Lihat Pembayaran
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-receipt-cutoff"></i>
            <p style="color:var(--teks-muted);">Belum ada pesanan</p>
            <a href="{{ route('pelanggan.menu') }}"
                style="background:var(--emas);color:var(--coklat);border-radius:12px;padding:.75rem 2rem;font-weight:700;text-decoration:none;display:inline-block;">
                Pesan Sekarang
            </a>
        </div>
    @endforelse
</div>
@endsection