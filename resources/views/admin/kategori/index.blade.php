@extends('layouts.app')
@section('title', 'Kategori — Admin')
@section('page-title', 'Kategori Menu')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Kategori</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-layer-group" style="color:var(--gold);margin-right:8px;"></i>Kategori Menu</p>
        <p class="ph-sub">Total {{ $kategoris->total() }} kategori</p>
    </div>
    <button class="btn-primary" onclick="openModal('createModal')">
        <i class="fa-solid fa-plus"></i> Tambah Kategori
    </button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-list" style="margin-right:7px;"></i>Daftar Kategori</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Kode Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="text-align:center;">Jumlah Menu</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $i => $kat)
                    <tr>
                        <td style="color:var(--text-light);font-size:12px;">{{ $kategoris->firstItem() + $i }}</td>
                        <td>
                            @if($kat->photo)
                                <img src="{{ asset('storage/'.$kat->photo) }}" alt=""
                                    style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:2px solid var(--cream-dark);">
                            @else
                                <div style="width:48px;height:48px;border-radius:10px;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;color:var(--cream-mid);font-size:18px;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td><span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--brown);">{{ $kat->kd_kategori }}</span></td>
                        <td><strong style="color:var(--text-dark);">{{ $kat->name_kategori }}</strong></td>
                        <td style="color:var(--text-light);font-size:12.5px;max-width:200px;">{{ Str::limit($kat->description, 60) ?? '—' }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-gold">{{ $kat->menus_count }} menu</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn-secondary btn-sm" onclick="editKategori({{ $kat->toJson() }})">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.kategori.destroy', $kat->kd_kategori) }}"
                                    onsubmit="return confirm('Hapus kategori {{ $kat->name_kategori }}?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty-state"><div class="empty-icon">📂</div><p>Belum ada kategori</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination mt-24">{{ $kategoris->links('vendor.pagination.simple') }}</div>

{{-- CREATE MODAL --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-plus" style="margin-right:7px;"></i>Tambah Kategori</span>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.kategori.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kategori <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name_kategori" class="form-control" placeholder="Contoh: Makanan Berat" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat..."></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Foto</label>
                    <input type="file" name="photo" class="form-control" accept="image/*" style="padding:7px;"
                        onchange="previewImg(this,'prevC')">
                    <img id="prevC" style="display:none;width:100%;max-height:120px;object-fit:cover;border-radius:10px;margin-top:8px;">
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
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square" style="margin-right:7px;"></i>Edit Kategori</span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name_kategori" id="eName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="eDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Foto Baru <span style="font-weight:400;color:var(--text-light);">(kosongkan jika tidak diubah)</span></label>
                    <input type="file" name="photo" class="form-control" accept="image/*" style="padding:7px;"
                        onchange="previewImg(this,'prevE')">
                    <img id="prevE" style="display:none;width:100%;max-height:120px;object-fit:cover;border-radius:10px;margin-top:8px;">
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
function previewImg(input, id) {
    const prev = document.getElementById(id);
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { prev.src = e.target.result; prev.style.display='block'; };
        r.readAsDataURL(input.files[0]);
    }
}
function editKategori(k) {
    document.getElementById('eName').value = k.name_kategori;
    document.getElementById('eDesc').value = k.description ?? '';
    document.getElementById('editForm').action = '/admin/kategori/' + k.kd_kategori;
    openModal('editModal');
}
@if($errors->any()) openModal('createModal'); @endif
</script>
@endpush