@extends('layouts.pelanggan')

@section('title', $menu->name_menu . ' — Dapur Nusantara')

@section('content')
@push('styles')
<style>
    .menu-hero { width: 100%; height: 220px; object-fit: cover; }
    .menu-hero-ph {
        width: 100%; height: 220px;
        background: var(--krem-dark);
        display: flex; align-items: center; justify-content: center;
        font-size: 4rem; color: var(--emas);
    }

    .detail-body { padding: 1rem; }
    .menu-nama { font-size: 1.2rem; font-weight: 800; color: var(--coklat); margin-bottom: .25rem; }
    .menu-harga { font-size: 1rem; font-weight: 700; color: var(--emas); margin-bottom: .4rem; }
    .menu-desc { color: var(--teks-muted); font-size: .875rem; margin-bottom: 1.25rem; line-height: 1.5; }

    .section-label {
        font-size: .72rem; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
        color: var(--coklat); margin-bottom: .4rem;
    }

    .qty-wrap {
        border: 1.5px solid #e8ddd0;
        border-radius: 12px;
        padding: .3rem .6rem;
        display: flex; align-items: center;
        background: var(--krem);
        margin-bottom: 1rem;
    }
    .qty-wrap input[type=number] {
        flex-grow: 1; border: none; background: transparent;
        text-align: center; font-size: 1rem; font-weight: 600;
        color: var(--coklat); font-family: inherit; outline: none;
        -moz-appearance: textfield;
    }
    .qty-wrap input::-webkit-outer-spin-button,
    .qty-wrap input::-webkit-inner-spin-button { -webkit-appearance: none; }
    .qty-btn {
        width: 32px; height: 32px;
        border-radius: 8px; border: none;
        background: var(--emas); color: var(--coklat);
        font-size: 1.1rem; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .subtotal-bar {
        background: var(--emas);
        border-radius: 12px;
        padding: .85rem 1.25rem;
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem;
    }
    .subtotal-bar span { color: var(--coklat); font-weight: 700; }

    .catatan-input {
        width: 100%; border: 1.5px solid #e8ddd0;
        border-radius: 12px; padding: .75rem 1rem;
        font-size: .875rem; background: var(--krem);
        resize: none; font-family: inherit;
        color: var(--coklat); outline: none;
    }
    .catatan-input:focus { border-color: var(--emas); }
    .catatan-hint { font-size: .75rem; color: var(--teks-muted); font-style: italic; margin-top: .35rem; }

    .btn-tambah {
        background: var(--emas); color: var(--coklat);
        border: none; border-radius: 16px;
        width: 100%; padding: 1rem;
        font-size: 1rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center; gap: .6rem;
        cursor: pointer; margin-top: 1.25rem;
        transition: background .2s, transform .1s;
    }
    .btn-tambah:hover { background: var(--emas-light); }
    .btn-tambah:active { transform: scale(.98); }
</style>
@endpush

{{-- Topbar --}}
<div class="topbar">
    <a href="{{ url()->previous() }}" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
    <span class="topbar-title">{{ $menu->name_menu }}</span>
</div>

{{-- Foto Menu --}}
@if($menu->photo)
    <img src="{{ Storage::url($menu->photo) }}" alt="{{ $menu->name_menu }}" class="menu-hero">
@else
    <div class="menu-hero-ph"><i class="bi bi-egg-fried"></i></div>
@endif

<div class="detail-body">
    <div class="menu-nama">{{ $menu->name_menu }}</div>
    <div class="menu-harga">Rp. {{ number_format($menu->harga,0,',','.') }}</div>
    @if($menu->description)
        <div class="menu-desc">{{ $menu->description }}</div>
    @endif

    <form action="{{ route('pelanggan.keranjang.tambah') }}" method="POST" id="formTambah">
        @csrf
        <input type="hidden" name="kd_menu" value="{{ $menu->kd_menu }}">

        {{-- Jumlah --}}
        <div class="section-label">Jumlah</div>
        <div class="qty-wrap">
            <button type="button" class="qty-btn" onclick="ubahQty(-1)">−</button>
            <input type="number" name="jumlah" id="inputJumlah" value="1" min="1" max="99" readonly>
            <button type="button" class="qty-btn" onclick="ubahQty(1)">+</button>
        </div>

        {{-- Subtotal --}}
        <div class="subtotal-bar">
            <span>Sub Total</span>
            <span id="subtotalText">Rp {{ number_format($menu->harga,0,',','.') }}</span>
        </div>

        {{-- Catatan --}}
        <div class="section-label">Catatan untuk Dapur (Opsional)</div>
        <textarea name="keterangan" class="catatan-input" rows="3"
            placeholder="Contoh: tidak pedas, tanpa timun">{{ old('keterangan') }}</textarea>
        <div class="catatan-hint">Catatan tersimpan otomatis saat tambah ke keranjang</div>

        {{-- Tombol --}}
        @if($menu->status === 'tersedia')
            <button type="submit" class="btn-tambah" id="btnTambah">
                <i class="bi bi-cart3"></i> Tambah ke Keranjang
            </button>
        @else
            <button type="button" class="btn-tambah" disabled style="background:#ddd;color:#999;cursor:not-allowed;">
                Menu Habis
            </button>
        @endif
    </form>
</div>

@push('scripts')
<script>
    const harga = {{ $menu->harga }};

    function ubahQty(delta) {
        const el = document.getElementById('inputJumlah');
        let val = parseInt(el.value) + delta;
        val = Math.max(1, Math.min(99, val));
        el.value = val;
        document.getElementById('subtotalText').textContent =
            'Rp ' + (harga * val).toLocaleString('id-ID');
    }

    document.getElementById('formTambah')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnTambah');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...'; }
    });
</script>
@endpush
@endsection