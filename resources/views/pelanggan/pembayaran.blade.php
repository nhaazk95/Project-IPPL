@extends('layouts.guest')

@section('title', 'Pembayaran — Dapur Nusantara')

@push('styles')
<style>
    :root {
        --coklat: #2c1810;
        --emas: #c9a227;
        --emas-light: #d4b040;
        --krem: #faf5ee;
        --krem-dark: #f0e8d8;
        --teks-muted: #7a6552;
    }

    body { background: var(--krem); }

    .pembayaran-body { padding: 1rem; padding-bottom: 2rem; }

    /* ── Ringkasan Pesanan ── */
    .ringkasan-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,24,16,.08);
        padding: 1rem 1.1rem;
        margin-bottom: 1.25rem;
    }
    .ringkasan-title {
        font-weight: 700;
        font-size: .9rem;
        color: var(--coklat);
        margin-bottom: .75rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .ringkasan-item {
        display: flex;
        justify-content: space-between;
        font-size: .875rem;
        color: var(--teks-muted);
        margin-bottom: .35rem;
    }
    .ringkasan-item .nama { color: var(--coklat); }
    .ringkasan-item .harga { font-weight: 600; color: var(--coklat); }
    .ringkasan-divider {
        border: none;
        border-top: 1.5px dashed #e0d5c5;
        margin: .75rem 0;
    }
    .ringkasan-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ringkasan-total .label { font-weight: 700; font-size: .9rem; color: var(--coklat); }
    .ringkasan-total .amount { font-weight: 800; font-size: 1.05rem; color: var(--emas); }

    /* ── Pilih Metode ── */
    .metode-title {
        font-weight: 700;
        font-size: .9rem;
        color: var(--coklat);
        margin-bottom: .75rem;
    }
    .metode-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(44,24,16,.07);
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.1rem;
        margin-bottom: .75rem;
        cursor: pointer;
        text-decoration: none;
        transition: box-shadow .2s, transform .15s;
        border: 2px solid transparent;
    }
    .metode-card:hover {
        box-shadow: 0 4px 16px rgba(201,162,39,.18);
        border-color: var(--emas);
        transform: translateY(-1px);
    }
    .metode-icon {
        width: 52px; height: 52px;
        background: var(--krem-dark);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        color: var(--emas);
        flex-shrink: 0;
    }
    .metode-info { flex-grow: 1; }
    .metode-info .nama { font-weight: 700; font-size: .9rem; color: var(--coklat); }
    .metode-info .desc { font-size: .78rem; color: var(--teks-muted); margin-top: 2px; }
    .metode-arrow { color: var(--teks-muted); font-size: 1rem; }

    /* ── Detail QRIS ── */
    .qris-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,24,16,.08);
        padding: 1.25rem 1.1rem;
        margin-bottom: 1rem;
        text-align: center;
    }
    .qris-card .judul { font-weight: 700; font-size: .95rem; color: var(--coklat); margin-bottom: .25rem; }
    .qris-card .sub   { font-size: .8rem; color: var(--teks-muted); margin-bottom: 1rem; }
    .qris-img-wrap {
        display: inline-block;
        background: var(--krem-dark);
        border-radius: 12px;
        padding: .75rem;
        margin-bottom: .75rem;
    }
    .qris-img-wrap img { width: 160px; height: 160px; display: block; }
    .qris-nominal { font-size: 1.4rem; font-weight: 800; color: var(--emas); margin-bottom: .2rem; }
    .qris-info { font-size: .78rem; color: var(--teks-muted); margin-bottom: 1rem; }
    .qris-steps {
        background: var(--krem-dark);
        border-radius: 10px;
        padding: .75rem 1rem;
        text-align: left;
        font-size: .8rem;
        color: var(--teks-muted);
        line-height: 1.8;
    }

    /* ── Detail Kasir ── */
    .kasir-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,24,16,.08);
        padding: 1.25rem 1.1rem;
        margin-bottom: 1rem;
        text-align: center;
    }
    .kasir-card .judul { font-weight: 700; font-size: .95rem; color: var(--coklat); margin-bottom: .75rem; }
    .kasir-banner {
        background: var(--emas);
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: .83rem;
        font-weight: 600;
        color: var(--coklat);
        margin-bottom: .9rem;
        line-height: 1.5;
    }
    .kasir-nominal { font-size: 1.4rem; font-weight: 800; color: var(--emas); margin-bottom: .2rem; }
    .kasir-info { font-size: .78rem; color: var(--teks-muted); margin-bottom: .9rem; }
    .kasir-qr-wrap {
        background: var(--krem-dark);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: .75rem;
    }
    .kasir-qr-wrap .qr-label { font-size: .75rem; color: var(--teks-muted); margin-bottom: .5rem; }
    .kasir-qr-wrap img { width: 130px; height: 130px; display: block; margin: 0 auto; }
    .kasir-qr-wrap .qr-sub { font-size: .75rem; color: var(--teks-muted); margin-top: .5rem; }
    .kasir-note {
        background: var(--krem-dark);
        border-radius: 10px;
        padding: .65rem 1rem;
        font-size: .8rem;
        color: var(--teks-muted);
    }

    /* ── Buttons ── */
    .btn-nota {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--coklat);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: .65rem 1.25rem;
        font-size: .82rem;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 1rem;
        cursor: pointer;
    }
    .btn-kembali {
        display: block;
        width: 100%;
        background: var(--emas);
        color: var(--coklat);
        border: none;
        border-radius: 14px;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-kembali:hover { background: var(--emas-light); }
</style>
@endpush

@section('content')

{{-- Topbar --}}
<div class="topbar">
    <a href="{{ url()->previous() }}" class="topbar-back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="topbar-icon"><i class="bi bi-wallet2"></i></span>
    <span class="topbar-title">Pembayaran</span>
</div>

<div class="pembayaran-body">

    {{-- ── Ringkasan Pesanan ── --}}
    <div class="ringkasan-card">
        <div class="ringkasan-title">
            <i class="bi bi-clipboard2-check" style="color:var(--emas);"></i>
            Ringkasan Pesanan
        </div>

        @foreach($order->detailOrders as $item)
            <div class="ringkasan-item">
                <span class="nama">{{ $item->menu->name_menu ?? '-' }} × {{ $item->total }}</span>
                <span class="harga">Rp. {{ number_format($item->sub_total, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <hr class="ringkasan-divider">

        <div class="ringkasan-total">
            <span class="label">Total Bayar</span>
            <span class="amount">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         STATE 1: Pilih Metode (default)
    ══════════════════════════════════════ --}}
    @if(!$metode)

        <div class="metode-title">Pilih Metode Pembayaran</div>

        <a href="{{ route('pelanggan.pembayaran', ['kd_order' => $order->kd_order, 'metode' => 'qris']) }}"
           class="metode-card">
            <div class="metode-icon"><i class="bi bi-qr-code"></i></div>
            <div class="metode-info">
                <div class="nama">QRIS</div>
                <div class="desc">Bayar via QR Code (GoPay, OVO, Dana, dll)</div>
            </div>
            <i class="bi bi-chevron-right metode-arrow"></i>
        </a>

        <a href="{{ route('pelanggan.pembayaran', ['kd_order' => $order->kd_order, 'metode' => 'kasir']) }}"
           class="metode-card">
            <div class="metode-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="metode-info">
                <div class="nama">Kasir</div>
                <div class="desc">Bayar langsung ke kasir (tunai/debit)</div>
            </div>
            <i class="bi bi-chevron-right metode-arrow"></i>
        </a>

    {{-- ══════════════════════════════════════
         STATE 2: QRIS
    ══════════════════════════════════════ --}}
    @elseif($metode === 'qris')

        <div class="qris-card">
            <div class="judul">Scan QR untuk Membayar</div>
            <div class="sub">Gunakan aplikasi dompet digital untuk scan QR di bawah</div>

            <div class="qris-img-wrap">
                {{-- Ganti src dengan path QR QRIS restoran --}}
                <img src="{{ asset('images/qris.png') }}" alt="QR Code QRIS"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none;width:160px;height:160px;align-items:center;justify-content:center;
                            background:var(--krem-dark);border-radius:8px;font-size:3rem;color:var(--emas);">
                    <i class="bi bi-qr-code"></i>
                </div>
            </div>

            <div class="qris-nominal">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</div>
            <div class="qris-info">Meja {{ $order->no_meja }} &nbsp;·&nbsp; Order {{ $order->kd_order }}</div>

            <div class="qris-steps">
                1. Buka aplikasi GoPay / OVO / Dana / BCA Mobile<br>
                2. Pilih menu "Scan QR" atau "Bayar"<br>
                3. Arahkan kamera ke QR Code di atas<br>
                4. Konfirmasi jumlah pembayaran<br>
                5. Pembayaran akan di acc langsung oleh kasir
            </div>
        </div>

        <a href="{{ route('pelanggan.pesanan') }}" class="btn-nota">
            <i class="bi bi-check2-square"></i> Pesanan Selesai - Lihat Nota
        </a>

        <a href="{{ route('pelanggan.keranjang') }}" class="btn-kembali">
            Kembali ke Keranjang
        </a>

    {{-- ══════════════════════════════════════
         STATE 3: Kasir
    ══════════════════════════════════════ --}}
    @elseif($metode === 'kasir')

        <div class="kasir-card">
            <div class="judul">Pembayaran di Kasir</div>

            <div class="kasir-banner">
                Tunjukkan QR Code di bawah kepada kasir.<br>
                Kasir akan memindai kode pesananmu.
            </div>

            <div class="kasir-nominal">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</div>
            <div class="kasir-info">Total yang harus dibayar - Meja {{ $order->no_meja }}</div>

            <div class="kasir-qr-wrap">
                <div class="qr-label">QR Code Pesanan - Tunjukkan ke kasir</div>
                {{-- QR berisi kd_order, di-generate via library atau service --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($order->kd_order) }}"
                     alt="QR Pesanan">
                <div class="qr-sub">{{ $order->kd_order }} - Meja {{ $order->no_meja }}</div>
            </div>

            <div class="kasir-note">
                <i class="bi bi-info-circle me-1"></i>
                Kasir menerima pembayaran tunai dan kartu debit/kredit.
            </div>
        </div>

        <a href="{{ route('pelanggan.pesanan') }}" class="btn-nota">
            <i class="bi bi-check2-square"></i> Pesanan Selesai - Lihat Nota
        </a>

        <a href="{{ route('pelanggan.keranjang') }}" class="btn-kembali">
            Kembali ke Keranjang
        </a>

    @endif

</div>

@endsection