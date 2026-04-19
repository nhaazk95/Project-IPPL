@extends('layouts.app')
@section('title', 'Level — Admin')
@section('page-title', 'Level')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Level</span>
@endsection

@section('content')

<div class="level-layout">

    {{-- ===== KIRI: Form Ubah Level ===== --}}
    <div class="level-form-card">
        <div class="level-form-header">
            <i class="fa-solid fa-pen-to-square" style="margin-right:8px;"></i>Ubah Level
        </div>
        <div class="level-form-body">
            <form method="POST" id="ubahLevelForm" action="">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label" style="color:var(--text-mid);">Level</label>
                    <input type="text" name="nama_level" id="inputNamaLevel"
                        class="form-control" placeholder="Pilih level dari tabel..."
                        required readonly
                        style="background:var(--cream);cursor:not-allowed;">
                </div>

                <div id="formHint" style="font-size:12px;color:var(--text-light);margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-arrow-right" style="color:var(--gold-dark);font-size:10px;"></i>
                    Klik tombol <strong style="color:var(--brown);">Edit</strong> di tabel untuk mengubah nama level
                </div>

                <button type="submit" id="btnUpdate" class="btn-update-level" disabled>
                    <i class="fa-solid fa-check"></i> Update
                </button>
            </form>

            {{-- Level chips summary --}}
            <div style="margin-top:20px;padding-top:16px;border-top:1px dashed var(--cream-dark);">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-light);margin-bottom:10px;">
                    Ringkasan Level
                </div>
                @foreach($levels as $lv)
                @php
                    $icons = ['admin'=>'fa-crown','kasir'=>'fa-cash-register','pelanggan'=>'fa-user'];
                    $ic = $icons[strtolower($lv->nama_level)] ?? 'fa-shield-halved';
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:8px 12px;border-radius:10px;margin-bottom:6px;
                    background:var(--cream);border:1px solid var(--cream-dark);">
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:var(--brown);
                            color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;">
                            <i class="fa-solid {{ $ic }}"></i>
                        </div>
                        <span style="font-weight:700;font-size:13px;color:var(--brown);">{{ $lv->nama_level }}</span>
                    </div>
                    <span style="font-size:12px;font-weight:700;color:var(--text-light);">
                        {{ $lv->users_count }} user
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== KANAN: Tabel Data Level ===== --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-shield-halved" style="margin-right:8px;"></i>Data Level</span>
            </div>

            {{-- Show & Search bar --}}
            <div style="padding:12px 18px;border-bottom:1px solid var(--cream-dark);
                display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-light);">
                    Show
                    <select id="showEntries" class="form-control" style="width:60px;padding:4px 6px;font-size:12px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    entries
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-light);">
                    Search
                    <input type="text" id="searchLevel" class="form-control"
                        style="width:150px;padding:5px 10px;font-size:12px;"
                        placeholder="Cari..." oninput="filterLevel()">
                </div>
            </div>

            {{-- Tabel --}}
            <div class="card-body" style="padding:0;">
                <table id="levelTable">
                    <thead>
                        <tr>
                            <th style="width:60px;text-align:center;">No</th>
                            <th>Nama</th>
                            <th style="text-align:center;width:120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="levelTbody">
                        @foreach($levels as $i => $lv)
                        <tr class="level-row" data-nama="{{ strtolower($lv->nama_level) }}">
                            <td style="text-align:center;color:var(--text-light);font-weight:600;">{{ $i + 1 }}.</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    @php
                                        $icons = ['admin'=>'fa-crown','kasir'=>'fa-cash-register','pelanggan'=>'fa-user'];
                                        $ic = $icons[strtolower($lv->nama_level)] ?? 'fa-shield-halved';
                                    @endphp
                                    <div style="width:32px;height:32px;border-radius:8px;background:var(--brown);
                                        color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">
                                        <i class="fa-solid {{ $ic }}"></i>
                                    </div>
                                    <span style="font-weight:700;font-size:14px;color:var(--text-dark);">
                                        {{ $lv->nama_level }}
                                    </span>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button class="btn-level-edit"
                                        onclick="pilihLevel('{{ $lv->id }}','{{ $lv->nama_level }}')">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.level.destroy', $lv->id) }}"
                                        onsubmit="return confirm('Hapus level {{ $lv->nama_level }}? Semua user dengan level ini akan terpengaruh!')"
                                        style="margin:0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-level-del">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer info + pagination --}}
            <div style="padding:12px 18px;border-top:1px solid var(--cream-dark);
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
    </div>

</div>

@endsection

@push('styles')
<style>
.level-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    align-items: start;
}
.level-form-card {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid var(--cream-dark);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.level-form-header {
    background: var(--brown);
    color: var(--gold);
    padding: 14px 18px;
    font-weight: 700;
    font-size: 14px;
}
.level-form-body { padding: 20px 18px; }

.btn-update-level {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 22px;
    background: rgba(201,162,39,.2);
    border: 1.5px solid var(--gold);
    border-radius: 10px;
    color: var(--brown); font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: all .18s;
    opacity: .5;
}
.btn-update-level:not([disabled]) { opacity: 1; }
.btn-update-level:not([disabled]):hover {
    background: var(--brown); color: var(--gold);
}

/* Table buttons */
.btn-level-edit {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 13px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    background: var(--cream); border: 1.5px solid var(--cream-mid);
    color: var(--text-mid); cursor: pointer; transition: var(--transition);
    font-family: inherit;
}
.btn-level-edit:hover { background: var(--brown); color: var(--gold); border-color: var(--brown); }
.btn-level-del {
    display: inline-flex; align-items: center;
    padding: 5px 9px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    background: #fde8e8; border: 1.5px solid #f5c6c6;
    color: var(--danger); cursor: pointer; transition: var(--transition);
    font-family: inherit;
}
.btn-level-del:hover { background: #fbd5d5; }

/* Pagination buttons */
.btn-pg {
    padding: 6px 14px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    background: #fff; border: 1.5px solid var(--cream-dark);
    color: var(--text-mid); cursor: pointer; transition: var(--transition);
    font-family: inherit;
}
.btn-pg:hover:not([disabled]) { background: var(--brown); color: var(--gold); border-color: var(--brown); }
.btn-pg[disabled] { opacity: .4; cursor: not-allowed; }
.pg-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    font-size: 13px; font-weight: 700;
    border: 1.5px solid var(--cream-dark);
    background: #fff; color: var(--text-mid);
}
.pg-num.active { background: var(--brown); color: var(--gold); border-color: var(--brown); }

@media (max-width: 900px) {
    .level-layout { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let perPage = 10;
const allRows = Array.from(document.querySelectorAll('.level-row'));

function pilihLevel(id, nama) {
    document.getElementById('inputNamaLevel').value = nama;
    document.getElementById('inputNamaLevel').readOnly = false;
    document.getElementById('inputNamaLevel').style.background = '#fff';
    document.getElementById('inputNamaLevel').style.cursor = 'text';
    document.getElementById('ubahLevelForm').action = '/admin/level/' + id;
    document.getElementById('btnUpdate').disabled = false;
    document.getElementById('formHint').style.display = 'none';

    // Highlight baris yang dipilih
    allRows.forEach(r => r.style.background = '');
    document.querySelectorAll('.btn-level-edit').forEach(b => {
        if (b.getAttribute('onclick').includes("'" + id + "'")) {
            b.closest('tr').style.background = 'rgba(201,162,39,.08)';
        }
    });

    // Scroll ke form di mobile
    document.getElementById('ubahLevelForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    document.getElementById('inputNamaLevel').focus();
}

function filterLevel() {
    const q = document.getElementById('searchLevel').value.toLowerCase();
    currentPage = 1;
    renderTable(q);
}

function renderTable(q = '') {
    const filtered = allRows.filter(r =>
        !q || r.dataset.nama.includes(q)
    );
    const total = filtered.length;
    const start = (currentPage - 1) * perPage;
    const end   = Math.min(start + perPage, total);

    allRows.forEach(r => r.style.display = 'none');
    filtered.slice(start, end).forEach(r => r.style.display = '');

    document.getElementById('showingInfo').textContent =
        total === 0
            ? 'No entries found'
            : `Showing ${start + 1} to ${end} of ${total} entries`;

    document.getElementById('pgNum').textContent = currentPage;
    document.getElementById('btnPrev').disabled = currentPage === 1;
    document.getElementById('btnNext').disabled = end >= total;
}

function changePage(dir) {
    const q = document.getElementById('searchLevel').value.toLowerCase();
    const filtered = allRows.filter(r => !q || r.dataset.nama.includes(q));
    const maxPage = Math.ceil(filtered.length / perPage);
    currentPage = Math.max(1, Math.min(currentPage + dir, maxPage));
    renderTable(q);
}

document.getElementById('showEntries').addEventListener('change', function() {
    perPage = parseInt(this.value);
    currentPage = 1;
    renderTable(document.getElementById('searchLevel').value.toLowerCase());
});

// Init
renderTable();
</script>
@endpush