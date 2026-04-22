<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur Nusantara')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --coklat:    #2c1810;
            --emas:      #c9a227;
            --emas-light:#d4b040;
            --krem:      #faf5ee;
            --krem-dark: #f0e8d8;
            --teks-muted:#7a6552;
            --orange:    #c9a227;
            --brown:     #2c1810;
            --cream-dark:#f0e8d8;
            --bottombar: 68px;
        }
        html { font-size: 14px; -webkit-font-smoothing: antialiased; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #FAFAFA;
            color: var(--coklat);
            min-height: 100vh;
            padding-bottom: var(--bottombar);
        }

        /* ── Topbar pelanggan ── */
        .topbar {
            background: var(--coklat);
            height: 56px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(44,24,16,.25);
        }
        .topbar-back {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
            text-decoration: none;
            flex-shrink: 0;
            transition: background .2s;
        }
        .topbar-back:hover { background: rgba(255,255,255,.22); }
        .topbar-icon {
            color: var(--emas);
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .topbar-title {
            color: var(--emas);
            font-weight: 700;
            font-size: .95rem;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Bottom Nav ── */
        .pelanggan-bottombar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: var(--bottombar);
            background: var(--coklat);
            display: flex;
            align-items: center;
            z-index: 100;
            border-top: 2px solid rgba(201,162,39,.2);
        }
        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 8px 4px;
            text-decoration: none;
            color: rgba(255,255,255,.45);
            font-size: .68rem;
            font-weight: 600;
            transition: color .2s;
            cursor: pointer;
            border: none;
            background: none;
        }
        .bottom-nav-item .icon { font-size: 1.35rem; }
        .bottom-nav-item.active { color: var(--emas); }
        .bottom-nav-item:hover  { color: rgba(201,162,39,.8); }

        /* ── Fix btn-ke-menu ── */
        .btn-ke-menu {
            background: var(--emas) !important;
            color: var(--coklat) !important;
            border: none !important;
            border-radius: 50px !important;
            padding: .6rem 1.5rem !important;
            font-size: .85rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            font-family: inherit !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: .4rem !important;
            width: fit-content !important;
            max-width: fit-content !important;
            white-space: nowrap !important;
            line-height: 1 !important;
        }

        /* ── Toast ── */
        #toast-container {
            position: fixed;
            bottom: calc(var(--bottombar) + 12px);
            left: 50%; transform: translateX(-50%);
            display: flex; flex-direction: column; gap: 8px;
            z-index: 9999; width: 90%; max-width: 380px;
        }
        .toast {
            padding: 10px 16px; border-radius: 10px;
            font-size: .85rem; font-weight: 600;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            animation: slideUp .3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* ── Menu Card ── */
        .menu-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(44,24,16,.07);
            transition: all .2s;
            cursor: pointer;
        }
        .menu-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(44,24,16,.13); }
        .menu-card-img {
            height: 150px; background: var(--krem-dark);
            overflow: hidden; position: relative;
        }
        .menu-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .menu-card-body { padding: 14px; }
        .menu-card-name { font-weight: 700; font-size: 14px; color: var(--coklat); margin-bottom: 4px; }
        .menu-card-desc { font-size: 12px; color: var(--teks-muted); margin-bottom: 10px; display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
        .menu-card-footer { display: flex; align-items: center; justify-content: space-between; }
        .menu-price { font-weight: 800; font-size: 15px; color: var(--emas); }
        .btn-add {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--coklat); color: var(--emas); border: none;
            font-size: 18px; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            transition: all .2s;
        }
        .btn-add:hover { background: var(--emas); color: var(--coklat); transform: scale(1.1); }

        /* ── Qty controls ── */
        .qty-control { display: flex; align-items: center; gap: 8px; }
        .qty-btn {
            width: 30px; height: 30px; border-radius: 50%;
            border: 2px solid var(--emas); background: #fff;
            color: var(--emas); font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all .2s;
        }
        .qty-btn:hover { background: var(--emas); color: #fff; }
        .qty-num { font-weight: 700; font-size: 15px; min-width: 24px; text-align: center; color: var(--coklat); }
    </style>
    @stack('styles')
</head>
<body>

@yield('content')

{{-- Bottom Navigation --}}
@php $cartCount = session('keranjang_count', 0); @endphp
<nav class="pelanggan-bottombar">
    <a href="{{ route('pelanggan.beranda') }}"
        class="bottom-nav-item {{ request()->routeIs('pelanggan.beranda') ? 'active' : '' }}">
        <span class="icon"><i class="bi bi-house-fill"></i></span>
        Beranda
    </a>
    <a href="{{ route('pelanggan.menu') }}"
        class="bottom-nav-item {{ request()->routeIs('pelanggan.menu*') ? 'active' : '' }}">
        <span class="icon"><i class="bi bi-egg-fried"></i></span>
        Menu
    </a>
    <a href="{{ route('pelanggan.keranjang') }}"
        class="bottom-nav-item {{ request()->routeIs('pelanggan.keranjang') ? 'active' : '' }}"
        style="position:relative;">
        <span class="icon"><i class="bi bi-cart3"></i></span>
        Keranjang
        @if($cartCount > 0)
            <span style="position:absolute;top:4px;left:50%;margin-left:6px;
                background:#e74c3c;color:#fff;border-radius:10px;
                font-size:.6rem;font-weight:700;padding:1px 5px;min-width:16px;text-align:center;">
                {{ $cartCount }}
            </span>
        @endif
    </a>
    <a href="#" class="bottom-nav-item"
        onclick="document.getElementById('logoutModal').classList.add('show');return false;">
        <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
        Keluar
    </a>
</nav>

{{-- Logout Modal --}}
<div id="logoutModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;
    align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:2rem 1.5rem;max-width:320px;width:90%;text-align:center;margin:auto;">
        <div style="width:72px;height:72px;border-radius:50%;border:3px solid var(--emas);
            display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;
            font-size:1.8rem;color:var(--emas);">
            <i class="bi bi-exclamation"></i>
        </div>
        <h5 style="font-weight:800;color:var(--coklat);margin-bottom:.5rem;">Keluar?</h5>
        <p style="color:var(--teks-muted);font-size:.875rem;margin-bottom:1.5rem;">
            Yakin ingin mengakhiri sesi?
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <form method="POST" action="{{ route('pelanggan.logout') }}">
                @csrf
                <button type="submit" style="background:var(--coklat);color:var(--emas);
                    border:none;border-radius:10px;padding:.65rem 2rem;font-weight:700;
                    font-size:.9rem;cursor:pointer;">OK</button>
            </form>
            <button onclick="document.getElementById('logoutModal').classList.remove('show')"
                style="background:var(--krem-dark);color:var(--coklat);border:none;
                border-radius:10px;padding:.65rem 2rem;font-weight:700;font-size:.9rem;cursor:pointer;">
                NO
            </button>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
const lm = document.getElementById('logoutModal');
if (lm) {
    lm.addEventListener('click', e => { if (e.target === lm) lm.classList.remove('show'); });
}
const style = document.createElement('style');
style.textContent = '#logoutModal.show { display:flex !important; }';
document.head.appendChild(style);

window.showToast = function(msg, type='success') {
    const tc = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast';
    t.style.borderLeft = '4px solid ' + (type==='success'?'#27ae60':'#e74c3c');
    t.innerHTML = (type==='success'?'✅':'❌') + ' ' + msg;
    tc.appendChild(t);
    setTimeout(() => t.remove(), 3000);
};
</script>
@stack('scripts')
</body>
</html>