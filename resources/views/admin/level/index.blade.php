@extends('layouts.app')
@section('title', 'Level — Admin')
@section('page-title', 'Level')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Level</span>
@endsection

@section('content')

{{-- Alert --}}
@if(session('success'))
<div class="alert alert-success mb-16">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-16">
    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
</div>
@endif

{{-- ===== TABEL LEVEL ===== --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fa-solid fa-shield-halved"></i> Data Level
        </span>
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:11px;color:rgba(245,233,192,.45);">{{ $levels->count() }} level</span>
            <button class="btn-tambah-level" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-plus"></i> Tambah Level
            </button>
        </div>
    </div>

    {{-- Show & Search --}}
    <div style="padding:11px 16px;border-bottom:1px solid var(--cream-dark);
        display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-light);">
            Show
            <select id="showEntries" class="form-control" style="width:62px;padding:4px 6px;font-size:12px;">
                <option>10</option><option>25</option><option>50</option>
            </select>
            entries
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-light);">
            Search
            <input type="text" id="searchLevel" class="form-control"
                style="width:150px;padding:5px 10px;font-size:12px;"
                placeholder="Cari level..." oninput="filterLevel()">
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <table id="levelTable" class="dnusa-table">
            <thead>
                <tr>
                    <th style="width:60px;text-align:center;">No</th>
                    <th>Nama</th>
                    <th style="text-align:center;width:100px;">Pegawai</th>
                    <th style="text-align:center;width:140px;">Action</th>
                </tr>
            </thead>
            <tbody id="levelTbody">
                @forelse($levels as $i => $lv)
                @php
                    $icons = ['admin'=>'fa-crown','kasir'=>'fa-cash-register'];
                    $ic    = $icons[strtolower($lv->nama_level)] ?? 'fa-shield-halved';

                    // Siapkan data pegawai sebagai JSON untuk modal
                    $pegawaiData = $lv->users->map(fn($u) => [
                        'kd_user' => $u->kd_user,
                        'name'    => $u->name,
                        'is_me'   => auth()->user()->kd_user === $u->kd_user,
                        'delete_url' => route('admin.level.user.destroy', $u->kd_user),
                    ]);
                @endphp
                <tr class="level-row" data-nama="{{ strtolower($lv->nama_level) }}">
                    <td style="text-align:center;color:var(--text-light);font-weight:600;">
                        {{ $i + 1 }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:var(--brown);
                                color:var(--gold);display:flex;align-items:center;justify-content:center;
                                font-size:13px;flex-shrink:0;">
                                <i class="fa-solid {{ $ic }}"></i>
                            </div>
                            <span style="font-weight:700;font-size:14px;color:var(--text-dark);">
                                {{ $lv->nama_level }}
                            </span>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <button class="btn-icon-pegawai"
                            onclick="openModalPegawai('{{ $lv->nama_level }}', '{{ $ic }}', {{ json_encode($pegawaiData) }})"
                            title="Lihat daftar pegawai {{ $lv->nama_level }}">
                            <i class="fa-solid fa-users"></i>
                            <span class="badge-count">{{ $lv->users_count }}</span>
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <button class="btn-lv-edit"
                                onclick="openModalEdit('{{ $lv->id }}','{{ $lv->nama_level }}')">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form method="POST"
                                action="{{ route('admin.level.destroy', $lv->id) }}"
                                onsubmit="return confirm('Hapus level \'{{ $lv->nama_level }}\'?\nSemua user dengan level ini akan terpengaruh!')"
                                style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-lv-del">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;">Tidak ada data level</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:11px 16px;border-top:1px solid var(--cream-dark);
        display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <span style="font-size:12.5px;color:var(--text-light);" id="showingInfo">
            Showing 1 to {{ $levels->count() }} of {{ $levels->count() }} entries
        </span>
        <div style="display:flex;gap:6px;align-items:center;">
            <button class="btn-pg" disabled id="btnPrev" onclick="changePage(-1)">Previous</button>
            <span class="pg-num active" id="pgNum">1</span>
            <button class="btn-pg" id="btnNext" onclick="changePage(1)">Next</button>
        </div>
    </div>
</div>

{{-- ===== MODAL PEGAWAI ===== --}}
<div id="modalPegawai" class="modal-overlay" onclick="closeModalOnOverlay(event,'modalPegawai')">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <span id="modalPegawaiTitle">
                <i class="fa-solid fa-users"></i> Daftar Pegawai
            </span>
            <button class="modal-close" onclick="closeModal('modalPegawai')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div id="modalPegawaiContent" style="max-height:400px;overflow-y:auto;"></div>
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH LEVEL ===== --}}
<div id="modalTambah" class="modal-overlay" onclick="closeModalOnOverlay(event,'modalTambah')">
    <div class="modal-box">
        <div class="modal-header">
            <span><i class="fa-solid fa-plus"></i> Tambah Level</span>
            <button class="modal-close" onclick="closeModal('modalTambah')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.level.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Level Baru</label>
                    <input type="text" name="nama_level" id="inputTambah"
                        class="form-control" placeholder="Contoh: Supervisor" required>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalTambah')">Batal</button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL UBAH LEVEL ===== --}}
<div id="modalEdit" class="modal-overlay" onclick="closeModalOnOverlay(event,'modalEdit')">
    <div class="modal-box">
        <div class="modal-header">
            <span><i class="fa-solid fa-pen-to-square"></i> Ubah Level</span>
            <button class="modal-close" onclick="closeModal('modalEdit')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" id="ubahLevelForm" action="">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Level</label>
                    <input type="text" name="nama_level" id="inputNamaLevel"
                        class="form-control" placeholder="Nama level..." required>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa-solid fa-check"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* (salin style dari sebelumnya, pastikan tidak ada error) */
.mb-16 { margin-bottom: 16px; }
.alert-success, .alert-danger { padding: 11px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; }
.alert-success { background:rgba(26,122,74,.08); border:1.5px solid rgba(26,122,74,.25); color:#1a7a4a; }
.alert-danger { background:rgba(220,53,69,.07); border:1.5px solid rgba(220,53,69,.2); color:var(--danger); }
.btn-tambah-level { display:inline-flex; align-items:center; gap:7px; padding:7px 16px; background:var(--gold); color:var(--brown); border:none; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; }
.btn-icon-pegawai { position:relative; display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; background:rgba(201,162,39,.1); border:1.5px solid rgba(201,162,39,.35); border-radius:10px; color:var(--brown); font-size:15px; cursor:pointer; }
.badge-count { position:absolute; top:-6px; right:-6px; background:var(--brown); color:var(--gold); font-size:9.5px; font-weight:800; min-width:17px; height:17px; border-radius:20px; display:flex; align-items:center; justify-content:center; padding:0 3px; border:1.5px solid #fff; }
.btn-lv-edit { padding:5px 13px; border-radius:8px; font-size:12px; font-weight:600; background:var(--cream); border:1.5px solid var(--cream-mid); color:var(--text-mid); cursor:pointer; }
.btn-lv-del { padding:5px 9px; border-radius:8px; font-size:12px; font-weight:600; background:#fde8e8; border:1.5px solid #f5c6c6; color:var(--danger); cursor:pointer; }
.btn-pg { padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; background:#fff; border:1.5px solid var(--cream-dark); cursor:pointer; }
.pg-num { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; font-size:13px; font-weight:700; border:1.5px solid var(--cream-dark); background:#fff; color:var(--text-mid); }
.pg-num.active { background:var(--brown); color:var(--gold); border-color:var(--brown); }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.active { display:flex; }
.modal-box { background:#fff; border-radius:16px; width:100%; max-width:460px; margin:16px; overflow:hidden; }
.modal-header { background:var(--brown); color:var(--gold); padding:14px 18px; font-weight:700; display:flex; justify-content:space-between; }
.modal-body { padding:20px 18px; }
.modal-footer { padding:12px 18px; border-top:1px solid var(--cream-dark); display:flex; justify-content:flex-end; gap:10px; }
.btn-modal-cancel, .btn-modal-submit { padding:8px 18px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; }
.btn-modal-cancel { background:var(--cream); border:1.5px solid var(--cream-dark); color:var(--text-mid); }
.btn-modal-submit { background:var(--brown); color:var(--gold); border:none; }
.tbl-pegawai { width:100%; border-collapse:collapse; }
.tbl-pegawai th { background:var(--cream); padding:9px 14px; font-size:11.5px; text-align:left; border-bottom:1.5px solid var(--cream-dark); }
.tbl-pegawai td { padding:10px 14px; border-bottom:1px solid var(--cream-dark); font-size:13px; }
.badge-id { background:var(--brown); color:var(--gold); padding:3px 9px; border-radius:6px; font-size:11px; font-weight:700; font-family:monospace; }
.avatar-circle { width:30px; height:30px; border-radius:50%; background:rgba(201,162,39,.15); display:flex; align-items:center; justify-content:center; font-weight:800; }
.badge-you { padding:2px 7px; background:rgba(26,122,74,.1); border:1px solid rgba(26,122,74,.25); color:#1a7a4a; border-radius:20px; font-size:10px; }
.btn-hapus-pegawai { padding:5px 11px; border-radius:8px; font-size:11.5px; font-weight:600; background:#fde8e8; border:1.5px solid #f5c6c6; color:var(--danger); cursor:pointer; }
</style>
@endpush

@push('scripts')
<script>
// ── CSRF token ─────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Modal helpers ─────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function closeModalOnOverlay(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

// ── Modal Tambah ──────────────────────────────────────
function openModalTambah() {
    document.getElementById('inputTambah').value = '';
    openModal('modalTambah');
}

// ── Modal Edit ────────────────────────────────────────
function openModalEdit(id, nama) {
    document.getElementById('inputNamaLevel').value = nama;
    document.getElementById('ubahLevelForm').action = '/admin/level/' + id;
    openModal('modalEdit');
}

// ── Modal Pegawai ─────────────────────────────────────
function openModalPegawai(namaLevel, icon, pegawai) {
    document.getElementById('modalPegawaiTitle').innerHTML =
        `<i class="fa-solid ${icon}"></i> Pegawai — ${namaLevel}`;

    const content = document.getElementById('modalPegawaiContent');
    if (!pegawai || pegawai.length === 0) {
        content.innerHTML = `<div style="padding:40px;text-align:center;color:var(--text-light);">
            <i class="fa-solid fa-users-slash"></i> Belum ada pegawai dengan level ini
        </div>`;
    } else {
        const rows = pegawai.map((p, i) => {
            const inisial = p.name.charAt(0).toUpperCase();
            const badgeYou = p.is_me ? `<span class="badge-you ms-2">Anda</span>` : '';
            const actionBtn = p.is_me
                ? `<span style="font-size:11px;color:var(--text-light);">—</span>`
                : `<form method="POST" action="${p.delete_url}" style="margin:0;"
                        onsubmit="return confirm('Hapus akun pegawai ${p.name}?')">
                        <input type="hidden" name="_token" value="${CSRF}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn-hapus-pegawai">
                            <i class="fa-solid fa-user-minus"></i> Hapus
                        </button>
                   </form>`;
            return `
            <tr>
                <td style="text-align:center;color:var(--text-light);">${i+1}.</td>
                <td><span class="badge-id">${p.kd_user}</span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div class="avatar-circle">${inisial}</div>
                        <span style="font-weight:600;">${p.name}</span>
                        ${badgeYou}
                    </div>
                </td>
                <td style="text-align:center;">${actionBtn}</td>
            </tr>`;
        }).join('');

        content.innerHTML = `
            <table class="tbl-pegawai">
                <thead>
                    <tr>
                        <th style="width:44px;">No</th>
                        <th style="width:120px;">ID Pegawai</th>
                        <th>Nama</th>
                        <th style="width:90px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `;
    }
    openModal('modalPegawai');
}

// ── Filter / Search level ─────────────────────────────
let currentPage = 1;
let perPage = 10;
let allRows = [];

function initRows() {
    allRows = Array.from(document.querySelectorAll('.level-row'));
    renderTable('');
}

function filterLevel() {
    const q = document.getElementById('searchLevel').value.toLowerCase();
    currentPage = 1;
    renderTable(q);
}

function renderTable(q = '') {
    const filtered = allRows.filter(r => !q || r.dataset.nama.includes(q));
    const total = filtered.length;
    const start = (currentPage - 1) * perPage;
    const end = Math.min(start + perPage, total);

    allRows.forEach(r => r.style.display = 'none');
    filtered.slice(start, end).forEach(r => r.style.display = '');

    document.getElementById('showingInfo').textContent =
        total === 0 ? 'Tidak ada data' : `Showing ${start+1} to ${end} of ${total} entries`;

    document.getElementById('pgNum').textContent = currentPage;
    document.getElementById('btnPrev').disabled = currentPage === 1;
    document.getElementById('btnNext').disabled = end >= total;
}

function changePage(dir) {
    const q = document.getElementById('searchLevel').value.toLowerCase();
    const filtered = allRows.filter(r => !q || r.dataset.nama.includes(q));
    const maxPage = Math.ceil(filtered.length / perPage) || 1;
    currentPage = Math.max(1, Math.min(currentPage + dir, maxPage));
    renderTable(q);
}

document.getElementById('showEntries')?.addEventListener('change', function() {
    perPage = parseInt(this.value);
    currentPage = 1;
    renderTable(document.getElementById('searchLevel').value.toLowerCase());
});

// Inisialisasi setelah DOM siap
document.addEventListener('DOMContentLoaded', function() {
    initRows();
});
</script>
@endpush