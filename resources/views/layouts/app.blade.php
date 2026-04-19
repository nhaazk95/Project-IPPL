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
        </div>

        <div class="sidebar-user-info">
            <div class="sidebar-avatar"><i class="fa-solid fa-user-tie"></i></div>
            <div class="sidebar-user-name">{{ auth()->user()->name ?? '-' }}</div>
            <div class="sidebar-user-role">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
        </div>

        <nav class="sidebar-nav">
            @auth
            @if(auth()->user()->isAdmin())
                <div class="nav-section-label">Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
                </a>
                <div class="nav-section-label">Manajemen</div>
                <a href="{{ route('admin.level.index') }}" class="nav-item {{ request()->routeIs('admin.level.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span> Level
                </a>
                <a href="{{ route('admin.menu.index') }}" class="nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-utensils"></i></span> Data Menu
                </a>
                <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-layer-group"></i></span> Kategori
                </a>
                <a href="{{ route('admin.meja.index') }}" class="nav-item {{ request()->routeIs('admin.meja.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chair"></i></span> Meja
                </a>
                <a href="{{ route('admin.transaksi.index') }}" class="nav-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span> Transaksi
                </a>

                <div class="nav-section-label">Laporan</div>
                {{-- Laporan accordion --}}
                <div class="nav-group" id="navLaporanGroup">
                    <div class="nav-item nav-parent {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
                        onclick="toggleNavGroup('navLaporanSub')" style="cursor:pointer;justify-content:space-between;">
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
                <a href="{{ route('kasir.dashboard') }}" class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
                </a>
                <a href="{{ route('kasir.transaksi.index') }}" class="nav-item {{ request()->routeIs('kasir.transaksi*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span> Transaksi
                </a>
                <a href="{{ route('kasir.laporan') }}" class="nav-item {{ request()->routeIs('kasir.laporan') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span> Laporan
                </a>
            @endif
            @endauth
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user" onclick="document.getElementById('logoutModal').classList.add('active')" title="Klik untuk keluar">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="user-name">{{ auth()->user()->name ?? '-' }}</div>
                    <div class="user-role">Klik untuk keluar</div>
                </div>
                <i class="fa-solid fa-right-from-bracket" style="color:rgba(201,162,39,.4);font-size:12px;flex-shrink:0;"></i>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="main-content">

        {{-- ===== TOPBAR ===== --}}
        <header class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-actions">

                {{-- Jam --}}
                <div class="topbar-clock">
                    <i class="fa-regular fa-clock" style="color:var(--gold);"></i>
                    <span id="clock">{{ now()->format('H:i') }}</span>
                    <span style="color:rgba(201,162,39,.35);">·</span>
                    <span>{{ now()->isoFormat('D MMM Y') }}</span>
                </div>

                {{-- Search --}}
                <div class="tp-wrap" id="topbarSearchWrap">
                    <button class="topbar-icon-btn" onclick="toggleTopbarSearch(event)" title="Cari">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <div class="topbar-search-box" id="topbarSearchBox">
                        <i class="fa-solid fa-magnifying-glass" style="color:rgba(201,162,39,.6);font-size:13px;flex-shrink:0;"></i>
                        <input type="text" id="topbarSearchInput" placeholder="Cari di halaman ini..."
                            onkeydown="if(event.key==='Escape')closeTopbarSearch()"
                            oninput="runTopbarSearch(this.value)">
                        <button onclick="closeTopbarSearch()" style="background:none;border:none;cursor:pointer;color:rgba(201,162,39,.5);padding:0;flex-shrink:0;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
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
                            <div class="topbar-dropdown-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="topbar-dropdown-name">{{ auth()->user()->name ?? '-' }}</div>
                                <div class="topbar-dropdown-role">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
                            </div>
                        </div>
                        <div class="topbar-dropdown-divider"></div>
                        <a href="#" class="topbar-dropdown-item" onclick="showProfileModal(); return false;">
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
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-circle" style="margin-right:8px;"></i>Profil Saya</span>
            <button class="modal-close" onclick="document.getElementById('profileModal').classList.remove('active')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div style="text-align:center;margin-bottom:22px;">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--gold);color:var(--brown);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;margin:0 auto 12px;border:3px solid var(--brown);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div style="font-weight:700;font-size:17px;color:var(--text-dark);">{{ auth()->user()->name }}</div>
                <div style="font-size:12px;color:var(--text-light);margin-top:3px;">{{ auth()->user()->level->nama_level ?? 'User' }}</div>
            </div>
            <div class="profil-row"><span class="profil-label"><i class="fa-solid fa-id-badge"></i> Kode User</span><span class="profil-val" style="font-family:monospace;">{{ auth()->user()->kd_user }}</span></div>
            <div class="profil-row"><span class="profil-label"><i class="fa-solid fa-user"></i> Username</span><span class="profil-val">{{ auth()->user()->username }}</span></div>
            <div class="profil-row"><span class="profil-label"><i class="fa-solid fa-envelope"></i> Email</span><span class="profil-val">{{ auth()->user()->email }}</span></div>
            <div class="profil-row" style="border:none;"><span class="profil-label"><i class="fa-solid fa-shield-halved"></i> Level</span><span class="profil-val"><span class="badge-level">{{ auth()->user()->level->nama_level ?? '-' }}</span></span></div>
        </div>
        <div class="modal-footer">
            <button class="btn-brown" onclick="document.getElementById('profileModal').classList.remove('active')">Tutup</button>
        </div>
    </div>
</div>

<div id="toast-container"></div>
<div id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('open');this.style.display='none';" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199;"></div>

<script>
// Clock
(function tick() {
    const el = document.getElementById('clock');
    if (el) { const n = new Date(); el.textContent = String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0'); }
    setTimeout(tick, 1000);
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
        // close other dropdowns
        document.getElementById('topbarDropdown')?.classList.remove('open');
        document.getElementById('bellDropdown')?.classList.remove('open');
        box.classList.add('open');
        setTimeout(() => document.getElementById('topbarSearchInput')?.focus(), 150);
    }
}
function closeTopbarSearch() {
    const box = document.getElementById('topbarSearchBox');
    box?.classList.remove('open');
    const inp = document.getElementById('topbarSearchInput');
    if (inp) { inp.value=''; runTopbarSearch(''); }
}
function runTopbarSearch(q) {
    document.querySelectorAll('table tbody tr').forEach(row => {
        row.style.display = (!q || row.textContent.toLowerCase().includes(q.toLowerCase())) ? '' : 'none';
    });
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
function showProfileModal() {
    closeTopbarMenu();
    document.getElementById('profileModal').classList.add('active');
}

// Close on outside click
document.addEventListener('click', function(e) {
    if (!document.getElementById('topbarMenuWrap')?.contains(e.target))
        document.getElementById('topbarDropdown')?.classList.remove('open');
    if (!document.getElementById('bellWrap')?.contains(e.target))
        document.getElementById('bellDropdown')?.classList.remove('open');
    if (!document.getElementById('topbarSearchWrap')?.contains(e.target)) {
        // intentionally don't auto-close search
    }
});

// ─── Bell (Kasir only) ──────────────────────────────────
@auth
@if(auth()->user()->isKasir())
let lastOrderKd='', bellNotifList=[];
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
    document.getElementById('bellList').innerHTML='<div class="bell-empty">Tidak ada order baru</div>';
    updateBellBadge(0);
}
function fetchNotifOrder() {
    fetch('{{ route("kasir.api.notif") }}?last_id='+encodeURIComponent(lastOrderKd),{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json()).then(data=>{
        if(data.latest_kd && data.latest_kd!==lastOrderKd && lastOrderKd!=='') {
            bellNotifList.unshift({kd:data.latest_kd,waktu:new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})});
            if(bellNotifList.length>10)bellNotifList.pop();
            renderBellList(); updateBellBadge(bellNotifList.length);
            if(!document.getElementById('bellDropdown')?.classList.contains('open')) {
                document.getElementById('bellBtn')?.classList.add('bell-ring');
                setTimeout(()=>document.getElementById('bellBtn')?.classList.remove('bell-ring'),2000);
            }
        }
        if(data.latest_kd)lastOrderKd=data.latest_kd;
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
