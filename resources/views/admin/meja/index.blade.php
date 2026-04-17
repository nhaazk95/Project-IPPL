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
            <span style="color:var(--success);font-weight:600;">{{ $mejas->where('status','tersedia')->count() }} tersedia</span> ·
            <span style="color:var(--danger);font-weight:600;">{{ $mejas->where('status','terisi')->count() }} terisi</span>
        </p>
    </div>
    <button class="btn-primary" onclick="openModal('createModal')">
        <i class="fa-solid fa-plus"></i> Tambah Meja
    </button>
</div>

{{-- Meja Visual Grid --}}
<div class="card mb-20">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-grip" style="margin-right:7px;"></i>Status Meja (Visual)</span>
        <div style="display:flex;gap:14px;font-size:12px;">
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:rgba(26,122,74,.2);border:1.5px solid var(--success);display:inline-block;"></span>Tersedia</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:rgba(201,162,39,.2);border:1.5px solid var(--gold);display:inline-block;"></span>Terisi</span>
        </div>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:12px;">
            @foreach($mejas as $meja)
            @php $terisi = $meja->status === 'terisi'; @endphp
            <div style="
                border:2px solid {{ $terisi ? 'var(--gold)' : 'rgba(26,122,74,.5)' }};
                background:{{ $terisi ? 'rgba(201,162,39,.1)' : 'rgba(26,122,74,.07)' }};
                border-radius:14px;padding:12px 8px;text-align:center;
                cursor:pointer;transition:var(--transition);"
                onclick="editMeja({{ $meja->toJson() }})"
                title="Klik untuk edit">
                <div style="font-size:22px;margin-bottom:4px;">🪑</div>
                <div style="font-weight:800;font-size:14px;color:{{ $terisi ? 'var(--gold-dark)' : 'var(--success)' }};">{{ $meja->no_meja }}</div>
                <div style="font-size:10px;margin-top:2px;color:{{ $terisi ? 'var(--gold-dark)' : 'var(--success)' }};font-weight:600;">
                    {{ $terisi ? 'Terisi' : 'Kosong' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table-list" style="margin-right:7px;"></i>Data Meja</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Meja</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mejas as $meja)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--brown);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;">
                                    {{ $meja->no_meja }}
                                </div>
                                <span style="font-weight:700;color:var(--text-dark);">Meja {{ $meja->no_meja }}</span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge {{ $meja->status === 'tersedia' ? 'badge-success' : 'badge-gold' }}">
                                <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                                {{ ucfirst($meja->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn-secondary btn-sm" onclick="editMeja({{ $meja->toJson() }})">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.meja.destroy', $meja->id) }}"
                                    onsubmit="return confirm('Hapus Meja {{ $meja->no_meja }}?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3">
                        <div class="empty-state"><div class="empty-icon">🪑</div><p>Belum ada meja terdaftar</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination mt-24">{{ $mejas->links('vendor.pagination.simple') }}</div>

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

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function editMeja(m) {
    document.getElementById('eNoMeja').value  = m.no_meja;
    document.getElementById('eStatus').value  = m.status;
    document.getElementById('editForm').action = '/admin/meja/' + m.id;
    openModal('editModal');
}
</script>
@endpush