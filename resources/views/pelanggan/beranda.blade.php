@extends('layouts.guest')

@section('title', 'Beranda — Dapur Nusantara')

@push('styles')
<style>
    :root {
        --coklat: #2c1810; --emas: #c9a227;
        --krem: #faf5ee; --krem-dark: #f0e8d8;
        --teks-muted: #7a6552;
    }
    .hero-banner {
        margin: 1rem; border-radius: 18px; overflow: hidden;
        position: relative; height: 160px;
        background: linear-gradient(135deg, var(--coklat) 0%, #4a2c1a 100%);
    }
    .hero-banner img { width: 100%; height: 100%; object-fit: cover; opacity: .55; }
    .hero-text {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 1rem 1.25rem;
        background: linear-gradient(to top, rgba(44,24,16,.9), transparent);
    }
    .hero-text h2 { color: #fff; font-size: 1.25rem; font-weight: 800; margin: 0; }
    .hero-text p  { color: rgba(255,255,255,.8); font-size: .8rem; margin: 0; }

    .search-wrap { margin: 0 1rem 1rem; position: relative; }
    .search-wrap i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #b0998a; }
    .search-wrap input {
        width: 100%; padding: .75rem 1rem .75rem 2.8rem;
        border-radius: 50px; border: 1.5px solid #e8ddd0;
        background: #fff; font-size: .9rem; outline: none;
        font-family: inherit; color: var(--coklat);
    }
    .search-wrap input:focus { border-color: var(--emas); }

    .greeting-bar {
        margin: 0 1rem 1.25rem; background: var(--emas);
        border-radius: 14px; padding: .9rem 1rem;
        display: flex; align-items: center; gap: .75rem;
    }
    .greeting-bar i { font-size: 1.6rem; color: var(--coklat); }
    .greeting-bar .g-title { font-weight: 700; font-size: .95rem; color: var(--coklat); }
    .greeting-bar .g-sub   { font-size: .78rem; color: rgba(44,24,16,.75); }

    .section-title {
        display: flex; align-items: center; gap: .75rem;
        padding: 0 1rem; margin-bottom: .75rem;
        font-weight: 800; font-size: 1rem; color: var(--coklat);
    }
    .section-title::after {
        content: ''; flex-grow: 1; height: 1.5px;
        background: var(--emas); opacity: .4;
    }

    .menu-scroll { overflow-x: auto; padding: 0 1rem 1rem; display: flex; gap: .75rem; scrollbar-width: none; }
    .menu-scroll::-webkit-scrollbar { display: none; }

    .menu-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 10px rgba(44,24,16,.08);
        min-width: 140px; overflow: hidden; flex-shrink: 0;
    }
    .menu-card img        { width: 100%; height: 100px; object-fit: cover; }
    .menu-card .img-ph    {
        width: 100%; height: 100px; background: var(--krem-dark);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: var(--emas);
    }
    .menu-card .card-body-inner { padding: .65rem .75rem .75rem; }
    .bestseller-badge {
        background: var(--emas); color: var(--coklat);
        font-size: .6rem; font-weight: 700;
        padding: .15rem .5rem; border-radius: 4px;
        display: inline-block; margin-bottom: .3rem;
    }
    .menu-card .menu-name  { font-size: .82rem; font-weight: 700; color: var(--coklat); margin-bottom: .2rem; line-height: 1.2; }
    .menu-card .menu-price { font-size: .8rem; color: var(--teks-muted); margin-bottom: .5rem; }
    .btn-order {
        background: var(--emas); color: var(--coklat); border: none;
        border-radius: 8px; padding: .3rem .8rem;
        font-size: .75rem; font-weight: 700; width: 100%;
        cursor: pointer; text-align: center; text-decoration: none;
        display: block; transition: background .2s;
    }
    .btn-order:hover { background: var(--emas-light, #d4af37); color: var(--coklat); }

    .kat-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 10px rgba(44,24,16,.08);
        min-width: 140px; overflow: hidden; flex-shrink: 0; text-decoration: none;
    }
    .kat-card img     { width: 100%; height: 90px; object-fit: cover; }
    .kat-card .img-ph {
        width: 100%; height: 90px; background: var(--krem-dark);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: var(--emas);
    }
    .kat-card .kat-name { padding: .5rem .75rem; font-size: .82rem; font-weight: 700; color: var(--coklat); }
    .kat-btn-order {
        margin: 0 .75rem .65rem; background: var(--emas); color: var(--coklat);
        border-radius: 8px; padding: .3rem;
        font-size: .75rem; font-weight: 700;
        width: calc(100% - 1.5rem); text-align: center; display: block;
    }

    /* Topbar beranda custom */
    .topbar-beranda {
        background: var(--coklat); padding: .75rem 1rem;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 100;
    }
    .topbar-beranda .brand { display: flex; align-items: center; gap: .6rem; }
    .topbar-beranda .brand-icon-wrap {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--emas); display: flex; align-items: center; justify-content: center;
    }
    .topbar-beranda .brand-name { color: var(--emas); font-weight: 800; font-size: 1rem; }
    .topbar-beranda .keranjang-btn {
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(255,255,255,.1); border: none; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; position: relative; text-decoration: none;
    }
    .topbar-beranda .keranjang-btn .badge-k {
        position: absolute; top: -2px; right: -2px;
        background: #e74c3c; color: #fff; border-radius: 50%;
        width: 16px; height: 16px; font-size: .6rem;
        display: flex; align-items: center; justify-content: center; font-weight: 700;
    }
</style>
@endpush

{{-- Custom topbar beranda --}}
<div class="topbar-beranda">
    <div class="brand">
        <div class="brand-icon-wrap">
            <i class="bi bi-shop" style="color:var(--coklat);font-size:1rem;"></i>
        </div>
        <span class="brand-name">Dapur Nusantara</span>
    </div>
    <a href="{{ route('pelanggan.keranjang') }}" class="keranjang-btn">
        <i class="bi bi-cart3"></i>
        @php $jmlKeranjang = session('keranjang_count', 0); @endphp
        @if($jmlKeranjang > 0)
            <span class="badge-k">{{ $jmlKeranjang }}</span>
        @endif
    </a>
</div>

{{-- Hero Banner --}}
<div class="hero-banner">
    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80" alt="Banner" loading="lazy">
    <div class="hero-text">
        <h2>Selamat Datang 👋</h2>
        <p>Cita rasa nusantara, langsung antar ke mejamu</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert-auto mx-3 mb-3 p-3 rounded-3"
        style="background:#d4edda;color:#155724;font-size:.85rem;border-left:4px solid #27ae60;">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-auto mx-3 mb-3 p-3 rounded-3"
        style="background:#f8d7da;color:#721c24;font-size:.85rem;border-left:4px solid #e74c3c;">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
@endif

{{-- Search --}}
<div class="search-wrap">
    <i class="bi bi-search"></i>
    <input type="text" id="searchInput" placeholder="Cari menu atau kategori..."
        onkeyup="filterBeranda(this.value)">
</div>

{{-- Greeting --}}
<div class="greeting-bar">
    <i class="bi bi-egg-fried"></i>
    <div>
        <div class="g-title">Halo, {{ session('pelanggan.name_pelanggan', 'Tamu') }}! 👋</div>
        <div class="g-sub">Mau makan apa hari ini? · Meja {{ session('pelanggan.no_meja') }}</div>
    </div>
</div>

{{-- Best Seller --}}
@if(isset($bestSeller) && $bestSeller->count())
<div class="section-title">Best Seller ⭐</div>
<div class="menu-scroll" id="bestsellerSection">
    @foreach($bestSeller as $menu)
        <div class="menu-card menu-item" data-nama="{{ strtolower($menu->name_menu) }}">
            @if($menu->photo)
                <img src="{{ Storage::url($menu->photo) }}" alt="{{ $menu->name_menu }}" loading="lazy">
            @else
                <div class="img-ph"><i class="bi bi-egg-fried"></i></div>
            @endif
            <div class="card-body-inner">
                <div class="bestseller-badge">★ BEST SELLER</div>
                <div class="menu-name">{{ $menu->name_menu }}</div>
                <div class="menu-price">Rp. {{ number_format($menu->harga,0,',','.') }}</div>
                <a href="{{ route('pelanggan.menu.detail', $menu->kd_menu) }}" class="btn-order">
                    Order →
                </a>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- Semua Kategori --}}
@if(isset($kategoris) && $kategoris->count())
<div class="section-title" style="margin-top:.5rem;">Semua Kategori</div>
<div class="menu-scroll" id="kategoriSection">
    @foreach($kategoris as $kat)
        <a href="{{ route('pelanggan.menu', ['kategori' => $kat->kd_kategori]) }}"
            class="kat-card" data-nama="{{ strtolower($kat->name_kategori) }}">
            @if($kat->photo)
                <img src="{{ Storage::url($kat->photo) }}" alt="{{ $kat->name_kategori }}" loading="lazy">
            @else
                <div class="img-ph"><i class="bi bi-grid"></i></div>
            @endif
            <div class="kat-name">{{ $kat->name_kategori }}</div>
            <div class="kat-btn-order">Order →</div>
        </a>
    @endforeach
</div>
@endif

<div style="height:1rem;"></div>

@push('scripts')
<script>
    function filterBeranda(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(el => {
            el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
        });
        document.querySelectorAll('.kat-card').forEach(el => {
            el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
        });
    }
</script>
@endpush