@extends('layouts.app')
@section('title', 'Data Menu — Admin')
@section('page-title', 'Data Menu')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Data Menu</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-utensils" style="color:var(--gold);margin-right:8px;"></i>Data Menu</p>
        <p class="ph-sub">Total {{ $menus->total() }} menu terdaftar</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
            <div class="search-box">
                <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" placeholder="Cari menu..." value="{{ request('search') }}">
            </div>
            <select name="kategori" class="form-control" style="width:auto;padding:7px 12px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->kd_kategori }}" {{ request('kategori')==$k->kd_kategori?'selected':'' }}>
                        {{ $k->name_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-brown"><i class="fa-solid fa-filter"></i> Filter</button>
        </form>
        <button class="btn-primary" onclick="openModal('createModal')">
            <i class="fa-solid fa-plus"></i> Tambah Menu
        </button>
    </div>
</div>

{{-- Menu Cards Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;" class="mb-20">
    @forelse($menus as $menu)
    <div class="menu-card">
        <div class="menu-card-img">
            @if($menu->photo)
                <img src="{{ asset('storage/'.$menu->photo) }}" alt="{{ $menu->name_menu }}">
            @else
                <div class="menu-card-noimg"><i class="fa-solid fa-bowl-food"></i></div>
            @endif
            <span class="menu-status-badge {{ $menu->status === 'tersedia' ? 'avail' : 'habis' }}">
                {{ $menu->status === 'tersedia' ? 'Tersedia' : 'Habis' }}
            </span>
        </div>
        <div class="menu-card-body">
            <div class="menu-card-cat">{{ $menu->kategori->name_kategori ?? '-' }}</div>
            <div class="menu-card-name">{{ $menu->name_menu }}</div>
            <div class="menu-card-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
            @if($menu->description)
                <div class="menu-card-desc">{{ Str::limit($menu->description, 55) }}</div>
            @endif
        </div>
        <div class="menu-card-footer">
            <button class="btn-secondary btn-sm" onclick="editMenu({{ $menu->toJson() }})">
                <i class="fa-solid fa-pen"></i> Edit
            </button>
            <form method="POST" action="{{ route('admin.menu.destroy', $menu->kd_menu) }}"
                onsubmit="return confirm('Hapus menu {{ $menu->name_menu }}?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;">
        <div class="empty-state"><div class="empty-icon">🍽️</div><p>Belum ada menu. Tambahkan menu pertama!</p></div>
    </div>
    @endforelse
</div>

<div class="pagination">{{ $menus->withQueryString()->links('vendor.pagination.simple') }}</div>

{{-- CREATE MODAL --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-plus" style="margin-right:7px;"></i>Tambah Menu</span>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Menu <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="name_menu" class="form-control" placeholder="Nama menu" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori <span style="color:var(--danger)">*</span></label>
                        <select name="kategori_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->kd_kategori }}">{{ $k->name_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Harga <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="harga" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span style="color:var(--danger)">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat menu..."></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Foto Menu</label>
                    <input type="file" name="photo" class="form-control" accept="image/*"
                        style="padding:7px;" onchange="previewImg(this,'prevCreate')">
                    <img id="prevCreate" style="display:none;width:100%;max-height:140px;object-fit:cover;border-radius:10px;margin-top:8px;">
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
            <span class="modal-title"><i class="fa-solid fa-pen-to-square" style="margin-right:7px;"></i>Edit Menu</span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="name_menu" id="eName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" id="eKat" class="form-control" required>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->kd_kategori }}">{{ $k->name_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" id="eHarga" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="eStatus" class="form-control" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="eDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Foto Baru <span style="font-weight:400;color:var(--text-light);">(kosongkan jika tidak diubah)</span></label>
                    <input type="file" name="photo" class="form-control" accept="image/*"
                        style="padding:7px;" onchange="previewImg(this,'prevEdit')">
                    <img id="prevEdit" style="display:none;width:100%;max-height:120px;object-fit:cover;border-radius:10px;margin-top:8px;">
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
.menu-card { background:#fff;border-radius:16px;border:1.5px solid var(--cream-dark);overflow:hidden;box-shadow:var(--shadow-sm);transition:var(--transition);display:flex;flex-direction:column; }
.menu-card:hover { box-shadow:var(--shadow-md);transform:translateY(-2px); }
.menu-card-img { position:relative;height:150px;background:var(--cream-dark);overflow:hidden;flex-shrink:0; }
.menu-card-img img { width:100%;height:100%;object-fit:cover; }
.menu-card-noimg { width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--cream-mid); }
.menu-status-badge { position:absolute;top:8px;right:8px;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px; }
.menu-status-badge.avail { background:rgba(26,122,74,.85);color:#fff; }
.menu-status-badge.habis { background:rgba(192,57,43,.85);color:#fff; }
.menu-card-body { padding:12px 14px;flex:1; }
.menu-card-cat { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--gold-dark);margin-bottom:3px; }
.menu-card-name { font-size:14px;font-weight:700;color:var(--brown);margin-bottom:4px;line-height:1.3; }
.menu-card-price { font-size:15px;font-weight:800;color:var(--gold-dark);margin-bottom:5px; }
.menu-card-desc { font-size:11.5px;color:var(--text-light);line-height:1.5; }
.menu-card-footer { padding:10px 14px;border-top:1px solid var(--cream-dark);display:flex;gap:7px;background:var(--cream);flex-shrink:0; }
</style>
@endpush

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function previewImg(input, previewId) {
    const prev = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}

function editMenu(m) {
    document.getElementById('eName').value   = m.name_menu;
    document.getElementById('eKat').value    = m.kategori_id;
    document.getElementById('eHarga').value  = m.harga;
    document.getElementById('eStatus').value = m.status;
    document.getElementById('eDesc').value   = m.description ?? '';
    document.getElementById('editForm').action = '/admin/menu/' + m.kd_menu;
    openModal('editModal');
}

@if($errors->any()) openModal('createModal'); @endif
</script>
@endpush