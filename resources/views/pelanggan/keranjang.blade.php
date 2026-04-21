@extends('layouts.pelanggan')

@section('title', 'Keranjang — Dapur Nusantara')

@push('styles')
    <style>
        :root {
            --coklat: #2c1810;
            --emas: #c9a227;
            --krem: #faf5ee;
            --krem-dark: #f0e8d8;
            --teks-muted: #7a6552;
        }

        body {
            background: var(--krem);
        }

        .keranjang-wrap {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 56px - 68px);
            /* topbar + bottomnav */
            padding: 1rem 1rem 0;
        }

        /* ── Heading ── */
        .keranjang-count {
            font-weight: 700;
            font-size: .9rem;
            color: var(--coklat);
            margin-bottom: .75rem;
        }

        /* ── Item card ── */
        .item-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(44, 24, 16, .07);
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem;
            margin-bottom: .75rem;
            border: 1.5px solid #f0e8d8;
        }

        .item-card img {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .item-img-ph {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            background: var(--krem-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--emas);
            flex-shrink: 0;
        }

        .item-info {
            flex-grow: 1;
            min-width: 0;
        }

        .item-name {
            font-weight: 700;
            font-size: .9rem;
            color: var(--coklat);
            margin-bottom: .35rem;
        }

        .item-qty-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .3rem;
        }

        .qty-btn-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1.5px solid var(--emas);
            background: #fff;
            color: var(--emas);
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: background .15s;
            text-decoration: none;
            flex-shrink: 0;
        }

        .qty-btn-circle:hover {
            background: var(--emas);
            color: #fff;
        }

        .qty-num {
            font-weight: 700;
            font-size: .9rem;
            color: var(--coklat);
            min-width: 20px;
            text-align: center;
        }

        .item-unit {
            font-size: .78rem;
            color: var(--teks-muted);
        }

        .item-price {
            font-weight: 800;
            color: var(--emas);
            font-size: .9rem;
        }

        .btn-hapus-item {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff3f3;
            border: 1.5px solid #f5c6cb;
            color: #e74c3c;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            flex-shrink: 0;
            transition: background .15s;
        }

        .btn-hapus-item:hover {
            background: #fde8e8;
        }

        /* ── Catatan & Meja ── */
        .extra-card {
            background: #fff;
            border-radius: 14px;
            padding: .85rem 1rem;
            margin-bottom: .75rem;
            box-shadow: 0 2px 10px rgba(44, 24, 16, .06);
            border: 1.5px solid #f0e8d8;
        }

        .extra-card label {
            font-size: .8rem;
            color: var(--teks-muted);
            display: block;
            margin-bottom: .4rem;
        }

        .extra-card textarea {
            width: 100%;
            border: 1.5px solid var(--krem-dark);
            border-radius: 8px;
            padding: .5rem .75rem;
            resize: none;
            font-size: .85rem;
            outline: none;
            font-family: inherit;
            color: var(--coklat);
            background: var(--krem);
        }

        .extra-card textarea:focus {
            border-color: var(--emas);
        }

        .extra-card strong {
            font-size: .88rem;
            color: var(--coklat);
        }

        /* ── Footer Total+Bayar ── */
        .keranjang-footer {
            background: var(--coklat);
            padding: 1rem 1.25rem 1.1rem;
            margin: auto -1rem -1rem;
            /* pull ke tepi */
        }

        .footer-label {
            color: rgba(255, 255, 255, .65);
            font-size: .8rem;
            font-weight: 600;
        }

        .footer-total {
            color: var(--emas);
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: .85rem;
        }

        .btn-bayar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            background: var(--emas);
            color: var(--coklat);
            border: none;
            border-radius: 14px;
            padding: .9rem;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            transition: background .2s;
        }

        .btn-bayar:hover {
            background: #d4b040;
        }

        /* ── Empty ── */
        .empty-keranjang {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-keranjang i {
            font-size: 3.5rem;
            color: var(--emas);
            margin-bottom: .75rem;
        }

        .empty-keranjang p {
            color: var(--teks-muted);
            margin-bottom: 1rem;
        }

        .btn-ke-menu {
            background: var(--emas);
            color: var(--coklat);
            border: none;
            border-radius: 12px;
            padding: .5rem 2rem;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            display: inline-block;
            width: fit-content;
        }
    </style>
@endpush

@section('content')

    {{-- Topbar --}}
    <div class="topbar">
        <a href="{{ route('pelanggan.beranda') }}" class="topbar-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <span class="topbar-icon"><i class="bi bi-cart3"></i></span>
        <span class="topbar-title">Keranjang Pesanan</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div
            style="background:#d4edda;color:#155724;font-size:.82rem;padding:.6rem 1rem;border-left:4px solid #27ae60;margin:.5rem 1rem 0;">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div
            style="background:#f8d7da;color:#721c24;font-size:.82rem;padding:.6rem 1rem;border-left:4px solid #e74c3c;margin:.5rem 1rem 0;">
            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
        </div>
    @endif

    <div class="keranjang-wrap">

        @if($keranjang->isEmpty())
            <div class="empty-keranjang">
                <i class="bi bi-cart-x"></i>
                <p>Keranjang kamu masih kosong</p>
                <a href="{{ route('pelanggan.menu') }}" class="btn-ke-menu">
                    <i class="bi bi-egg-fried"></i> Lihat Menu
                </a>
            </div>
        @else

            {{-- Heading --}}
            <div class="keranjang-count">
                Pesananmu ({{ $keranjang->count() }} item)
            </div>

            {{-- Item List --}}
            @foreach($keranjang as $item)
                <div class="item-card">

                    {{-- Foto --}}
                    @if($item->menu?->photo)
                        <img src="{{ Storage::url($item->menu->photo) }}" alt="{{ $item->menu->name_menu }}">
                    @else
                        <div class="item-img-ph"><i class="bi bi-egg-fried"></i></div>
                    @endif

                    {{-- Info --}}
                    <div class="item-info">
                        <div class="item-name">{{ $item->menu->name_menu ?? '-' }}</div>

                        {{-- Qty stepper --}}
                        <div class="item-qty-row">
                            {{-- Kurang --}}
                            <form action="{{ route('pelanggan.keranjang.update', $item->kd_detail) }}" method="POST"
                                style="margin:0;">
                                @csrf @method('PUT')
                                <input type="hidden" name="jumlah" value="{{ max(1, $item->total - 1) }}">
                                <button type="submit" class="qty-btn-circle">−</button>
                            </form>

                            <span class="qty-num">{{ $item->total }}</span>

                            {{-- Tambah --}}
                            <form action="{{ route('pelanggan.keranjang.update', $item->kd_detail) }}" method="POST"
                                style="margin:0;">
                                @csrf @method('PUT')
                                <input type="hidden" name="jumlah" value="{{ $item->total + 1 }}">
                                <button type="submit" class="qty-btn-circle">+</button>
                            </form>

                            <span class="item-unit">pcs</span>
                        </div>

                        <div class="item-price">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</div>
                    </div>

                    {{-- Hapus --}}
                    <form action="{{ route('pelanggan.keranjang.hapus', $item->kd_detail) }}" method="POST" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-hapus-item" onclick="return confirm('Hapus item ini?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            @endforeach

            {{-- Catatan --}}
            <div class="extra-card">
                <label>Catatan untuk dapur (opsional)</label>
                <textarea id="catatanInput" rows="2" placeholder="Contoh: tidak pedas"></textarea>
            </div>

            {{-- Meja --}}
            <div class="extra-card">
                <strong>Meja:</strong>
                Meja {{ session('pelanggan.no_meja') ?? session('pelanggan_no_meja') ?? '-' }}
            </div>

            {{-- Footer Total + Bayar --}}
            <div class="keranjang-footer">
                <div class="footer-label">Total Bayar</div>
                <div class="footer-total">
                    Rp. {{ number_format($keranjang->sum('sub_total'), 0, ',', '.') }}
                </div>
                <form action="{{ route('pelanggan.checkout') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="keterangan" id="keteranganHidden">
                    <button type="submit" class="btn-bayar" onclick="setKeterangan()">
                        <i class="bi bi-credit-card-fill"></i> Lanjut Pembayaran
                    </button>
                </form>
            </div>

        @endif

    </div>

    @push('scripts')
        <script>
            function setKeterangan() {
                const catatan = document.getElementById('catatanInput');
                const hidden = document.getElementById('keteranganHidden');
                if (catatan && hidden) hidden.value = catatan.value;
            }
        </script>
    @endpush

@endsection