@extends('layouts.app')
@section('title', 'Level User — Admin')
@section('page-title', 'Level User')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Level User</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-shield-halved" style="color:var(--gold);margin-right:8px;"></i>Level / Role User</p>
        <p class="ph-sub">{{ $levels->count() }} level terdaftar</p>
    </div>
</div>

{{-- Level Cards --}}
<div class="grid-3 mb-24">
    @foreach($levels as $level)
    @php
        $icons = ['admin' => 'fa-crown', 'kasir' => 'fa-cash-register', 'pelanggan' => 'fa-user'];
        $icon  = $icons[strtolower($level->nama_level)] ?? 'fa-shield-halved';
    @endphp
    <div class="card" style="overflow:visible;">
        <div style="padding:24px 20px;text-align:center;border-bottom:1px solid var(--cream-dark);">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--brown);color:var(--gold);
                display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px;">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--brown);margin-bottom:4px;">
                {{ $level->nama_level }}
            </div>
            <div style="font-size:13px;color:var(--text-light);">
                <span class="badge badge-gold">{{ $level->users_count }} pengguna</span>
            </div>
        </div>
        <div style="padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <span style="font-size:12px;font-family:monospace;color:var(--text-light);">ID: {{ $level->id }}</span>
            <button class="btn-secondary btn-sm" onclick="editLevel({{ $level->toJson() }})">
                <i class="fa-solid fa-pen"></i> Edit
            </button>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table" style="margin-right:7px;"></i>Tabel Level</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Level</th>
                    <th style="text-align:center;">Jumlah User</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($levels as $level)
                <tr>
                    <td style="font-family:monospace;color:var(--text-light);">{{ $level->id }}</td>
                    <td>
                        <span class="badge {{ strtolower($level->nama_level)==='admin' ? 'badge-brown' : 'badge-gold' }}" style="font-size:12px;">
                            {{ $level->nama_level }}
                        </span>
                    </td>
                    <td style="text-align:center;font-weight:700;color:var(--text-dark);">{{ $level->users_count }}</td>
                    <td style="text-align:center;">
                        <button class="btn-secondary btn-sm" onclick="editLevel({{ $level->toJson() }})">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square" style="margin-right:7px;"></i>Edit Level</span>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Level</label>
                    <input type="text" name="nama_level" id="eNamaLevel" class="form-control"
                        placeholder="Contoh: Admin" required>
                    <p style="font-size:12px;color:var(--text-light);margin-top:6px;">
                        <i class="fa-solid fa-triangle-exclamation" style="color:var(--gold-dark);"></i>
                        Perubahan nama level mempengaruhi akses seluruh user dengan level ini.
                    </p>
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
function editLevel(lv) {
    document.getElementById('eNamaLevel').value  = lv.nama_level;
    document.getElementById('editForm').action   = '/admin/level/' + lv.id;
    openModal('editModal');
}
</script>
@endpush