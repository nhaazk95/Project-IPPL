@extends('layouts.pelanggan')

@section('title', 'Konfirmasi Pesanan — Dapur Nusantara')

@push('styles')
<style>
    :root {
        --coklat: #2c1810; --emas: #c9a227; --emas-light: #d4b040;
        --krem: #faf5ee; --krem-dark: #f0e8d8; --teks-muted: #7a6552;
    }
    body { background: var(--krem); }
    .preview-body { padding: 1rem; padding-bottom: 6rem; }

    .section-title {
        font-weight: 700; font-size: .88rem; color: var(--coklat);
        margin-bottom: .75rem; display: flex; align-items: center; gap: .4rem;
    }

    /* Ringkasan */
    .ringkasan-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(44,24,16,.08);
        padding: 1rem 1.1rem; margin-bottom: 1.1rem;
    }
    .ringkasan-item {
        display: flex; justify-content: space-between;
        font-size: .875rem; color: var(--teks-muted); margin-bottom: .35rem;
    }
    .ringkasan-item .nama { color: var(--coklat); }
    .ringkasan-item .harga { font-weight: 600; color: var(--coklat); }
    .ringkasan-divider { border: none; border-top: 1.5px dashed #e0d5c5; margin: .75rem 0; }
    .ringkasan-total {
        display: flex; justify-content: space-between; align-items: center;
    }
    .ringkasan-total .label { font-weight: 700; font-size: .9rem; color: var(--coklat); }
    .ringkasan-total .amount { font-weight: 800; font-size: 1.1rem; color: var(--emas); }

    /* Info meja */
    .info-meja {
        background: rgba(201,162,39,.1); border: 1.5px solid rgba(201,162,39,.3);
        border-radius: 10px; padding: .6rem 1rem;
        font-size: .83rem; font-weight: 600; color: var(--coklat);
        display: flex; align-items: center; gap: .5rem;
        margin-bottom: 1.1rem;
    }

    /* Metode cards */
    .metode-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 10px rgba(44,24,16,.07);
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem 1.1rem; margin-bottom: .75rem;
        cursor: pointer; border: 2px solid transparent;
        transition: border-color .18s, transform .15s;
    }
    .metode-card:has(input:checked),
    .metode-card.selected {
        border-color: var(--emas);
        background: rgba(201,162,39,.05);
    }
    .metode-card input[type=radio] { display: none; }
    .metode-icon {
        width: 50px; height: 50px; background: var(--krem-dark);
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: var(--emas); flex-shrink: 0;
    }
    .metode-info { flex-grow: 1; }
    .metode-info .nama { font-weight: 700; font-size: .9rem; color: var(--coklat); }
    .metode-info .desc { font-size: .78rem; color: var(--teks-muted); margin-top: 2px; }
    .metode-radio {
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid var(--krem-dark);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: border-color .15s;
    }
    .metode-radio .dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--emas); display: none;
    }
    .metode-card.selected .metode-radio { border-color: var(--emas); }
    .metode-card.selected .metode-radio .dot { display: block; }

    /* Padding bawah supaya tidak ketutup navbar */
    .preview-body { padding: 1rem; padding-bottom: 2rem; }
</style>
@endpush

@section('content')

<div class="topbar">
    <a href="{{ route('pelanggan.keranjang') }}" class="topbar-back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="topbar-icon"><i class="bi bi-wallet2"></i></span>
    <span class="topbar-title">Konfirmasi Pesanan</span>
</div>

@if(session('error'))
<div style="background:#f8d7da;color:#721c24;font-size:.82rem;padding:.6rem 1rem;border-left:4px solid #e74c3c;margin:.5rem 1rem 0;">
    {{ session('error') }}
</div>
@endif

<div class="preview-body">

    {{-- Info Meja --}}
    <div class="info-meja">
        <i class="bi bi-shop"></i>
        Meja {{ $noMeja ?? '-' }}
        @if($keterangan)
            &nbsp;·&nbsp; <span style="font-weight:400;color:var(--teks-muted);">{{ $keterangan }}</span>
        @endif
    </div>

    {{-- Ringkasan Pesanan --}}
    <div class="ringkasan-card">
        <div class="section-title">
            <i class="bi bi-clipboard2-check" style="color:var(--emas);"></i>
            Pesananmu
        </div>

        @foreach($keranjang as $item)
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

    {{-- Pilih Metode --}}
    <div class="section-title" style="margin-top:.25rem;">
        <i class="bi bi-credit-card" style="color:var(--emas);"></i>
        Pilih Metode Pembayaran
    </div>

    <form method="POST" action="{{ route('pelanggan.konfirmasi-metode') }}" id="formMetode">
        @csrf

        <label class="metode-card" id="cardKasir" onclick="pilihMetode('kasir')">
            <input type="radio" name="metode" value="kasir" id="radioKasir">
            <div class="metode-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="metode-info">
                <div class="nama">Bayar di Kasir</div>
                <div class="desc">Bayar langsung ke kasir (tunai / debit)</div>
            </div>
            <div class="metode-radio" id="radioKasirUI"><div class="dot"></div></div>
        </label>

        <label class="metode-card" id="cardQris" onclick="pilihMetode('qris')">
            <input type="radio" name="metode" value="qris" id="radioQris">
            <div class="metode-icon"><i class="bi bi-qr-code"></i></div>
            <div class="metode-info">
                <div class="nama">QRIS</div>
                <div class="desc">Scan QR — GoPay, OVO, Dana, BCA Mobile</div>
            </div>
            <div class="metode-radio" id="radioQrisUI"><div class="dot"></div></div>
        </label>

        {{-- Tombol submit di dalam konten, bukan sticky --}}
        <div style="margin-top:1.5rem;margin-bottom:5rem;">
            <button type="submit" class="btn-pesan" id="btnPesan" disabled
                style="display:flex;align-items:center;justify-content:center;gap:.5rem;
                       width:100%;border:none;border-radius:14px;padding:1rem;
                       font-size:1rem;font-weight:800;cursor:not-allowed;
                       font-family:inherit;transition:all .2s;
                       background:#d9cbb5;color:#a09080;">
                <i class="bi bi-check-circle-fill"></i> Pilih metode dulu...
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function pilihMetode(val) {
    document.getElementById('radioKasir').checked = (val === 'kasir');
    document.getElementById('radioQris').checked  = (val === 'qris');

    document.getElementById('cardKasir').classList.toggle('selected', val === 'kasir');
    document.getElementById('cardQris').classList.toggle('selected',  val === 'qris');

    const btn = document.getElementById('btnPesan');
    btn.disabled = false;
    btn.style.cssText = 'display:flex;align-items:center;justify-content:center;gap:.5rem;' +
        'width:100%;border:none;border-radius:14px;padding:1rem;' +
        'font-size:1rem;font-weight:800;cursor:pointer;font-family:inherit;transition:all .2s;' +
        'background:#c9a227;color:#2c1810;';
    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Kirim Pesanan';
}
</script>
@endpush

@endsection