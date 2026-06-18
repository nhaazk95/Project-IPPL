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
            --sidebar-w:   260px;
            --sidebar-w-collapsed: 76px;
            --header-h:    60px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-main); min-height: 100vh; display: flex; }

        .sidebar {
            width: var(--sidebar-w);
            background: #D6C9B4; /* lighter for kasir */
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: width 0.25s ease;
            overflow: hidden;
        }
        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }
        .sidebar-header {
            background: var(--brown-mid);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            height: var(--header-h);
        }
        .sidebar-header .brand { color: var(--gold); font-size: 15px; font-weight: 700; white-space: nowrap; opacity: 1; transition: opacity 0.15s ease; }
        .sidebar.collapsed .sidebar-header .brand { opacity: 0; width: 0; }

        .sidebar-toggle {
            margin-left: auto;
            background: none;
            border: none;
            color: rgba(255,255,255,0.75);
            cursor: pointer;
            font-size: 16px;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: none;
            transition: background 0.2s, transform 0.25s ease;
        }
        .sidebar-toggle:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .sidebar.collapsed .sidebar-toggle { transform: rotate(180deg); margin-left: 0; }

        .user-box {
            padding: 22px 18px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            white-space: nowrap;
        }
        .user-avatar {
            width: 64px; height: 64px;
            background: var(--brown-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin-bottom: 10px;
            flex: none;
        }
        .user-name { color: var(--brown-main); font-size: 14px; font-weight: 600; }
        .user-role { color: #888; font-size: 12px; }
        .sidebar.collapsed .user-avatar { width: 40px; height: 40px; font-size: 20px; margin-bottom: 0; }
        .sidebar.collapsed .user-name,
        .sidebar.collapsed .user-role { display: none; }

        .nav { flex: 1; padding: 10px 0; overflow-y: auto; overflow-x: hidden; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 20px;
            color: rgba(60,30,0,0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }
        .nav-item:hover { background: rgba(0,0,0,0.05); color: var(--brown-dark); }
        .nav-item.active { background: var(--brown-main); color: #fff; border-left-color: var(--gold); }
        .nav-icon { font-size: 16px; width: 20px; text-align: center; flex: none; }
        .nav-divider { height: 1px; background: rgba(0,0,0,0.07); margin: 6px 0; }
        .nav-item .nav-label { transition: opacity 0.15s ease; }

        .sidebar.collapsed .nav-item { justify-content: center; padding: 13px 0; position: relative; }
        .sidebar.collapsed .nav-item .nav-label { display: none; }
        .sidebar.collapsed .nav-item:hover::after {
            content: attr(data-label);
            position: absolute;
            left: calc(var(--sidebar-w-collapsed) + 8px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--brown-dark);
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
            z-index: 150;
            pointer-events: none;
        }

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
            transition: left 0.25s ease;
        }
        .header.collapsed { left: var(--sidebar-w-collapsed); }
        .header-icon { font-size: 18px; color: rgba(255,255,255,0.8); cursor: pointer; }
        .header-icon:hover { color: var(--gold); }

        .main {
            margin-left: var(--sidebar-w);
            margin-top: var(--header-h);
            flex: 1;
            padding: 28px;
            transition: margin-left 0.25s ease;
        }
        .main.collapsed { margin-left: var(--sidebar-w-collapsed); }
        .breadcrumb { text-align: center; margin-bottom: 24px; font-size: 13px; color: #999; }
        .breadcrumb a { color: var(--brown-main); text-decoration: none; }

        .card { background: #fff; border-radius: 14px; padding: 22px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 22px; }
        .card-title { background: var(--brown-main); color: #fff; font-size: 15px; font-weight: 600; padding: 13px 20px; border-radius: 10px 10px 0 0; margin: -22px -24px 20px; }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: var(--brown-main); color: #fff; padding: 11px 14px; text-align: left; font-weight: 600; }
        td { padding: 11px 14px; border-bottom: 1px solid #f0e8dc; color: #333; }
        tr:nth-child(even) td { background: #faf5ef; }
        tr:hover td { background: #f3e9dc; cursor: pointer; }

        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: var(--brown-main); color: #fff; }
        .btn-gold    { background: var(--gold); color: #fff; }
        .btn-danger  { background: #dc3545; color: #fff; }
        .btn-outline { background: transparent; border: 1.5px solid var(--brown-main); color: var(--brown-main); }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--brown-main); margin-bottom: 6px; }
        .form-control { width: 100%; background: #F8F0E8; border: 1.5px solid #D4B896; border-radius: 10px; padding: 10px 14px; font-family: 'Poppins', sans-serif; font-size: 13px; color: #1a0a00; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: var(--brown-main); }

        /* Stat cards */
        .stat-card {
            background: #fff;
            border: 1.5px solid #E8D8C4;
            border-radius: 14px;
            padding: 22px;
            text-align: center;
        }
        .stat-num { font-size: 36px; font-weight: 700; color: var(--brown-main); }
        .stat-label { font-size: 13px; color: #888; margin-top: 4px; }

        /* Order card */
        .order-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #E8D8C4;
            padding: 18px 20px;
            margin-bottom: 16px;
        }
        .order-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .meja-badge {
            background: var(--brown-dark);
            color: #fff;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge {
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 11px;
            color: #888;
        }
        .order-item-row { display: flex; justify-content: space-between; font-size: 13px; padding: 3px 0; }
        .order-total { display: flex; justify-content: space-between; font-size: 13px; color: #666; border-top: 1px solid #f0e8dc; padding-top: 10px; margin-top: 8px; }
        .order-action { display: flex; justify-content: flex-end; margin-top: 12px; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal-box { background: #fff; border-radius: 16px; padding: 32px; width: 90%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; }
        .modal-icon { font-size: 48px; margin-bottom: 14px; }
        .modal-title { font-size: 22px; font-weight: 700; color: #1a0a00; margin-bottom: 8px; }
        .modal-msg { font-size: 14px; color: #666; margin-bottom: 24px; }
        .modal-btns { display: flex; justify-content: center; gap: 14px; }
        .modal-btns .btn { min-width: 90px; padding: 12px 28px; border-radius: 10px; font-size: 15px; }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: none; }
            .sidebar.collapsed { width: var(--sidebar-w); }
            .sidebar.collapsed .brand,
            .sidebar.collapsed .user-name,
            .sidebar.collapsed .user-role,
            .sidebar.collapsed .nav-item .nav-label { display: initial; opacity: 1; width: auto; }
            .sidebar.collapsed .user-avatar { width: 64px; height: 64px; font-size: 28px; margin-bottom: 10px; }
            .sidebar.collapsed .nav-item { justify-content: flex-start; padding: 13px 20px; }
            .header, .header.collapsed { left: 0; }
            .main, .main.collapsed { margin-left: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span style="font-size:22px;">🏠</span>
        <span class="brand">Dapur Nusantara</span>
        <button class="sidebar-toggle" id="sidebarToggle" title="Tutup/Buka Menu" type="button">◀</button>
    </div>

    <div class="user-box">
        <div class="user-avatar">👤</div>
        <div class="user-name">{{ auth()->user()->name ?? 'Kasir' }}</div>
        <div class="user-role">{{ auth()->user()->level->name ?? 'Kasir' }}</div>
    </div>

    <nav class="nav">
        <a href="{{ route('kasir.dashboard') }}" class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}" data-label="Dashboard">
            <span class="nav-icon">⊞</span> <span class="nav-label">Dashboard</span>
        </a>
        <div class="nav-divider"></div>
        <a href="{{ route('kasir.transaksi') }}" class="nav-item {{ request()->routeIs('kasir.transaksi') ? 'active' : '' }}" data-label="Transaksi">
            <span class="nav-icon">📋</span> <span class="nav-label">Transaksi</span>
        </a>
        <a href="{{ route('kasir.laporan') }}" class="nav-item {{ request()->routeIs('kasir.laporan') ? 'active' : '' }}" data-label="Laporan">
            <span class="nav-icon">📄</span> <span class="nav-label">Laporan</span>
        </a>
    </nav>
</aside>

<header class="header" id="mainHeader">
    <span class="header-icon">🔍</span>
    <span class="header-icon" onclick="showLogout()">☰</span>
</header>

<main class="main" id="mainContent">
    <div class="breadcrumb">@yield('breadcrumb')</div>

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<div class="modal-overlay" id="logoutModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Keluar?</div>
        <p class="modal-msg">Yakin ingin mengahiri sesi?</p>
        <div class="modal-btns">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gold" style="background:#C9A84C;">OK</button>
            </form>
            <button class="btn btn-gold" style="background:#7B5A1E;" onclick="hideLogout()">NO</button>
        </div>
    </div>
</div>

<script>
function showLogout() { document.getElementById('logoutModal').classList.add('show'); }
function hideLogout() { document.getElementById('logoutModal').classList.remove('show'); }

(function () {
    const sidebar = document.getElementById('sidebar');
    const header = document.getElementById('mainHeader');
    const main = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('sidebarToggle');

    function applyState(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        header.classList.toggle('collapsed', collapsed);
        main.classList.toggle('collapsed', collapsed);
    }

    const isMobile = () => window.innerWidth <= 768;

    const saved = localStorage.getItem('sidebarCollapsed') === '1';
    if (!isMobile()) {
        applyState(saved);
    }

    toggleBtn.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.toggle('open');
        } else {
            const collapsed = !sidebar.classList.contains('collapsed');
            applyState(collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
        }
    });
})();
</script>
@stack('scripts')
</body>
</html>