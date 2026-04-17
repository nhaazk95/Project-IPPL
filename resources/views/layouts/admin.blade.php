<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') – Dapur Nusantara</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --brown-dark:  #3B1A08;
            --brown-mid:   #5C2E00;
            --brown-main:  #7B3F00;
            --brown-light: #B08060;
            --gold:        #C9A84C;
            --bg-main:     #F5EBD8;
            --bg-card:     #EDE0CD;
            --sidebar-w:   260px;
            --header-h:    60px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-main); min-height: 100vh; display: flex; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--brown-dark);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-header {
            background: var(--brown-mid);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            height: var(--header-h);
        }
        .sidebar-header .logo { font-size: 24px; }
        .sidebar-header .brand { color: var(--gold); font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }

        .user-box {
            padding: 22px 18px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .user-avatar {
            width: 64px; height: 64px;
            background: var(--brown-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .user-name { color: var(--gold); font-size: 14px; font-weight: 600; }
        .user-role { color: rgba(255,255,255,0.55); font-size: 12px; }

        .nav { flex: 1; padding: 10px 0; overflow-y: auto; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-item.active { background: var(--brown-main); color: #fff; border-left-color: var(--gold); }
        .nav-item .nav-icon { font-size: 16px; width: 20px; text-align: center; }
        .nav-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 6px 0; }
        .nav-group { position: relative; }
        .nav-sub { display: none; background: rgba(0,0,0,0.2); }
        .nav-sub.open { display: block; }
        .nav-sub .nav-item { padding-left: 52px; font-size: 12px; }
        .nav-item .chevron { margin-left: auto; font-size: 11px; transition: transform 0.2s; }
        .nav-item.open .chevron { transform: rotate(90deg); }

        /* ── HEADER ── */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--header-h);
            background: var(--brown-mid);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 24px;
            gap: 16px;
            z-index: 90;
        }
        .header-icon { font-size: 18px; color: rgba(255,255,255,0.8); cursor: pointer; }
        .header-icon:hover { color: var(--gold); }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            margin-top: var(--header-h);
            flex: 1;
            padding: 28px;
            min-height: calc(100vh - var(--header-h));
        }
        .breadcrumb {
            text-align: center;
            margin-bottom: 24px;
            font-size: 13px;
            color: #999;
        }
        .breadcrumb a { color: var(--brown-main); text-decoration: none; }
        .breadcrumb span { color: #555; }

        /* ── CARDS ── */
        .card {
            background: #fff;
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            margin-bottom: 22px;
        }
        .card-title {
            background: var(--brown-main);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 13px 20px;
            border-radius: 10px 10px 0 0;
            margin: -22px -24px 20px;
        }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th {
            background: var(--brown-main);
            color: #fff;
            padding: 11px 14px;
            text-align: left;
            font-weight: 600;
        }
        td { padding: 11px 14px; border-bottom: 1px solid #f0e8dc; color: #333; }
        tr:nth-child(even) td { background: #faf5ef; }
        tr:hover td { background: #f3e9dc; }

        /* ── BTNS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: var(--brown-main); color: #fff; }
        .btn-gold    { background: var(--gold); color: #fff; }
        .btn-danger  { background: #dc3545; color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ── FORM ── */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--brown-main); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            background: #F8F0E8;
            border: 1.5px solid #D4B896;
            border-radius: 10px;
            padding: 10px 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: #1a0a00;
            outline: none;
            transition: border 0.2s;
        }
        .form-control:focus { border-color: var(--brown-main); }
        select.form-control { cursor: pointer; }

        /* ── MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: 90%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .modal-icon { font-size: 48px; margin-bottom: 14px; }
        .modal-title { font-size: 22px; font-weight: 700; color: #1a0a00; margin-bottom: 8px; }
        .modal-msg { font-size: 14px; color: #666; margin-bottom: 24px; }
        .modal-btns { display: flex; justify-content: center; gap: 14px; }
        .modal-btns .btn { min-width: 90px; padding: 12px 28px; border-radius: 10px; font-size: 15px; }

        /* ── GRID ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 22px; }
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            border: 1px solid #e8ddd0;
        }
        .stat-num { font-size: 32px; font-weight: 700; }
        .stat-label { font-size: 13px; color: #888; margin-top: 4px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: none; }
            .header { left: 0; }
            .main { margin-left: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="logo">🏠</span>
        <span class="brand">Dapur Nusantara</span>
    </div>

    <div class="user-box">
        <div class="user-avatar">👤</div>
        <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
        <div class="user-role">{{ auth()->user()->level->nama_level ?? 'Admin' }}</div>
    </div>

    <nav class="nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">⊞</span> Dashboard
        </a>
        <div class="nav-divider"></div>
        <a href="{{ route('admin.level.index') }}" class="nav-item {{ request()->routeIs('admin.level.*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span> Level
        </a>
        <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
            <span class="nav-icon">☰</span> Kategori
        </a>
        <a href="{{ route('admin.menu.index') }}" class="nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <span class="nav-icon">🍴</span> Menu
        </a>
        <a href="{{ route('admin.meja.index') }}" class="nav-item {{ request()->routeIs('admin.meja.*') ? 'active' : '' }}">
            <span class="nav-icon">⊞+</span> Meja
        </a>
        <a href="{{ route('admin.transaksi.index') }}" class="nav-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span> Transaksi
        </a>
        <div class="nav-divider"></div>

        {{-- Laporan Group --}}
        <div class="nav-group">
            <div class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
                 onclick="toggleSub('laporan-sub', this)">
                <span class="nav-icon">📄</span> Laporan
                <span class="chevron {{ request()->routeIs('admin.laporan.*') ? 'open' : '' }}">▶</span>
            </div>
            <div class="nav-sub {{ request()->routeIs('admin.laporan.*') ? 'open' : '' }}" id="laporan-sub">
                <a href="{{ route('admin.laporan.orderan') }}" class="nav-item {{ request()->routeIs('admin.laporan.orderan') ? 'active' : '' }}">
                    Laporan Orderan
                </a>
                <a href="{{ route('admin.laporan.transaksi') }}" class="nav-item {{ request()->routeIs('admin.laporan.transaksi') ? 'active' : '' }}">
                    Laporan Transaksi
                </a>
            </div>
        </div>
    </nav>
</aside>

<!-- HEADER -->
<header class="header">
    <span class="header-icon" title="Cari">🔍</span>
    <span class="header-icon" title="Notifikasi">🔔</span>
    <span class="header-icon" title="Keluar" onclick="showLogout()">☰</span>
</header>

<!-- MAIN CONTENT -->
<main class="main">
    <div class="breadcrumb">
        @yield('breadcrumb')
    </div>

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<!-- LOGOUT MODAL -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Keluar?</div>
        <p class="modal-msg">Yakin ingin mengakhiri sesi?</p>
        <div class="modal-btns">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gold" style="background:#C9A84C;">OK</button>
            </form>
            <button class="btn btn-gold" style="background:#7B5A1E;" onclick="hideLogout()">Batal</button>
        </div>
    </div>
</div>

<script>
function showLogout() { document.getElementById('logoutModal').classList.add('show'); }
function hideLogout() { document.getElementById('logoutModal').classList.remove('show'); }
function toggleSub(id, el) {
    const sub = document.getElementById(id);
    sub.classList.toggle('open');
    el.classList.toggle('open');
}
</script>

@stack('scripts')
</body>
</html>