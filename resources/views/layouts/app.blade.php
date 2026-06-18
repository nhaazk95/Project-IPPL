<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur Nusantara')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<div class="app-layout">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                @else 🏠 @endif
            </div>
            <div>
                <div class="logo-text">Dapur Nusantara</div>
                <div class="logo-sub">@auth {{ auth()->user()->isAdmin() ? 'Panel Admin' : 'Panel Kasir' }} @endauth</div>
            </div>
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" type="button" title="Tutup/Buka Menu">
                <i class="fa-solid fa-angles-left"></i>
            </button>
        </div>

        <div class="sidebar-user-info">
            <div class="sidebar-avatar" style="overflow:hidden;">
                @if(auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}"
                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                @else
                    <i class="fa-solid fa-user-tie"></i>
                @endif
            </div>
            <div class="sidebar-user-name">{{ auth()->user()->name ?? '-' }}</div>
            <div class="sidebar-user-role">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
        </div>

        <nav class="sidebar-nav">
            @auth
            @if(auth()->user()->isAdmin())
                <div class="nav-section-label">Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
                </a>
                <div class="nav-section-label">Manajemen</div>
                <a href="{{ route('admin.level.index') }}" class="nav-item {{ request()->routeIs('admin.level.*') ? 'active' : '' }}" data-label="Level">
                    <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span> Level
                </a>
                <a href="{{ route('admin.menu.index') }}" class="nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}" data-label="Data Menu">
                    <span class="nav-icon"><i class="fa-solid fa-utensils"></i></span> Data Menu
                </a>
                <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" data-label="Kategori">
                    <span class="nav-icon"><i class="fa-solid fa-layer-group"></i></span> Kategori
                </a>
                <a href="{{ route('admin.meja.index') }}" class="nav-item {{ request()->routeIs('admin.meja.*') ? 'active' : '' }}" data-label="Meja">
                    <span class="nav-icon"><i class="fa-solid fa-chair"></i></span> Meja
                </a>
                <a href="{{ route('admin.transaksi.index') }}" class="nav-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" data-label="Transaksi">
                    <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span> Transaksi
                </a>

                <div class="nav-section-label">Laporan</div>
                {{-- Laporan accordion --}}
                <div class="nav-group" id="navLaporanGroup">
                    <div class="nav-item nav-parent {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
                        onclick="toggleNavGroup('navLaporanSub')" style="cursor:pointer;justify-content:space-between;" data-label="Laporan">
                        <span style="display:flex;align-items:center;gap:11px;">
                            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span> Laporan
                        </span>
                        <i class="fa-solid fa-chevron-down" id="navLaporanChevron"
                            style="font-size:10px;color:rgba(201,162,39,.5);transition:transform .2s;
                            {{ request()->routeIs('admin.laporan.*') ? 'transform:rotate(180deg);' : '' }}"></i>
                    </div>
                    <div id="navLaporanSub" style="{{ request()->routeIs('admin.laporan.*') ? '' : 'display:none;' }}">
                        <a href="{{ route('admin.laporan.transaksi') }}"
                            class="nav-item nav-sub {{ request()->routeIs('admin.laporan.transaksi') ? 'active' : '' }}">
                            <span class="nav-icon" style="font-size:11px;"><i class="fa-solid fa-receipt"></i></span>
                            Kelola Transaksi
                        </a>
                        <a href="{{ route('admin.laporan.orderan') }}"
                            class="nav-item nav-sub {{ request()->routeIs('admin.laporan.orderan') ? 'active' : '' }}">
                            <span class="nav-icon" style="font-size:11px;"><i class="fa-solid fa-clipboard-list"></i></span>
                            Orderan per Periode
                        </a>
                    </div>
                </div>
            @elseif(auth()->user()->isKasir())
                <div class="nav-section-label">Menu Kasir</div>
                <a href="{{ route('kasir.dashboard') }}" class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
                </a>
                <a href="{{ route('kasir.transaksi.index') }}" class="nav-item {{ request()->routeIs('kasir.transaksi*') ? 'active' : '' }}" data-label="Transaksi">
                    <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span> Transaksi
                </a>
                <a href="{{ route('kasir.laporan') }}" class="nav-item {{ request()->routeIs('kasir.laporan') ? 'active' : '' }}" data-label="Laporan">
                    <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span> Laporan
                </a>
            @endif
            @endauth
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user" onclick="document.getElementById('logoutModal').classList.add('active')" title="Klik untuk keluar">
                <div class="user-avatar" style="overflow:hidden;">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="user-name">{{ auth()->user()->name ?? '-' }}</div>
                    <div class="user-role">Klik untuk keluar</div>
                </div>
                <i class="fa-solid fa-right-from-bracket" style="color:rgba(201,162,39,.4);font-size:12px;flex-shrink:0;"></i>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="main-content" id="mainContent">

        {{-- ===== TOPBAR ===== --}}
        <header class="topbar">
            <button class="sidebar-expand-btn" id="sidebarExpandBtn" type="button" title="Buka Menu">
                <i class="fa-solid fa-angles-right"></i>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-actions">

                {{-- Jam --}}
                <div class="topbar-clock">
                    <i class="fa-regular fa-clock" style="color:var(--gold);"></i>
                    <span id="clock">{{ now()->format('H:i') }}</span>
                    <span style="color:rgba(201,162,39,.35);">·</span>
                    <span>{{ now()->isoFormat('D MMM Y') }}</span>
                </div>

                {{-- Global Search --}}
                <div class="tp-wrap" id="topbarSearchWrap">
                    <button class="topbar-icon-btn" onclick="toggleTopbarSearch(event)" title="Cari halaman / menu">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <div class="topbar-search-box" id="topbarSearchBox" style="width:300px;">
                        <i class="fa-solid fa-magnifying-glass" style="color:rgba(201,162,39,.6);font-size:13px;flex-shrink:0;"></i>
                        <input type="text" id="topbarSearchInput" placeholder="Cari menu / halaman..."
                            onkeydown="handleSearchKey(event)"
                            oninput="runGlobalSearch(this.value)" autocomplete="off">
                        <button onclick="closeTopbarSearch()" style="background:none;border:none;cursor:pointer;color:rgba(201,162,39,.5);padding:0;flex-shrink:0;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div id="searchResults" style="display:none;position:absolute;top:calc(100% + 10px);right:0;
                        width:300px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(44,24,16,.18);
                        border:1px solid var(--cream-dark);overflow:hidden;z-index:600;max-height:360px;overflow-y:auto;">
                    </div>
                </div>

                {{-- Bell (Kasir only) --}}
                @auth
                @if(auth()->user()->isKasir())
                <div class="tp-wrap" id="bellWrap">
                    <button class="topbar-icon-btn" id="bellBtn" onclick="toggleBellDropdown(event)" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="bell-badge" id="bellBadge" style="display:none;">0</span>
                    </button>
                    <div class="bell-dropdown" id="bellDropdown">
                        <div class="bell-dropdown-header">
                            <span><i class="fa-solid fa-bell" style="color:var(--gold);margin-right:6px;"></i>Notifikasi Order</span>
                            <button onclick="clearBellNotif()" style="background:none;border:none;font-size:11px;color:rgba(201,162,39,.6);cursor:pointer;">Tandai dibaca</button>
                        </div>
                        <div id="bellList"><div class="bell-empty">Tidak ada order baru</div></div>
                        <div class="bell-dropdown-footer"><a href="{{ route('kasir.order') }}">Lihat semua order →</a></div>
                    </div>
                </div>
                @endif
                @endauth

                {{-- Hamburger menu --}}
                <div class="tp-wrap" id="topbarMenuWrap">
                    <button class="topbar-icon-btn" id="topbarMenuBtn" onclick="toggleTopbarMenu(event)" title="Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="topbar-dropdown" id="topbarDropdown">
                        <div class="topbar-dropdown-profile">
                            <div class="topbar-dropdown-avatar" style="overflow:hidden;padding:0;">
                                @if(auth()->user()->foto)
                                    <img src="{{ asset('storage/' . auth()->user()->foto) }}"
                                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="topbar-dropdown-name" id="dropdownName">{{ auth()->user()->name ?? '-' }}</div>
                                <div class="topbar-dropdown-role">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
                            </div>
                        </div>
                        <div class="topbar-dropdown-divider"></div>
                        <a href="#" class="topbar-dropdown-item" onclick="showProfileModal(event); return false;">
                            <i class="fa-solid fa-user-circle"></i> Profil Saya
                        </a>
                        <div class="topbar-dropdown-divider"></div>
                        <a href="#" class="topbar-dropdown-item danger"
                            onclick="document.getElementById('logoutModal').classList.add('active'); closeTopbarMenu(); return false;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </a>
                    </div>
                </div>

            </div>
        </header>

        {{-- Breadcrumb --}}
        @hasSection('breadcrumb')
        <div style="padding:10px 26px;background:#fff;border-bottom:1px solid var(--cream-dark);">
            <div class="breadcrumb-bar">@yield('breadcrumb')</div>
        </div>
        @endif

        {{-- Flash --}}
        @if(session('success') || session('error') || $errors->any())
        <div style="padding:14px 26px 0;">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
        </div>
        @endif

        <main class="page-content">@yield('content')</main>
    </div>
</div>

{{-- LOGOUT MODAL --}}
<div class="modal-backdrop" id="logoutModal">
    <div class="modal-box" style="max-width:360px;">
        <div class="modal-body" style="text-align:center;padding:36px 28px 24px;">
            <div style="width:72px;height:72px;border-radius:50%;border:3px solid var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:28px;color:var(--gold);">
                <i class="fa-solid fa-exclamation"></i>
            </div>
            <h4 style="font-size:20px;font-weight:700;color:var(--text-dark);margin-bottom:8px;">Keluar?</h4>
            <p style="color:var(--text-light);font-size:14px;margin-bottom:28px;">Yakin ingin mengakhiri sesi?</p>
            <div style="display:flex;gap:14px;justify-content:center;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-gold" style="padding:10px 32px;">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
                <button class="btn-brown" style="padding:10px 32px;" onclick="document.getElementById('logoutModal').classList.remove('active')">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- PROFIL MODAL --}}
<div class="modal-backdrop" id="profileModal">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-circle" style="margin-right:8px;"></i>Profil Saya</span>
            <button class="modal-close" onclick="closeProfileModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="padding:20px 22px;">
            <div id="profileAlert" style="display:none;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;"></div>

            {{-- Foto profil --}}
            <div style="text-align:center;margin-bottom:20px;">
                <div style="position:relative;display:inline-block;">
                    @if(auth()->user()->foto)
                        <img id="profilePreview" src="{{ asset('storage/' . auth()->user()->foto) }}"
                            style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--brown);">
                    @else
                        <div id="profilePreview" style="width:80px;height:80px;border-radius:50%;background:var(--gold);color:var(--brown);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;border:3px solid var(--brown);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <label for="photoInput" style="position:absolute;bottom:0;right:0;width:26px;height:26px;
                        background:var(--brown);color:var(--gold);border-radius:50%;display:flex;
                        align-items:center;justify-content:center;font-size:11px;cursor:pointer;border:2px solid #fff;">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="photoInput" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                </div>
                <div style="font-weight:700;font-size:16px;color:var(--text-dark);margin-top:10px;" id="profileNameDisplay">{{ auth()->user()->name }}</div>
                <div style="font-size:12px;color:var(--text-light);margin-top:2px;">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="pName" class="form-control" value="{{ auth()->user()->name }}">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Username</label>
                    <input type="text" id="pUsername" class="form-control" value="{{ auth()->user()->username }}">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Email</label>
                    <input type="email" id="pEmail" class="form-control" value="{{ auth()->user()->email }}">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">No. HP</label>
                    <input type="text" id="pNoHp" class="form-control" placeholder="08xx..." value="{{ auth()->user()->no_hp ?? '' }}">
                </div>
            </div>

            <div style="border-top:1px solid var(--cream-dark);padding-top:12px;margin-bottom:4px;">
                <div style="font-size:11.5px;font-weight:700;color:var(--text-light);margin-bottom:8px;">
                    <i class="fa-solid fa-lock" style="margin-right:5px;"></i>
                    Ganti Password — kosongkan jika tidak ingin mengubah
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Password Baru</label>
                        <input type="password" id="pPassword" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" id="pPasswordConfirm" class="form-control" placeholder="Ulangi password">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeProfileModal()">Batal</button>
            <button class="btn-gold" onclick="saveProfile()">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </div>
    </div>
</div>

<div id="toast-container"></div>
<div id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('open');this.style.display='none';" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199;"></div>

<script>
// Clock — dipaksa ke WIB (Asia/Jakarta) terlepas dari timezone device,
// supaya konsisten untuk semua kasir/admin yang mengakses dari mana pun.
(function tick() {
    const el = document.getElementById('clock');
    if (el) {
        const parts = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false
        }).formatToParts(new Date());
        const h = parts.find(p => p.type === 'hour').value;
        const m = parts.find(p => p.type === 'minute').value;
        el.textContent = h + ':' + m;
    }
    setTimeout(tick, 1000);
})();

// ─── Sidebar Collapse / Expand (fully hides the sidebar) ──
(function () {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const expandBtn = document.getElementById('sidebarExpandBtn');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !main || !collapseBtn || !expandBtn) return;

    const isMobile = () => window.innerWidth <= 768;

    function closeSidebar() {
        sidebar.classList.add('collapsed');
        sidebar.classList.remove('open');
        main.classList.add('collapsed');
        expandBtn.classList.add('show');
        if (overlay) overlay.style.display = 'none';
        if (!isMobile()) localStorage.setItem('dnusaSidebarCollapsed', '1');
    }

    function openSidebar() {
        sidebar.classList.remove('collapsed');
        main.classList.remove('collapsed');
        expandBtn.classList.remove('show');
        if (isMobile()) {
            sidebar.classList.add('open');
            if (overlay) overlay.style.display = 'block';
        }
        if (!isMobile()) localStorage.setItem('dnusaSidebarCollapsed', '0');
    }

    // Restore saved preference (desktop only — mobile always starts closed)
    if (!isMobile() && localStorage.getItem('dnusaSidebarCollapsed') === '1') {
        closeSidebar();
    } else if (isMobile()) {
        closeSidebar();
    }

    collapseBtn.addEventListener('click', closeSidebar);
    expandBtn.addEventListener('click', openSidebar);

    window.addEventListener('resize', function () {
        if (isMobile()) {
            closeSidebar();
        } else {
            if (localStorage.getItem('dnusaSidebarCollapsed') === '1') closeSidebar();
            else openSidebar();
        }
    });
})();

// Toast
window.showToast = function(msg, type='success') {
    const tc = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast '+type;
    t.innerHTML = '<i class="fa-solid fa-'+(type==='success'?'circle-check':'circle-xmark')+'" style="margin-right:7px;"></i>'+msg;
    tc.appendChild(t);
    setTimeout(() => t.remove(), 3500);
};

// Modal backdrop
document.querySelectorAll('.modal-backdrop').forEach(b => {
    b.addEventListener('click', e => { if(e.target===b) b.classList.remove('active'); });
});

// Auto-dismiss flash
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(el => {
        el.style.transition='opacity .5s'; el.style.opacity='0';
        setTimeout(()=>el.remove(), 500);
    });
}, 4000);

// ─── Sidebar Laporan Accordion ──────────────────────────
function toggleNavGroup(subId) {
    const sub = document.getElementById(subId);
    const chevron = document.getElementById('navLaporanChevron');
    const isOpen = sub.style.display !== 'none';
    sub.style.display = isOpen ? 'none' : 'block';
    if (chevron) chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

// ─── Topbar Search ──────────────────────────────────────
function toggleTopbarSearch(e) {
    e && e.stopPropagation();
    const box = document.getElementById('topbarSearchBox');
    if (box.classList.contains('open')) { closeTopbarSearch(); }
    else {
        document.getElementById('topbarDropdown')?.classList.remove('open');
        document.getElementById('bellDropdown')?.classList.remove('open');
        box.classList.add('open');
        document.getElementById('searchResults').style.display = 'none';
        setTimeout(() => document.getElementById('topbarSearchInput')?.focus(), 150);
    }
}
function closeTopbarSearch() {
    document.getElementById('topbarSearchBox')?.classList.remove('open');
    document.getElementById('searchResults').style.display = 'none';
    const inp = document.getElementById('topbarSearchInput');
    if (inp) inp.value = '';
}
// ─── Global Navigation Search ─────────────────────────
@php
$searchItems = auth()->user()?->isAdmin() ? [
    ['label'=>'Dashboard',        'icon'=>'fa-gauge',             'url'=> route('admin.dashboard')],
    ['label'=>'Data Menu',        'icon'=>'fa-utensils',          'url'=> route('admin.menu.index')],
    ['label'=>'Kategori',         'icon'=>'fa-tags',              'url'=> route('admin.kategori.index')],
    ['label'=>'Meja',             'icon'=>'fa-chair',             'url'=> route('admin.meja.index')],
    ['label'=>'Level / Role',     'icon'=>'fa-shield-halved',     'url'=> route('admin.level.index')],
    ['label'=>'Transaksi',        'icon'=>'fa-cash-register',     'url'=> route('admin.transaksi.index')],
    ['label'=>'Laporan Orderan',  'icon'=>'fa-file-lines',        'url'=> route('admin.laporan.orderan')],
    ['label'=>'Laporan Transaksi','icon'=>'fa-file-invoice-dollar','url'=> route('admin.laporan.transaksi')],
] : [
    ['label'=>'Dashboard',        'icon'=>'fa-gauge',             'url'=> route('kasir.dashboard')],
    ['label'=>'Transaksi / Bayar','icon'=>'fa-cash-register',     'url'=> route('kasir.transaksi.index')],
    ['label'=>'Kelola Order',     'icon'=>'fa-clipboard-list',    'url'=> route('kasir.order')],
    ['label'=>'Laporan',          'icon'=>'fa-file-lines',        'url'=> route('kasir.laporan')],
];
@endphp
const SEARCH_ITEMS = {!! json_encode($searchItems) !!};

function runGlobalSearch(q) {
    const res = document.getElementById('searchResults');
    if (!q || q.length < 1) { res.style.display = 'none'; return; }

    const filtered = SEARCH_ITEMS.filter(item =>
        item.label.toLowerCase().includes(q.toLowerCase())
    );

    if (filtered.length === 0) {
        res.innerHTML = '<div style="padding:14px 16px;font-size:13px;color:var(--text-light);text-align:center;">Tidak ditemukan</div>';
    } else {
        res.innerHTML = filtered.map((item, i) => `
            <a href="${item.url}" style="display:flex;align-items:center;gap:12px;padding:11px 16px;
                text-decoration:none;color:var(--text-dark);border-bottom:1px solid var(--cream-dark);
                transition:background .12s;font-size:13.5px;font-weight:600;"
                onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background=''"
                onclick="closeTopbarSearch()">
                <span style="width:30px;height:30px;border-radius:8px;background:var(--brown);
                    color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;">
                    <i class="fa-solid ${item.icon}"></i>
                </span>
                ${item.label}
            </a>`).join('');
    }
    res.style.display = 'block';
}

function handleSearchKey(e) {
    if (e.key === 'Escape') closeTopbarSearch();
    if (e.key === 'Enter') {
        const first = document.querySelector('#searchResults a');
        if (first) first.click();
    }
}

// ─── Hamburger Dropdown ─────────────────────────────────
function toggleTopbarMenu(e) {
    e && e.stopPropagation();
    const dd = document.getElementById('topbarDropdown');
    const open = dd.classList.contains('open');
    // close others
    document.getElementById('bellDropdown')?.classList.remove('open');
    closeTopbarSearch();
    dd.classList.toggle('open', !open);
}
function closeTopbarMenu() {
    document.getElementById('topbarDropdown')?.classList.remove('open');
}
function closeProfileModal() {
    document.getElementById('profileModal').classList.remove('active');
    document.getElementById('profileAlert').style.display = 'none';
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('profilePreview');
            // Replace with img if currently a div
            if (preview.tagName === 'DIV') {
                const img = document.createElement('img');
                img.id = 'profilePreview';
                img.style.cssText = 'width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--brown);';
                preview.parentNode.replaceChild(img, preview);
            }
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function saveProfile() {
    const alertEl = document.getElementById('profileAlert');
    alertEl.style.display = 'none';

    const pw  = document.getElementById('pPassword').value;
    const pwc = document.getElementById('pPasswordConfirm').value;
    if (pw && pw !== pwc) {
        alertEl.textContent = '✗ Konfirmasi password tidak cocok.';
        alertEl.style.cssText = 'display:block;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;background:#fde8e8;color:var(--danger);border:1px solid #f5c6c6;';
        return;
    }

    const fd = new FormData();
    fd.append('_method', 'POST');
    fd.append('name',     document.getElementById('pName').value);
    fd.append('username', document.getElementById('pUsername').value);
    fd.append('email',    document.getElementById('pEmail').value);
    fd.append('no_hp',    document.getElementById('pNoHp').value);
    if (pw) { fd.append('password', pw); fd.append('password_confirmation', pwc); }
    const photoFile = document.getElementById('photoInput').files[0];
    if (photoFile) fd.append('photo', photoFile);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    try {
        const resp = await fetch('{{ route("profil.update") }}', { method:'POST', body: fd });
        const data = await resp.json();

        if (resp.status === 422) {
            // Validation errors dari Laravel
            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Validasi gagal.');
            alertEl.textContent = '✗ ' + errors;
            alertEl.style.cssText = 'display:block;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;background:#fde8e8;color:var(--danger);border:1px solid #f5c6c6;';
            return;
        }

        if (data.success) {
            alertEl.textContent = '✓ ' + data.message;
            alertEl.style.cssText = 'display:block;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;background:#e6f7ee;color:var(--success);border:1px solid #b7e4c7;';
            if (data.name) updateSidebarName(data.name);
            if (data.photo_url) updateSidebarPhoto(data.photo_url);
            document.getElementById('pPassword').value = '';
            document.getElementById('pPasswordConfirm').value = '';
            document.getElementById('photoInput').value = '';
            showToast('Profil berhasil diperbarui!');
        } else {
            alertEl.textContent = '✗ ' + (data.message || 'Terjadi kesalahan.');
            alertEl.style.cssText = 'display:block;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;background:#fde8e8;color:var(--danger);border:1px solid #f5c6c6;';
        }
    } catch(e) {
        alertEl.textContent = '✗ Terjadi kesalahan. Coba lagi.';
        alertEl.style.cssText = 'display:block;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:14px;background:#fde8e8;color:var(--danger);border:1px solid #f5c6c6;';
    }
}

function updateSidebarPhoto(url) {
    const imgTag = `<img src="${url}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;

    // Sidebar avatar (tengah)
    const sidebarAvatar = document.querySelector('.sidebar-avatar');
    if (sidebarAvatar) { sidebarAvatar.style.padding = '0'; sidebarAvatar.innerHTML = imgTag; }

    // Sidebar footer avatar (bawah kiri)
    const footerAvatar = document.querySelector('.user-avatar');
    if (footerAvatar) { footerAvatar.style.padding = '0'; footerAvatar.innerHTML = imgTag; }

    // Dropdown topbar avatar (kanan atas)
    const dropdownAvatar = document.querySelector('.topbar-dropdown-avatar');
    if (dropdownAvatar) { dropdownAvatar.style.padding = '0'; dropdownAvatar.innerHTML = imgTag; }
}

function updateSidebarName(name) {
    const els = [
        document.querySelector('.sidebar-user-name'),
        document.querySelector('.user-name'),
        document.getElementById('dropdownName'),
        document.getElementById('profileNameDisplay'),
    ];
    els.forEach(el => { if (el) el.textContent = name; });
}

function showProfileModal(e) {
    e && e.stopPropagation();
    closeTopbarMenu();
    setTimeout(() => {
        document.getElementById('profileModal').classList.add('active');
    }, 50);
}

// Close on outside click
document.addEventListener('click', function(e) {
    if (!document.getElementById('topbarMenuWrap')?.contains(e.target))
        document.getElementById('topbarDropdown')?.classList.remove('open');
    if (!document.getElementById('bellWrap')?.contains(e.target))
        document.getElementById('bellDropdown')?.classList.remove('open');
    if (!document.getElementById('topbarSearchWrap')?.contains(e.target)) {
        document.getElementById('topbarSearchBox')?.classList.remove('open');
        document.getElementById('searchResults').style.display = 'none';
    }
});

// ─── Bell (Kasir only) ──────────────────────────────────
@auth
@if(auth()->user()->isKasir())
let knownOrderIds = JSON.parse(localStorage.getItem('dnusaKnownOrderIds') || 'null');
let bellInitialized = knownOrderIds !== null;
if (knownOrderIds === null) knownOrderIds = [];
let bellNotifList = JSON.parse(localStorage.getItem('dnusaBellNotif') || '[]');
if (bellNotifList.length) { renderBellList(); updateBellBadge(bellNotifList.length); }

function toggleBellDropdown(e) {
    e && e.stopPropagation();
    const dd = document.getElementById('bellDropdown');
    const open = dd.classList.contains('open');
    document.getElementById('topbarDropdown')?.classList.remove('open');
    closeTopbarSearch();
    if (!open) { dd.classList.add('open'); updateBellBadge(0); }
    else dd.classList.remove('open');
}
function updateBellBadge(c) {
    const b = document.getElementById('bellBadge');
    if (c>0){b.style.display='flex';b.textContent=c>9?'9+':c;}else b.style.display='none';
}
function clearBellNotif() {
    bellNotifList=[];
    localStorage.removeItem('dnusaBellNotif');
    document.getElementById('bellList').innerHTML='<div class="bell-empty">Tidak ada order baru</div>';
    updateBellBadge(0);
}
function fetchNotifOrder() {
    const params = new URLSearchParams({
        known_ids: knownOrderIds.join(','),
        initialized: bellInitialized ? '1' : '0',
    });
    fetch('{{ route("kasir.api.notif") }}?'+params.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json()).then(data=>{
        let gotNew = false;
        if (data.orders && data.orders.length > 0) {
            gotNew = true;
            data.orders.forEach(o => {
                bellNotifList.unshift({kd:o.kd_order, waktu:o.waktu});
            });
            if(bellNotifList.length>10)bellNotifList.length=10;
            localStorage.setItem('dnusaBellNotif', JSON.stringify(bellNotifList));
            renderBellList(); updateBellBadge(bellNotifList.length);
            if(!document.getElementById('bellDropdown')?.classList.contains('open')) {
                document.getElementById('bellBtn')?.classList.add('bell-ring');
                setTimeout(()=>document.getElementById('bellBtn')?.classList.remove('bell-ring'),2000);
            }
        }
        if (data.all_pending_ids) {
            knownOrderIds = data.all_pending_ids;
            localStorage.setItem('dnusaKnownOrderIds', JSON.stringify(knownOrderIds));
        }
        bellInitialized = true;
        // Dashboard & daftar order kasir dirender server-side, jadi statistik dan
        // kartu order tidak ikut update otomatis — refresh halaman supaya kasir
        // tidak perlu reload manual untuk melihat order baru. Notifikasi bell tetap
        // tersimpan di localStorage sehingga tidak hilang setelah reload.
        @if(request()->routeIs('kasir.dashboard') || request()->routeIs('kasir.order'))
        if (gotNew && !document.getElementById('bellDropdown')?.classList.contains('open')
            && !document.querySelector('.modal-backdrop.active')) {
            setTimeout(() => window.location.reload(), 2500);
        }
        @endif
    }).catch(()=>{});
}
function renderBellList() {
    const list=document.getElementById('bellList');
    if(!bellNotifList.length){list.innerHTML='<div class="bell-empty">Tidak ada order baru</div>';return;}
    list.innerHTML=bellNotifList.map(n=>`<a href="{{ route('kasir.order') }}" class="bell-item"><div class="bell-item-icon"><i class="fa-solid fa-clipboard-list"></i></div><div class="bell-item-info"><div class="bell-item-title">Order Baru Masuk</div><div class="bell-item-sub">${n.kd} · ${n.waktu}</div></div></a>`).join('');
}
setInterval(fetchNotifOrder,15000); setTimeout(fetchNotifOrder,2000);
@endif
@endauth
</script>
@stack('scripts')
</body>
</html>