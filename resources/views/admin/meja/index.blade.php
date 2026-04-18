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
            {{ $mejas->total() }} meja total ·
            <span style="color:var(--success);font-weight:700;">{{ $mejaStats['tersedia'] }} tersedia</span> ·
            <span style="color:var(--danger);font-weight:700;">{{ $mejaStats['terisi'] }} terisi</span>
        </p>
    </div>
    <button class="btn-primary" onclick="openModal('createModal')">
        <i class="fa-solid fa-plus"></i> Tambah Meja
    </button>
</div>

{{-- Visual Grid Card --}}
<div class="card mb-20">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-grip" style="margin-right:7px;"></i>Status Meja (Visual)</span>
        <div style="display:flex;gap:16px;font-size:12px;align-items:center;">
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:14px;height:14px;border-radius:4px;background:rgba(26,122,74,.15);border:1.5px solid var(--success);display:inline-block;"></span>
                Tersedia
            </span>
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:14px;height:14px;border-radius:4px;background:rgba(201,162,39,.15);border:1.5px solid var(--gold);display:inline-block;"></span>
                Terisi
            </span>
        </div>
    </div>
    <div class="card-body" style="padding:22px;">
        <div class="meja-grid">
            @foreach($mejas as $meja)
            @php $terisi = $meja->status === 'terisi'; @endphp
            <div class="meja-cell {{ $terisi ? 'terisi' : 'tersedia' }}"
                onclick="editMeja({{ $meja->toJson() }})" title="Klik untuk edit">
                <div class="meja-chair-icon">
                    {{-- Chair SVG icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="30" height="30">
                        <path d="M5 5a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v4H5V5zm-2 6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V11zm3 6h2v2H6v-2zm10 0h2v2h-2v-2z"/>
                    </svg>
                </div>
                <div class="meja-number">{{ $meja->no_meja }}</div>
                <div class="meja-status-text">{{ $terisi ? 'Terisi' : 'Kosong' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Data Table Card --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table-list" style="margin-right:7px;"></i>Data Meja</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th style="width:80px;">No. Meja</th>
                    <th></th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mejas as $meja)
                @php $terisi = $meja->status === 'terisi'; @endphp
                <tr>
                    <td>
                        <div style="width:36px;height:36px;border-radius:10px;background:var(--brown);color:var(--gold);
                            display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;">
                            {{ $meja->no_meja }}
                        </div>
                    </td>
                    <td style="font-weight:600;color:var(--text-dark);">Meja {{ $meja->no_meja }}</td>
                    <td>
                        <span class="badge {{ $terisi ? 'badge-gold' : 'badge-success' }}">
                            <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                            {{ $terisi ? 'Terisi' : 'Tersedia' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:8px;justify-content:center;">
                            <button class="btn-edit-meja" onclick="editMeja({{ $meja->toJson() }})">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.meja.destroy', $meja->id) }}"
                                onsubmit="return confirm('Hapus Meja {{ $meja->no_meja }}?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del-meja">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4">
                    <div class="empty-state"><div class="empty-icon">🪑</div><p>Belum ada meja terdaftar</p></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination besar --}}
<div class="pagination-wrap mt-24">
    {{ $mejas->links('vendor.pagination.simple') }}
</div>

{{-- CREATE MODAL --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box" style="max-width:380px;">
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
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square" style="margin-right:7px;"></i>Edit Meja</span>
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
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Meja Grid */
.meja-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(82px, 1fr));
    gap: 12px;
}
.meja-cell {
    border-radius: 14px;
    padding: 14px 8px 10px;
    text-align: center;
    cursor: pointer;
    transition: all .18s ease;
    user-select: none;
}
.meja-cell:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(44,24,16,.12); }
.meja-cell.tersedia {
    background: rgba(26,122,74,.07);
    border: 2px solid rgba(26,122,74,.35);
}
.meja-cell.terisi {
    background: rgba(201,162,39,.1);
    border: 2px solid rgba(201,162,39,.45);
}
.meja-chair-icon {
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 6px;
}
.meja-cell.tersedia .meja-chair-icon { color: #1a7a4a; }
.meja-cell.terisi  .meja-chair-icon { color: #a07d1a; }
.meja-number {
    font-weight: 800; font-size: 15px; line-height: 1;
}
.meja-cell.tersedia .meja-number { color: #1a7a4a; }
.meja-cell.terisi  .meja-number  { color: #a07d1a; }
.meja-status-text {
    font-size: 10.5px; font-weight: 600; margin-top: 3px;
}
.meja-cell.tersedia .meja-status-text { color: #1a7a4a; }
.meja-cell.terisi  .meja-status-text  { color: #a07d1a; }

/* Table buttons */
.btn-edit-meja {
    display:inline-flex;align-items:center;gap:5px;
    padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;
    background:var(--cream);border:1.5px solid var(--cream-mid);
    color:var(--text-mid);cursor:pointer;transition:var(--transition);
}
.btn-edit-meja:hover { background:var(--cream-dark); }
.btn-del-meja {
    display:inline-flex;align-items:center;gap:5px;
    padding:5px 10px;border-radius:8px;font-size:12px;font-weight:600;
    background:#fde8e8;border:1.5px solid #f5c6c6;
    color:var(--danger);cursor:pointer;transition:var(--transition);
}
.btn-del-meja:hover { background:#fbd5d5; }
</style>
@endpush

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function editMeja(m) {
    document.getElementById('eNoMeja').value = m.no_meja;
    document.getElementById('eStatus').value = m.status;
    document.getElementById('editForm').action = '/admin/meja/' + m.id;
    openModal('editModal');
}
</script>
@endpush