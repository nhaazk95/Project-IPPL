@extends('layouts.app')
@section('title', 'Meja — Admin')
@section('page-title', 'Manajemen Meja')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Meja</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-chair" style="color:var(--gold);margin-right:8px;"></i>Manajemen Meja</p>
        <p class="ph-sub">
            <span id="statTotal">{{ $totalMeja }}</span> meja ·
            <span style="color:var(--success);font-weight:700;" id="statTersedia">{{ $mejaStats['tersedia'] }} tersedia</span> ·
            <span style="color:var(--danger);font-weight:700;" id="statTerisi">{{ $mejaStats['terisi'] }} terisi</span>
        </p>
    </div>
    <button class="btn-primary" onclick="openModal('createModal')">
        <i class="fa-solid fa-plus"></i> Tambah Meja
    </button>
</div>

{{-- Visual Grid Card --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-grip" style="margin-right:7px;"></i>Status Meja (Visual)</span>
        <div style="display:flex;gap:14px;font-size:12px;align-items:center;">
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:11px;height:11px;border-radius:3px;background:rgba(26,122,74,.15);border:1.5px solid var(--success);display:inline-block;"></span>
                Tersedia
            </span>
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:11px;height:11px;border-radius:3px;background:rgba(201,162,39,.15);border:1.5px solid var(--gold);display:inline-block;"></span>
                Terisi
            </span>
        </div>
    </div>

    {{-- Hint (diubah) --}}
    <div style="padding:10px 20px 0;font-size:11.5px;color:var(--text-light);display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-pen"></i> Hover lalu klik <strong>✏️</strong> untuk edit / hapus meja
    </div>

    <div class="card-body" style="padding:20px;">
        <div class="meja-grid" id="mejaGrid">
            @foreach($mejas as $meja)
            @php
                $terisi = $meja->status === 'terisi';
                $pelangganAktif = $terisi
                    ? \App\Models\Pelanggan::where('no_meja', $meja->no_meja)->latest('login_at')->first()
                    : null;
            @endphp
            <div class="meja-box {{ $terisi ? 'terisi' : 'tersedia' }}"
                id="meja-{{ $meja->id }}"
                data-id="{{ $meja->id }}"
                data-no="{{ $meja->no_meja }}"
                data-status="{{ $meja->status }}"
                data-pelanggan-nama="{{ $pelangganAktif?->name_pelanggan ?? '' }}"
                data-login-at="{{ $pelangganAktif?->login_at ? \Carbon\Carbon::parse($pelangganAktif->login_at)->format('H:i') : '' }}"
                style="cursor:default;">  {{-- cursor default, bukan pointer --}}

                {{-- Tombol edit pensil --}}
                <button class="meja-edit-btn" onclick="event.stopPropagation(); editMeja(this.parentElement)" title="Edit meja">
                    <i class="fa-solid fa-pen"></i>
                </button>

                <div class="meja-chair">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 2a2 2 0 0 0-2 2v5H3a1 1 0 0 0-1 1v3a3 3 0 0 0 3 3h1v3a1 1 0 0 0 2 0v-3h8v3a1 1 0 0 0 2 0v-3h1a3 3 0 0 0 3-3V10a1 1 0 0 0-1-1h-1V4a2 2 0 0 0-2-2H6z"/>
                    </svg>
                </div>
                <div class="meja-num">{{ $meja->no_meja }}</div>
                <div class="meja-lbl">
                    @if($terisi && $pelangganAktif)
                        {{ $pelangganAktif->name_pelanggan }}
                    @elseif($terisi)
                        Terisi
                    @else
                        Kosong
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    <div style="padding:14px 20px;border-top:1px solid var(--cream-dark);
        display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <span style="font-size:12.5px;color:var(--text-light);">
            Halaman {{ $mejas->currentPage() }} dari {{ $mejas->lastPage() }}
            · Total {{ $totalMeja }} meja
        </span>
        <div style="display:flex;gap:6px;align-items:center;">
            @if($mejas->onFirstPage())
                <span class="meja-pg-btn disabled">Previous</span>
            @else
                <a href="{{ $mejas->previousPageUrl() }}" class="meja-pg-btn">Previous</a>
            @endif

            @for($p = 1; $p <= $mejas->lastPage(); $p++)
                @if($p == $mejas->currentPage())
                    <span class="meja-pg-num active">{{ $p }}</span>
                @elseif($p == 1 || $p == $mejas->lastPage() || abs($p - $mejas->currentPage()) <= 1)
                    <a href="{{ $mejas->url($p) }}" class="meja-pg-num">{{ $p }}</a>
                @elseif(abs($p - $mejas->currentPage()) == 2)
                    <span class="meja-pg-ellipsis">…</span>
                @endif
            @endfor

            @if($mejas->hasMorePages())
                <a href="{{ $mejas->nextPageUrl() }}" class="meja-pg-btn">Next</a>
            @else
                <span class="meja-pg-btn disabled">Next</span>
            @endif
        </div>
    </div>
</div>

{{-- Toast Notifikasi --}}
<div id="mejaToast" class="meja-toast"></div>

{{-- CREATE MODAL --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box" style="max-width:360px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-plus" style="margin-right:7px;"></i>Tambah Meja</span>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.meja.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nomor Meja <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="no_meja" class="form-control" placeholder="Contoh: 5" min="1" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status Awal</label>
                    <select name="status" class="form-control">
                        <option value="tersedia">Tersedia</option>
                        <option value="terisi">Terisi</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Batal</button>
                <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL (dengan status) --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-chair" style="margin-right:7px;"></i>Edit Meja <span id="editMejaTitle"></span></span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            {{-- Info pelanggan jika meja terisi --}}
            <div id="infoTerisi" style="display:none;background:rgba(201,162,39,.08);
                border:1.5px solid rgba(201,162,39,.3);border-radius:10px;
                padding:10px 13px;margin-bottom:14px;font-size:12.5px;color:var(--brown);">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span><i class="fa-solid fa-user" style="margin-right:6px;color:var(--gold-dark);"></i>
                        <strong id="infoPelangganNama">—</strong></span>
                    <span style="font-size:11px;color:var(--text-light);" id="infoPelangganLogin"></span>
                </div>
                <form method="POST" id="kosongkanForm" style="margin-top:10px;">
                    @csrf
                    <button type="submit" class="btn-kosongkan w-100"
                        onclick="return confirm('Paksa kosongkan meja ini?\nPelanggan akan otomatis keluar.')">
                        <i class="fa-solid fa-right-from-bracket"></i> Kosongkan Meja (Force Logout)
                    </button>
                </form>
            </div>

            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nomor Meja</label>
                    <input type="number" name="no_meja" id="eNoMeja" class="form-control" min="1" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="status" id="eStatus" class="form-control" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="terisi">Terisi</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <form method="POST" id="deleteForm" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger" onclick="return confirm('Hapus meja ini?')">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </form>
            <div style="display:flex;gap:8px;">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" form="editForm" class="btn-gold">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Grid meja (sama persis seperti sebelumnya) */
.meja-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 10px;
}
.meja-box {
    border-radius: 12px;
    padding: 10px 6px 8px;
    text-align: center;
    transition: all .18s;
    user-select: none;
    position: relative;
}
.meja-box.tersedia {
    background: rgba(26,122,74,.07);
    border: 1.5px solid rgba(26,122,74,.3);
}
.meja-box.terisi {
    background: rgba(201,162,39,.1);
    border: 1.5px solid rgba(201,162,39,.4);
}
.meja-box:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(44,24,16,.12); }

.meja-chair svg { width: 22px; height: 22px; }
.meja-box.tersedia .meja-chair svg { color: #1a7a4a; }
.meja-box.terisi .meja-chair svg { color: #a07d1a; }

.meja-num { font-weight: 800; font-size: 13px; line-height: 1; margin-top: 2px; }
.meja-box.tersedia .meja-num { color: #1a7a4a; }
.meja-box.terisi .meja-num { color: #a07d1a; }

.meja-lbl { font-size: 9.5px; font-weight: 600; margin-top: 2px; }
.meja-box.tersedia .meja-lbl { color: rgba(26,122,74,.7); }
.meja-box.terisi .meja-lbl { color: rgba(160,125,26,.8); }

/* Tombol edit kecil di pojok meja */
.meja-edit-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(44,24,16,.6);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity .15s;
    z-index: 2;
    font-size: 10px;
}
.meja-box:hover .meja-edit-btn { opacity: 1; }
.meja-edit-btn:hover { background: var(--brown); color: var(--gold); }

/* Toast */
.meja-toast {
    position: fixed;
    bottom: 28px; left: 50%; transform: translateX(-50%);
    background: var(--brown);
    color: var(--gold);
    padding: 10px 22px;
    border-radius: 30px;
    font-size: 13px; font-weight: 700;
    box-shadow: 0 4px 20px rgba(0,0,0,.18);
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s;
    z-index: 9999;
    white-space: nowrap;
}
.meja-toast.show { opacity: 1; }

/* Tombol kosongkan meja */
.btn-kosongkan {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 700;
    background: #fde8e8; border: 1.5px solid #f5c6c6;
    color: var(--danger); cursor: pointer;
    transition: var(--transition);
    font-family: inherit;
    justify-content: center;
}
.btn-kosongkan:hover { background: var(--danger); color: #fff; border-color: var(--danger); }
.w-100 { width: 100%; }

/* Pagination */
.meja-pg-btn, .meja-pg-num {
    display: inline-flex; align-items: center;
    padding: 7px 16px; border-radius: 9px;
    font-size: 12.5px; font-weight: 600;
    border: 1.5px solid var(--cream-dark);
    background: #fff; color: var(--text-mid);
    text-decoration: none; cursor: pointer;
    transition: all .18s;
}
.meja-pg-btn:hover:not(.disabled), .meja-pg-num:hover {
    background: var(--brown); color: var(--gold); border-color: var(--brown);
}
.meja-pg-btn.disabled { opacity: .4; cursor: not-allowed; pointer-events: none; }
.meja-pg-num.active { background: var(--brown); color: var(--gold); border-color: var(--brown); }
.meja-pg-ellipsis { font-size: 13px; color: var(--text-light); padding: 0 2px; }

/* Modal */
.modal-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(44,24,16,.5);
    z-index: 1000; align-items: center; justify-content: center;
    padding: 20px;
}
.modal-backdrop.active { display: flex; }
.modal-box {
    background: #fff; border-radius: 18px;
    width: 100%; max-width: 400px;
    box-shadow: 0 20px 60px rgba(44,24,16,.25);
    overflow: hidden;
}
.modal-header {
    background: var(--brown); color: var(--gold);
    padding: 14px 20px; display: flex;
    align-items: center; justify-content: space-between;
}
.modal-title { font-weight: 700; font-size: 14px; }
.modal-close { background: none; border: none; color: rgba(201,162,39,.6); font-size: 16px; cursor: pointer; }
.modal-close:hover { color: var(--gold); }
.modal-body { padding: 20px 22px; }
.modal-footer {
    padding: 12px 22px; display: flex;
    justify-content: space-between; gap: 10px;
    border-top: 1px solid var(--cream-dark);
}
.btn-secondary, .btn-gold, .btn-danger {
    padding: 8px 18px; border-radius: 8px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    border: none;
}
.btn-secondary { background: var(--cream-dark); color: var(--text-mid); border: 1px solid var(--cream-mid); }
.btn-gold { background: var(--gold); color: var(--brown); }
.btn-danger { background: #fde8e8; color: var(--danger); border: 1.5px solid #f5c6c6; }
</style>
@endpush

@push('scripts')
<script>
// Modal helpers
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

// Edit meja (dipanggil dari tombol pensil)
function editMeja(box) {
    const id = box.dataset.id;
    const no_meja = box.dataset.no;
    const status = box.dataset.status;
    const pelangganNama = box.dataset.pelangganNama || '';
    const loginAt = box.dataset.loginAt || '';

    document.getElementById('eNoMeja').value = no_meja;
    document.getElementById('eStatus').value = status;
    document.getElementById('editMejaTitle').textContent = '— Meja ' + no_meja;
    document.getElementById('editForm').action = `/admin/meja/${id}`;
    document.getElementById('deleteForm').action = `/admin/meja/${id}`;
    document.getElementById('kosongkanForm').action = `/admin/meja/${id}/kosongkan`;

    const infoBox = document.getElementById('infoTerisi');
    if (status === 'terisi') {
        document.getElementById('infoPelangganNama').textContent = pelangganNama || 'Ada pelanggan';
        document.getElementById('infoPelangganLogin').textContent = loginAt ? 'Login: ' + loginAt : '';
        infoBox.style.display = 'block';
    } else {
        infoBox.style.display = 'none';
    }

    openModal('editModal');
}

// Toast
let toastTimer;
function showToast(msg) {
    const t = document.getElementById('mejaToast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}
</script>
@endpush