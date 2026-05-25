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

    {{-- Hint --}}
    <div style="padding:10px 20px 0;font-size:11.5px;color:var(--text-light);display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-circle-info" style="color:var(--gold-dark);"></i>
        Klik meja untuk ubah status · Tahan untuk edit / hapus meja
    </div>

    <div class="card-body" style="padding:20px;">
        <div class="meja-grid" id="mejaGrid">
            @foreach($mejas as $meja)
            @php $terisi = $meja->status === 'terisi'; @endphp
            <div class="meja-box {{ $terisi ? 'terisi' : 'tersedia' }}"
                id="meja-{{ $meja->id }}"
                data-id="{{ $meja->id }}"
                data-no="{{ $meja->no_meja }}"
                data-status="{{ $meja->status }}"
                title="Klik untuk ubah status · Tahan untuk edit">
                <div class="meja-chair">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 2a2 2 0 0 0-2 2v5H3a1 1 0 0 0-1 1v3a3 3 0 0 0 3 3h1v3a1 1 0 0 0 2 0v-3h8v3a1 1 0 0 0 2 0v-3h1a3 3 0 0 0 3-3V10a1 1 0 0 0-1-1h-1V4a2 2 0 0 0-2-2H6z"/>
                    </svg>
                </div>
                <div class="meja-num">{{ $meja->no_meja }}</div>
                <div class="meja-lbl">{{ $terisi ? 'Terisi' : 'Kosong' }}</div>
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

{{-- EDIT MODAL --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box" style="max-width:360px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square" style="margin-right:7px;"></i>Edit Meja <span id="editMejaTitle"></span></span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nomor Meja</label>
                    <input type="number" name="no_meja" id="eNoMeja" class="form-control" min="1" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="status" id="eStatus" class="form-control">
                        <option value="tersedia">Tersedia</option>
                        <option value="terisi">Terisi</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger"
                        onclick="return confirm('Hapus meja ini?')">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" form="editForm" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Grid meja */
.meja-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 10px;
}
.meja-box {
    border-radius: 12px;
    padding: 10px 6px 8px;
    text-align: center;
    cursor: pointer;
    transition: all .18s;
    user-select: none;
    position: relative;
}
.meja-box:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(44,24,16,.12); }
.meja-box.tersedia {
    background: rgba(26,122,74,.07);
    border: 1.5px solid rgba(26,122,74,.3);
}
.meja-box.terisi {
    background: rgba(201,162,39,.1);
    border: 1.5px solid rgba(201,162,39,.4);
}
/* Animasi saat toggle */
.meja-box.toggling {
    transform: scale(.93);
    opacity: .7;
}
/* Loading spinner di atas meja */
.meja-box.loading::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 11px;
    background: rgba(255,255,255,.55);
    display: flex;
    align-items: center;
    justify-content: center;
}

.meja-chair {
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
}
.meja-chair svg { width: 22px; height: 22px; }
.meja-box.tersedia .meja-chair { color: #1a7a4a; }
.meja-box.terisi  .meja-chair  { color: #a07d1a; }
.meja-num { font-weight: 800; font-size: 13px; line-height: 1; }
.meja-box.tersedia .meja-num { color: #1a7a4a; }
.meja-box.terisi  .meja-num  { color: #a07d1a; }
.meja-lbl { font-size: 9.5px; font-weight: 600; margin-top: 2px; }
.meja-box.tersedia .meja-lbl { color: rgba(26,122,74,.7); }
.meja-box.terisi  .meja-lbl  { color: rgba(160,125,26,.8); }

/* Toast */
.meja-toast {
    position: fixed;
    bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: var(--brown);
    color: var(--gold);
    padding: 10px 22px;
    border-radius: 30px;
    font-size: 13px; font-weight: 700;
    box-shadow: 0 4px 20px rgba(0,0,0,.18);
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s, transform .25s;
    z-index: 9999;
    white-space: nowrap;
}
.meja-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

/* Pagination */
.meja-pg-btn {
    display: inline-flex; align-items: center;
    padding: 7px 16px; border-radius: 9px;
    font-size: 12.5px; font-weight: 600;
    border: 1.5px solid var(--cream-dark);
    background: #fff; color: var(--text-mid);
    text-decoration: none; cursor: pointer;
    transition: all .18s;
}
.meja-pg-btn:hover:not(.disabled) {
    background: var(--brown); color: var(--gold); border-color: var(--brown);
}
.meja-pg-btn.disabled { opacity: .4; cursor: not-allowed; pointer-events: none; }
.meja-pg-num {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 9px;
    font-size: 13px; font-weight: 700;
    border: 1.5px solid var(--cream-dark);
    background: #fff; color: var(--text-mid);
    text-decoration: none; transition: all .18s;
}
.meja-pg-num:hover { background: var(--cream-dark); }
.meja-pg-num.active { background: var(--brown); color: var(--gold); border-color: var(--brown); }
.meja-pg-ellipsis { font-size: 13px; color: var(--text-light); padding: 0 2px; }
</style>
@endpush

@push('scripts')
<script>
// ── Modal helpers ─────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

// ── Edit modal (buka via long-press / klik kanan) ─────────
function editMeja(m) {
    document.getElementById('eNoMeja').value  = m.no_meja;
    document.getElementById('eStatus').value  = m.status;
    document.getElementById('editMejaTitle').textContent = '— Meja ' + m.no_meja;
    document.getElementById('editForm').action   = '/admin/meja/' + m.id;
    document.getElementById('deleteForm').action = '/admin/meja/' + m.id;
    openModal('editModal');
}

// ── Toast ──────────────────────────────────────────────────
let toastTimer;
function showToast(msg) {
    const t = document.getElementById('mejaToast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

// ── Update stat counter di header ─────────────────────────
function updateStats(newStatus) {
    const elTersedia = document.getElementById('statTersedia');
    const elTerisi   = document.getElementById('statTerisi');
    let tersedia = parseInt(elTersedia.textContent);
    let terisi   = parseInt(elTerisi.textContent);

    if (newStatus === 'terisi') { tersedia--; terisi++; }
    else                        { tersedia++; terisi--; }

    elTersedia.textContent = tersedia + ' tersedia';
    elTerisi.textContent   = terisi   + ' terisi';
}

// ── Toggle status via AJAX ────────────────────────────────
function toggleMeja(box) {
    if (box.classList.contains('loading')) return;

    const id     = box.dataset.id;
    const noMeja = box.dataset.no;

    box.classList.add('loading', 'toggling');

    fetch(`/admin/meja/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error('Gagal');

        const newStatus = data.status;
        const isTerisi  = newStatus === 'terisi';

        // Update class & data
        box.classList.remove('tersedia', 'terisi');
        box.classList.add(newStatus);
        box.dataset.status = newStatus;

        // Update label
        box.querySelector('.meja-lbl').textContent = isTerisi ? 'Terisi' : 'Kosong';

        // Update stat header
        updateStats(newStatus);

        // Toast
        const icon = isTerisi ? '🔴' : '🟢';
        showToast(`${icon} Meja ${noMeja} → ${isTerisi ? 'Terisi' : 'Tersedia'}`);
    })
    .catch(() => {
        showToast('⚠️ Gagal mengubah status meja');
    })
    .finally(() => {
        box.classList.remove('loading', 'toggling');
    });
}

// ── Bind events: klik = toggle, tahan = edit ─────────────
document.querySelectorAll('.meja-box').forEach(box => {
    let pressTimer = null;
    let moved = false;

    // Long press (tahan ~600ms) → buka modal edit
    box.addEventListener('mousedown', () => {
        moved = false;
        pressTimer = setTimeout(() => {
            pressTimer = null;
            editMeja({
                id:       box.dataset.id,
                no_meja:  box.dataset.no,
                status:   box.dataset.status,
            });
        }, 600);
    });

    box.addEventListener('mousemove', () => { moved = true; });

    box.addEventListener('mouseup', () => {
        if (pressTimer) {
            clearTimeout(pressTimer);
            pressTimer = null;
            // Short click → toggle
            if (!moved) toggleMeja(box);
        }
    });

    box.addEventListener('mouseleave', () => {
        if (pressTimer) { clearTimeout(pressTimer); pressTimer = null; }
    });

    // Touch support (mobile long press)
    box.addEventListener('touchstart', (e) => {
        moved = false;
        pressTimer = setTimeout(() => {
            pressTimer = null;
            editMeja({
                id:      box.dataset.id,
                no_meja: box.dataset.no,
                status:  box.dataset.status,
            });
        }, 600);
    }, { passive: true });

    box.addEventListener('touchmove', () => { moved = true; });

    box.addEventListener('touchend', () => {
        if (pressTimer) {
            clearTimeout(pressTimer);
            pressTimer = null;
            if (!moved) toggleMeja(box);
        }
    });
});
</script>
@endpush