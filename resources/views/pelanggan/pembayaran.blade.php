@extends('layouts.pelanggan')
@section('title', 'Pembayaran — Dapur Nusantara')

@push('styles')
<style>
    :root { --coklat:#2c1810; --emas:#c9a227; --emas-light:#d4b040; --krem:#faf5ee; --krem-dark:#f0e8d8; --teks-muted:#7a6552; }
    body { background: var(--krem); }
    .pembayaran-body { padding: 1rem; padding-bottom: 5rem; }

    .ringkasan-card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(44,24,16,.08); padding:1rem 1.1rem; margin-bottom:1.25rem; }
    .ringkasan-title { font-weight:700; font-size:.9rem; color:var(--coklat); margin-bottom:.75rem; display:flex; align-items:center; gap:.4rem; }
    .ringkasan-item { display:flex; justify-content:space-between; font-size:.875rem; color:var(--teks-muted); margin-bottom:.35rem; }
    .ringkasan-item .nama { color:var(--coklat); }
    .ringkasan-item .harga { font-weight:600; color:var(--coklat); }
    .ringkasan-divider { border:none; border-top:1.5px dashed #e0d5c5; margin:.75rem 0; }
    .ringkasan-total { display:flex; justify-content:space-between; align-items:center; }
    .ringkasan-total .label { font-weight:700; font-size:.9rem; color:var(--coklat); }
    .ringkasan-total .amount { font-weight:800; font-size:1.05rem; color:var(--emas); }

    .metode-card { background:#fff; border-radius:14px; box-shadow:0 2px 10px rgba(44,24,16,.07); display:flex; align-items:center; gap:1rem; padding:1rem 1.1rem; margin-bottom:.75rem; cursor:pointer; text-decoration:none; transition:box-shadow .2s,transform .15s; border:2px solid transparent; }
    .metode-card:hover { border-color:var(--emas); transform:translateY(-1px); }
    .metode-icon { width:52px; height:52px; background:var(--krem-dark); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:var(--emas); flex-shrink:0; }
    .metode-info .nama { font-weight:700; font-size:.9rem; color:var(--coklat); }
    .metode-info .desc { font-size:.78rem; color:var(--teks-muted); margin-top:2px; }

    .qris-card, .kasir-card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(44,24,16,.08); padding:1.25rem 1.1rem; margin-bottom:1rem; text-align:center; }
    .judul { font-weight:700; font-size:.95rem; color:var(--coklat); margin-bottom:.25rem; }
    .sub   { font-size:.8rem; color:var(--teks-muted); margin-bottom:1rem; }
    .qris-img-wrap { display:inline-block; background:var(--krem-dark); border-radius:12px; padding:.75rem; margin-bottom:.75rem; }
    .qris-img-wrap img { width:160px; height:160px; display:block; }
    .nominal { font-size:1.4rem; font-weight:800; color:var(--emas); margin-bottom:.2rem; }
    .info-kecil { font-size:.78rem; color:var(--teks-muted); margin-bottom:1rem; }
    .steps-box { background:var(--krem-dark); border-radius:10px; padding:.75rem 1rem; text-align:left; font-size:.8rem; color:var(--teks-muted); line-height:1.8; }
    .kasir-banner { background:var(--emas); border-radius:10px; padding:.75rem 1rem; font-size:.83rem; font-weight:600; color:var(--coklat); margin-bottom:.9rem; line-height:1.5; }
    .kasir-qr-wrap { background:var(--krem-dark); border-radius:12px; padding:1rem; margin-bottom:.75rem; }
    .kasir-qr-wrap .qr-label { font-size:.75rem; color:var(--teks-muted); margin-bottom:.5rem; }
    .kasir-qr-wrap img { width:130px; height:130px; display:block; margin:0 auto; }
    .kasir-qr-wrap .qr-sub { font-size:.75rem; color:var(--teks-muted); margin-top:.5rem; }
    .kasir-note { background:var(--krem-dark); border-radius:10px; padding:.65rem 1rem; font-size:.8rem; color:var(--teks-muted); }

    /* Tombol sticky bawah */
    .action-footer { position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:1.5px solid var(--krem-dark); padding:.85rem 1.1rem; display:flex; gap:.75rem; flex-direction:column; z-index:50; }
    .btn-lihat-nota { display:flex; align-items:center; justify-content:center; gap:.5rem; background:var(--coklat); color:var(--emas); border:none; border-radius:14px; padding:.85rem; font-size:.95rem; font-weight:800; text-decoration:none; cursor:pointer; font-family:inherit; }
    .btn-lihat-nota:hover { opacity:.9; }
    .btn-kembali { display:flex; align-items:center; justify-content:center; gap:.5rem; background:var(--krem-dark); color:var(--coklat); border:none; border-radius:14px; padding:.75rem; font-size:.85rem; font-weight:700; text-decoration:none; cursor:pointer; font-family:inherit; }
</style>
@endpush

@section('content')

<div class="topbar">
    <a href="{{ url()->previous() }}" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
    <span class="topbar-icon"><i class="bi bi-wallet2"></i></span>
    <span class="topbar-title">Pembayaran</span>
</div>

<div class="pembayaran-body">

    {{-- Ringkasan --}}
    <div class="ringkasan-card">
        <div class="ringkasan-title">
            <i class="bi bi-clipboard2-check" style="color:var(--emas);"></i> Ringkasan Pesanan
        </div>
        @foreach($order->detailOrders as $item)
        <div class="ringkasan-item">
            <span class="nama">{{ $item->menu->name_menu ?? '-' }} × {{ $item->total }}</span>
            <span class="harga">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <hr class="ringkasan-divider">
        <div class="ringkasan-total">
            <span class="label">Total Bayar</span>
            <span class="amount">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- STATE: Pilih Metode --}}
    @if(!$metode)
    <p style="font-weight:700;font-size:.9rem;color:var(--coklat);margin-bottom:.75rem;">Pilih Metode Pembayaran</p>

    <a href="{{ route('pelanggan.pembayaran', ['kd_order'=>$order->kd_order,'metode'=>'qris']) }}" class="metode-card">
        <div class="metode-icon"><i class="bi bi-qr-code"></i></div>
        <div class="metode-info">
            <div class="nama">QRIS</div>
            <div class="desc">Bayar via QR Code (GoPay, OVO, Dana, dll)</div>
        </div>
        <i class="bi bi-chevron-right" style="color:var(--teks-muted);"></i>
    </a>

    <a href="{{ route('pelanggan.pembayaran', ['kd_order'=>$order->kd_order,'metode'=>'kasir']) }}" class="metode-card">
        <div class="metode-icon"><i class="bi bi-cash-stack"></i></div>
        <div class="metode-info">
            <div class="nama">Kasir</div>
            <div class="desc">Bayar langsung ke kasir (tunai/debit)</div>
        </div>
        <i class="bi bi-chevron-right" style="color:var(--teks-muted);"></i>
    </a>

    {{-- STATE: QRIS --}}
    @elseif($metode === 'qris')
    <div class="qris-card">
        <div class="judul">Scan QR untuk Membayar</div>
        <div class="sub">Gunakan aplikasi dompet digital untuk scan QR di bawah</div>
        <div class="qris-img-wrap">
            <img src="{{ asset('images/qris.png') }}" alt="QR QRIS"
                onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div style="display:none;width:160px;height:160px;align-items:center;justify-content:center;background:var(--krem-dark);border-radius:8px;font-size:3rem;color:var(--emas);">
                <i class="bi bi-qr-code"></i>
            </div>
        </div>
        <div class="nominal">Rp {{ number_format($totalHarga, 0, ',', '.') }}</div>
        <div class="info-kecil">Meja {{ $order->no_meja }} &nbsp;·&nbsp; {{ $order->kd_order }}</div>
        <div class="steps-box">
            1. Buka aplikasi GoPay / OVO / Dana / BCA Mobile<br>
            2. Pilih menu "Scan QR" atau "Bayar"<br>
            3. Arahkan kamera ke QR Code di atas<br>
            4. Konfirmasi jumlah pembayaran<br>
            5. Tunjukkan bukti bayar ke kasir
        </div>
    </div>

    {{-- STATE: Kasir --}}
    @elseif($metode === 'kasir')
    <div class="kasir-card">
        <div class="judul">Pembayaran di Kasir</div>
        <div class="kasir-banner">
            Tunjukkan QR Code di bawah kepada kasir.<br>
            Kasir akan memindai kode pesananmu.
        </div>
        <div class="nominal">Rp {{ number_format($totalHarga, 0, ',', '.') }}</div>
        <div class="info-kecil">Total yang harus dibayar — Meja {{ $order->no_meja }}</div>
        <div class="kasir-qr-wrap">
            <div class="qr-label">QR Code Pesanan — Tunjukkan ke kasir</div>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($order->kd_order) }}" alt="QR Pesanan">
            <div class="qr-sub">{{ $order->kd_order }} — Meja {{ $order->no_meja }}</div>
        </div>
        <div class="kasir-note">
            <i class="bi bi-info-circle"></i> Kasir menerima pembayaran tunai dan kartu debit/kredit.
        </div>
    </div>
    @endif

    {{-- Tombol Lihat Nota — muncul langsung di bawah konten setelah pilih metode --}}
    @if($metode)
    <div style="display:flex;flex-direction:column;gap:.75rem;margin-top:1rem;margin-bottom:1rem;">
        <a href="{{ route('pelanggan.pesanan') }}"
            style="display:flex;align-items:center;justify-content:center;gap:.5rem;
                   background:#2c1810;color:#c9a227;border:none;border-radius:14px;
                   padding:.85rem;font-size:.95rem;font-weight:800;text-decoration:none;">
            <i class="bi bi-receipt"></i> Lihat Nota Pesanan
        </a>
        <a href="{{ route('pelanggan.beranda') }}"
            style="display:flex;align-items:center;justify-content:center;gap:.5rem;
                   background:var(--krem-dark);color:var(--coklat);border:none;border-radius:14px;
                   padding:.75rem;font-size:.85rem;font-weight:700;text-decoration:none;">
            <i class="bi bi-house"></i> Kembali ke Beranda
        </a>
    </div>
    @endif

</div>

{{-- Hapus action-footer sticky yang dulu --}}

@endsection