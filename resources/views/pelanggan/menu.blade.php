@extends('layouts.guest')

@section('title', 'Menu — Dapur Nusantara')

@section('content')
    @push('styles')
        <style>
            .menu-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: .75rem;
                padding: 1rem;
            }

            @media (max-width: 480px) {
                .menu-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .menu-card-grid {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 2px 10px rgba(44, 24, 16, .08);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .menu-card-grid img {
                width: 100%;
                height: 110px;
                object-fit: cover;
            }

            .menu-card-grid .img-ph {
                width: 100%;
                height: 110px;
                background: var(--krem-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                color: var(--emas);
            }

            .menu-card-grid .info {
                padding: .6rem .75rem .75rem;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .menu-card-grid .name {
                font-weight: 700;
                font-size: .82rem;
                color: var(--coklat);
                margin-bottom: .2rem;
                line-height: 1.2;
            }

            .menu-card-grid .price {
                font-size: .8rem;
                color: var(--teks-muted);
                margin-bottom: .5rem;
            }

            .menu-card-grid .btn-order {
                background: var(--emas);
                color: var(--coklat);
                border: none;
                border-radius: 8px;
                padding: .35rem;
                font-size: .75rem;
                font-weight: 700;
                text-align: center;
                text-decoration: none;
                display: block;
                margin-top: auto;
                cursor: pointer;
            }

            .menu-card-grid .btn-habis {
                background: #ddd;
                color: #999;
                border-radius: 8px;
                padding: .35rem;
                font-size: .75rem;
                text-align: center;
                margin-top: auto;
            }

            .search-menu {
                margin: 0 1rem .75rem;
                position: relative;
            }

            .search-menu i {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #b0998a;
            }

            .search-menu input {
                width: 100%;
                padding: .7rem 1rem .7rem 2.8rem;
                border-radius: 50px;
                border: 1.5px solid #e8ddd0;
                background: #fff;
                font-size: .875rem;
                outline: none;
                font-family: inherit;
                color: var(--coklat);
            }

            .search-menu input:focus {
                border-color: var(--emas);
            }
        </style>
    @endpush

    {{-- Topbar --}}
    <div class="topbar">
        <a href="{{ url()->previous() }}" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
        <span class="topbar-icon"><i class="bi bi-egg-fried"></i></span>
        <span class="topbar-title">
            {{ $kategoriAktif?->name_kategori ?? 'Semua Menu' }}
        </span>
    </div>

    {{-- Search --}}
    <div class="search-menu mt-3">
        <i class="bi bi-search"></i>
        <input type="text" id="searchMenu" placeholder="Cari menu..." onkeyup="filterMenu(this.value)"
            value="{{ request('q') }}">
    </div>

    {{-- Grid Menu --}}
    @if($menus->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size:3rem;color:var(--emas);"></i>
            <p class="mt-2 text-muted">Menu tidak ditemukan</p>
        </div>
    @else
        <div class="menu-grid" id="menuGrid">
            @foreach($menus as $menu)
                <div class="menu-card-grid" data-nama="{{ strtolower($menu->name_menu) }}">
                    @if($menu->photo)
                        <img src="{{ Storage::url($menu->photo) }}" alt="{{ $menu->name_menu }}" loading="lazy">
                    @else
                        <div class="img-ph"><i class="bi bi-egg-fried"></i></div>
                    @endif
                    <div class="info">
                        <div class="name">{{ $menu->name_menu }}</div>
                        <div class="price">Rp. {{ number_format($menu->harga, 0, ',', '.') }}</div>
                        @if($menu->status === 'tersedia')
                            <a href="{{ route('pelanggan.menu.detail', $menu->kd_menu) }}" class="btn-order">Order →</a>
                        @else
                            <div class="btn-habis">Habis</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @push('scripts')
        <script>
            function filterMenu(q) {
                q = q.toLowerCase();
                document.querySelectorAll('#menuGrid .menu-card-grid').forEach(el => {
                    el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
                });
            }
        </script>
    @endpush
@endsection