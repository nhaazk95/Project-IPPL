@extends('layouts.guest')

@section('title', 'Keranjang — Dapur Nusantara')

@push('styles')
<style>
    .keranjang-body {
        padding: 1rem 1rem 0;
    }

    .keranjang-count {
        font-weight: 700;
        font-size: .9rem;
        color: var(--brown);
        margin-bottom: .75rem;
    }

    .item-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(44,24,16,.07);
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem;
        margin-bottom: .75rem;
    }

    .item-card img {
        width: 64px;
        height: 64px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .img-ph {
        width: 64px;
        height: 64px;
        border-radius: 10px;
        background: var(--cream-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        color: var(--orange);
        flex-shrink: 0;
    }

    .item-info { flex-grow: 1; min-width: 0; }

    .item-name {
        font-weight: 700;
        font-size: .88rem;
        color: var(--brown);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-price {
        font-weight: 700;
        color: var(--orange);
        font-size: .9rem;
        margin-top: 4px;
    }

    .btn-hapus {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fff3f3;
        border: 1.5px solid #f5c6cb;
        color: #e74c3c;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* qty */
    .qty-stepper {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .qty-stepper button {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1.5px solid var(--orange);
        background: #fff;
        color: var(--orange);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-stepper span {
        font-weight: 700;
        min-width: 20px;
        text-align: center;
        color: var(--brown);
    }

    /* catatan */
    .catatan-box {
        background: #fff;
        border-radius: 14px;
        padding: .85rem 1rem;
        margin-bottom: .75rem;
        box-shadow: 0 2px 10px rgba(44,24,16,.07);
    }

    .catatan-box textarea {
        width: 100%;
        border: 1.5px solid var(--cream-dark);
        border-radius: 8px;
        padding: .5rem .75rem;
        resize: none;
        font-size: .85rem;
        outline: none;
    }

    .catatan-box textarea:focus {
        border-color: var(--orange);
    }

    /* footer */
    .footer-total {
        position: fixed;
        bottom: var(--bottombar, 70px);
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        background: var(--brown);
        padding: .9rem 1.25rem;
        z-index: 90;
    }

    .footer-total .label {
        color: rgba(255,255,255,.7);
        font-size: .8rem;
    }

    .footer-total .total {
        color: var(--orange);
        font-size: 1.3rem;
        font-weight: 800;
    }

    .btn-lanjut {
        background: var(--orange);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: .7rem 1.2rem;
        font-weight: 800;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: var(--orange);
        margin-bottom: .75rem;
    }
</style>
@endpush

@section('content')

{{-- TOPBAR --}}
<div class="topbar">
    <a href="{{ route('pelanggan.beranda') }}" class="topbar-back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="topbar-icon"><i class="bi bi-cart3"></i></span>
    <span class="topbar-title">Keranjang Pesanan</span>
</div>

{{-- FLASH --}}
@if(session('success'))
<div class="alert alert-success m-3">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger m-3">{{ session('error') }}</div>
@endif

<div class="keranjang-body" style="padding-bottom: {{ $keranjang->isNotEmpty() ? '170px' : '1rem' }}">

@if($keranjang->isEmpty())

    <div class="empty-state">
        <i class="bi bi-cart-x"></i>
        <p>Keranjang kamu masih kosong</p>
        <a href="{{ route('pelanggan.menu') }}" class="btn btn-warning mt-2">
            Lihat Menu
        </a>
    </div>

@else

    <div class="keranjang-count">
        Pesananmu ({{ $keranjang->count() }} item)
    </div>

    @foreach($keranjang as $item)
    <div class="item-card">

        {{-- IMAGE --}}
        @if($item->menu?->photo)
            <img src="{{ Storage::url($item->menu->photo) }}" alt="">
        @else
            <div class="img-ph"><i class="bi bi-egg-fried"></i></div>
        @endif

        {{-- INFO --}}
        <div class="item-info">
            <div class="item-name">
                {{ $item->menu->name_menu }}
            </div>

            {{-- QTY --}}
            <div class="qty-stepper">

                <form action="{{ route('pelanggan.keranjang.update', $item->kd_detail) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="jumlah" value="{{ max(1, $item->total - 1) }}">
                    <button type="submit">−</button>
                </form>

                <span>{{ $item->total }}</span>

                <form action="{{ route('pelanggan.keranjang.update', $item->kd_detail) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="jumlah" value="{{ $item->total + 1 }}">
                    <button type="submit">+</button>
                </form>

                <small>pcs</small>
            </div>

            <div class="item-price">
                Rp {{ number_format($item->sub_total,0,',','.') }}
            </div>
        </div>

        {{-- DELETE --}}
        <form action="{{ route('pelanggan.keranjang.hapus', $item->kd_detail) }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn-hapus" onclick="return confirm('Hapus item?')">
                <i class="bi bi-trash3"></i>
            </button>
        </form>

    </div>
    @endforeach

    {{-- CATATAN --}}
    <div class="catatan-box">
        <label>Catatan untuk dapur (opsional)</label>
        <textarea id="catatanInput" rows="2" placeholder="Contoh: tidak pedas"></textarea>
    </div>

    {{-- MEJA --}}
    <div class="catatan-box">
        <strong>Meja:</strong> Meja {{ session('pelanggan_no_meja') }}
    </div>

@endif

</div>

{{-- FOOTER --}}
@if($keranjang->isNotEmpty())
<div class="footer-total">
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <div class="label">Total Bayar</div>
            <div class="total">
                Rp {{ number_format($keranjang->sum('sub_total'),0,',','.') }}
            </div>
        </div>

        <form action="{{ route('pelanggan.checkout') }}" method="POST">
            @csrf
            <input type="hidden" name="keterangan" id="keteranganHidden">

            <button type="submit" class="btn-lanjut" onclick="setKeterangan()">
                <i class="bi bi-credit-card"></i> Bayar
            </button>
        </form>

    </div>
</div>
@endif

@push('scripts')
<script>
function setKeterangan() {
    const catatan = document.getElementById('catatanInput');
    if (catatan) {
        document.getElementById('keteranganHidden').value = catatan.value;
    }
}
</script>
@endpush

@endsection