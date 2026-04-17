{{-- resources/views/layouts/pelanggan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DNUSA Resto')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        :root { --pelanggan-topbar: 64px; --pelanggan-bottombar: 68px; }
        body { background: #FAFAFA; padding-bottom: var(--pelanggan-bottombar); }

        /* Top Nav Pelanggan */
        .pelanggan-topbar {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--pelanggan-topbar);
            background: var(--brown);
            display: flex; align-items: center; padding: 0 20px;
            gap: 14px; z-index: 100;
            box-shadow: 0 2px 16px rgba(78,52,46,0.3);
        }
        .pelanggan-topbar .logo {
            font-family: var(--font-display);
            color: #fff; font-size: 20px; flex: 1;
        }
        .pelanggan-topbar .logo span { color: var(--orange-soft); font-size: 13px; display: block; font-family: var(--font-body); font-weight: 400; }
        .topbar-btn {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.1);
            border: none; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px; cursor: pointer;
            text-decoration: none; position: relative;
            transition: var(--transition);
        }
        .topbar-btn:hover { background: rgba(255,255,255,0.2); }
        .cart-count {
            position: absolute; top: -4px; right: -4px;
            background: var(--orange); color: #fff;
            width: 18px; height: 18px; border-radius: 50%;
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* Main content area */
        .pelanggan-content {
            margin-top: var(--pelanggan-topbar);
            min-height: calc(100vh - var(--pelanggan-topbar) - var(--pelanggan-bottombar));
        }

        /* Bottom Nav */
        .pelanggan-bottombar {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: var(--pelanggan-bottombar);
            background: #fff;
            display: flex; align-items: center;
            border-top: 1px solid var(--cream-dark);
            box-shadow: 0 -4px 20px rgba(78,52,46,0.08);
            z-index: 100;
        }
        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            gap: 4px; padding: 8px 4px; text-decoration: none;
            color: var(--brown-light); font-size: 11px; font-weight: 500;
            transition: var(--transition); cursor: pointer;
            border: none; background: none;
        }
        .bottom-nav-item .icon { font-size: 20px; }
        .bottom-nav-item.active, .bottom-nav-item:hover { color: var(--orange); }
        .bottom-nav-item.active .icon { transform: scale(1.1); }

        /* Menu Card Pelanggan */
        .menu-card {
            background: #fff;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            cursor: pointer;
        }
        .menu-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .menu-card-img {
            height: 150px; background: var(--cream-dark);
            overflow: hidden; position: relative;
        }
        .menu-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .menu-card-body { padding: 14px; }
        .menu-card-name { font-weight: 600; font-size: 14px; color: var(--brown); margin-bottom: 4px; }
        .menu-card-desc { font-size: 12px; color: var(--text-light); margin-bottom: 10px; display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
        .menu-card-footer { display: flex; align-items: center; justify-content: space-between; }
        .menu-price { font-weight: 700; font-size: 15px; color: var(--orange); }
        .btn-add {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--orange); color: #fff; border: none;
            font-size: 18px; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .btn-add:hover { background: var(--orange-dark); transform: scale(1.1); }

        /* Qty controls */
        .qty-control { display: flex; align-items: center; gap: 8px; }
        .qty-btn {
            width: 30px; height: 30px; border-radius: 50%;
            border: 2px solid var(--orange); background: #fff;
            color: var(--orange); font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .qty-btn:hover { background: var(--orange); color: #fff; }
        .qty-num { font-weight: 700; font-size: 15px; min-width: 24px; text-align: center; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Topbar --}}
<div class="pelanggan-topbar">
    <div class="logo">
        ☕ DNUSA
        <span>Meja {{ session('pelanggan.no_meja') ?? '-' }} · {{ session('pelanggan.name') ?? 'Pelanggan' }}</span>
    </div>
    <a href="{{ route('pelanggan.keranjang') }}" class="topbar-btn" title="Keranjang">
        <i class="fa-solid fa-bag-shopping"></i>
        @if(($cartCount = session('cart_count', 0)) > 0)
            <span class="cart-count">{{ $cartCount }}</span>
        @endif
    </a>
    <button class="topbar-btn" onclick="document.getElementById('logoutModal').classList.add('active')" title="Keluar">
        <i class="fa-solid fa-right-from-bracket"></i>
    </button>
</div>

<div class="pelanggan-content">
    @yield('content')
</div>

{{-- Bottom Nav --}}
<nav class="pelanggan-bottombar">
    <a href="{{ route('pelanggan.beranda') }}" class="bottom-nav-item {{ request()->routeIs('pelanggan.beranda') ? 'active' : '' }}">
        <span class="icon"><i class="fa-solid fa-house"></i></span> Beranda
    </a>
    <a href="{{ route('pelanggan.menu') }}" class="bottom-nav-item {{ request()->routeIs('pelanggan.menu') ? 'active' : '' }}">
        <span class="icon"><i class="fa-solid fa-utensils"></i></span> Menu
    </a>
    <a href="{{ route('pelanggan.keranjang') }}" class="bottom-nav-item {{ request()->routeIs('pelanggan.keranjang') ? 'active' : '' }}" style="position:relative;">
        <span class="icon"><i class="fa-solid fa-bag-shopping"></i></span> Keranjang
        @if($cartCount > 0)
            <span style="position:absolute;top:4px;left:50%;margin-left:4px;background:var(--orange);color:#fff;border-radius:10px;font-size:10px;font-weight:700;padding:1px 6px;">{{ $cartCount }}</span>
        @endif
    </a>
    <a href="{{ route('pelanggan.pesanan') }}" class="bottom-nav-item {{ request()->routeIs('pelanggan.pesanan') ? 'active' : '' }}">
        <span class="icon"><i class="fa-solid fa-clipboard-list"></i></span> Pesanan
    </a>
</nav>

{{-- Logout Modal --}}
<div class="modal-backdrop" id="logoutModal">
    <div class="modal-box" style="max-width:360px;">
        <div class="modal-header">
            <span class="modal-title">Keluar Sesi?</span>
            <button class="modal-close" onclick="document.getElementById('logoutModal').classList.remove('active')">✕</button>
        </div>
        <div class="modal-body" style="text-align:center;padding:28px;">
            <div style="font-size:40px;margin-bottom:12px;">👋</div>
            <p style="color:var(--text-light);">Terima kasih telah mengunjungi DNUSA Resto!</p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:14px;">
            <button class="btn-secondary" onclick="document.getElementById('logoutModal').classList.remove('active')">Batal</button>
            <form method="POST" action="{{ route('pelanggan.logout') }}">
                @csrf
                <button type="submit" class="btn-primary" style="background:var(--danger);">Keluar</button>
            </form>
        </div>
    </div>
</div>

<div id="toast-container"></div>
<script>
window.showToast = function(msg, type='success') {
    const tc = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.innerHTML = (type==='success'?'✅':'❌') + ' ' + msg;
    tc.appendChild(t);
    setTimeout(() => t.remove(), 3000);
};
document.querySelectorAll('.modal-backdrop').forEach(b =>
    b.addEventListener('click', e => { if (e.target === b) b.classList.remove('active'); })
);
</script>
@stack('scripts')
</body>
</html>
