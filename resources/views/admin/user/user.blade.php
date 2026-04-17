@extends('layouts.app')
@section('title', 'Data User — Dapur Nusantara')
@section('page-title', 'Data User')

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-users" style="margin-right:8px;color:var(--gold);"></i>Manajemen User</p>
        <p class="ph-sub">Total {{ $users->total() ?? 0 }} user terdaftar</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.user.index') }}">
            <div class="search-box">
                <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" placeholder="Cari user..." value="{{ request('search') }}">
            </div>
        </form>
        <button class="btn-primary" onclick="openModal('createModal')">
            <i class="fa-solid fa-plus"></i> Tambah User
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-list" style="margin-right:7px;"></i>Daftar User</span>
        <span style="font-size:11px;color:rgba(245,233,192,.45);">{{ $users->total() }} pengguna</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode User</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Level</th>
                        <th>Dibuat</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><span style="font-family:monospace;font-size:12px;font-weight:600;color:var(--brown);">{{ $user->kd_user }}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--gold);color:var(--brown);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:600;color:var(--text-dark);">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="font-weight:500;">{{ $user->username }}</td>
                        <td style="color:var(--text-light);font-size:12.5px;">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->isAdmin() ? 'badge-brown' : 'badge-gold' }}">
                                <i class="fa-solid fa-{{ $user->isAdmin() ? 'crown' : 'cash-register' }}" style="font-size:10px;"></i>
                                {{ $user->level->nama_level ?? '-' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-light);">{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn-secondary btn-sm" onclick="editUser({{ $user->toJson() }})">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                @if($user->kd_user !== auth()->user()->kd_user)
                                <button class="btn-danger btn-sm" onclick="confirmDeleteUser('{{ $user->kd_user }}', '{{ $user->name }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state"><div class="empty-icon">👤</div><p>Belum ada user terdaftar</p></div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination mt-24">{{ $users->withQueryString()->links('vendor.pagination.simple') }}</div>

{{-- CREATE MODAL --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-plus" style="margin-right:7px;"></i>Tambah User</span>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.user.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username unik" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Alamat email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Level</label>
                    <select name="level_id" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->nama_level }}</option>
                        @endforeach
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
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-pen" style="margin-right:7px;"></i>Edit User</span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="editUsername" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru <span style="font-weight:400;color:var(--text-light);">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Level</label>
                    <select name="level_id" id="editLevel" class="form-control" required>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->nama_level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-box" style="max-width:360px;">
        <div class="modal-body" style="text-align:center;padding:32px 24px 20px;">
            <div style="width:64px;height:64px;border-radius:50%;background:#fde8e8;border:2px solid #f5c6c6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;color:var(--danger);">
                <i class="fa-solid fa-trash"></i>
            </div>
            <h4 style="font-size:18px;font-weight:700;color:var(--text-dark);margin-bottom:7px;">Hapus User?</h4>
            <p style="color:var(--text-light);font-size:13px;margin-bottom:24px;">
                User "<strong id="deleteUserName"></strong>" akan dihapus permanen.
            </p>
            <div style="display:flex;gap:12px;justify-content:center;">
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger" style="padding:9px 28px;font-size:14px;">Hapus</button>
                </form>
                <button class="btn-secondary" style="padding:9px 28px;font-size:14px;" onclick="closeModal('deleteModal')">Batal</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function editUser(u) {
    document.getElementById('editName').value     = u.name;
    document.getElementById('editUsername').value = u.username;
    document.getElementById('editEmail').value    = u.email;
    document.getElementById('editLevel').value    = u.level_id;
    document.getElementById('editForm').action    = '/admin/user/' + u.kd_user;
    openModal('editModal');
}

function confirmDeleteUser(kd, nama) {
    document.getElementById('deleteUserName').textContent = nama;
    document.getElementById('deleteForm').action = '/admin/user/' + kd;
    openModal('deleteModal');
}

@if($errors->any())
    openModal('createModal');
@endif
</script>
@endpush
@endsection