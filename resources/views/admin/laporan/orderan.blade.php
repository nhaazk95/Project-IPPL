@extends('layouts.app')
@section('title', 'Laporan Orderan per Periode — Admin')
@section('page-title', 'Laporan Orderan per Periode')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.laporan.transaksi') }}">Laporan</a>
    <span class="sep">/</span>
    <span class="current">Orderan per Periode</span>
@endsection

@section('content')

{{-- Periode Filter Card --}}
<div class="card mb-24">
    <div class="card-header">
        <span class="card-title" style="font-size:15px;">Periode</span>
    </div>
    <div class="card-body" style="padding:24px 26px;">
        <form method="GET" action="{{ route('admin.laporan.orderan') }}" id="filterForm">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:20px;max-width:680px;">
                <div>
                    <label class="form-label" style="font-size:13px;color:var(--text-dark);margin-bottom:8px;">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control periode-input"
                        value="{{ request('dari') }}" placeholder="dd/mm/yy">
                </div>
                <div>
                    <label class="form-label" style="font-size:13px;color:var(--text-dark);margin-bottom:8px;">Ke Tanggal</label>
                    <input type="date" name="sampai" class="form-control periode-input"
                        value="{{ request('sampai') }}" placeholder="dd/mm/yy">
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn-periode-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
                <button type="button" class="btn-periode-reload" onclick="reloadPage()">
                    Reload
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Results Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fa-solid fa-table-list" style="margin-right:8px;"></i>
            Data Orderan
            @if(request('dari') && request('sampai'))
                <span style="font-size:11px;font-weight:400;color:rgba(245,233,192,.55);margin-left:8px;">
                    {{ \Carbon\Carbon::parse(request('dari'))->format('d/m/Y') }}
                    — {{ \Carbon\Carbon::parse(request('sampai'))->format('d/m/Y') }}
                </span>
            @endif
        </span>
        @if(request('dari'))
        <a href="{{ route('admin.laporan.export', request()->all()) }}"
            style="font-size:11px;color:var(--gold);background:rgba(201,162,39,.15);padding:3px 12px;border-radius:20px;text-decoration:none;font-weight:700;">
            <i class="fa-solid fa-file-csv"></i> Export
        </a>
        @endif
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tabel-orderan">
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Pelanggan</th>
                        <th style="text-align:center;">No Meja</th>
                        <th>Nama Menu</th>
                        <th style="text-align:center;">Jumlah</th>
                        <th>Sub Total</th>
                        <th>Harga</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td style="font-weight:700;font-family:monospace;font-size:12.5px;color:var(--brown);">
                            {{ $row['kode_order'] }}
                        </td>
                        <td>{{ $row['pelanggan'] }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-brown" style="font-size:11px;">{{ $row['no_meja'] }}</span>
                        </td>
                        <td style="font-weight:600;color:var(--text-dark);">{{ $row['nama_menu'] }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $row['jumlah'] }}</td>
                        <td style="font-weight:700;color:var(--text-mid);">
                            Rp. {{ number_format($row['sub_total'], 0, ',', '.') }}
                        </td>
                        <td style="font-weight:700;color:var(--gold-dark);">
                            Rp. {{ number_format($row['harga'], 0, ',', '.') }}
                        </td>
                        <td style="font-size:12.5px;color:var(--text-light);">
                            {{ $row['tanggal'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state" style="padding:48px 24px;">
                                <div class="empty-icon">📊</div>
                                @if(!request('dari'))
                                    <p style="font-weight:600;color:var(--brown);margin-bottom:4px;">Pilih periode terlebih dahulu</p>
                                    <p style="font-size:12px;">Masukkan rentang tanggal dan klik <strong>Search</strong></p>
                                @else
                                    <p>Tidak ada data pada periode ini</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($rows) > 0)
                <tfoot>
                    <tr style="background:var(--cream);">
                        <td colspan="4" style="text-align:right;font-weight:700;font-size:13px;padding:10px 14px;color:var(--text-mid);">
                            Total ({{ count($rows) }} item)
                        </td>
                        <td style="text-align:center;font-weight:800;font-size:13px;padding:10px 14px;">
                            {{ collect($rows)->sum('jumlah') }}
                        </td>
                        <td style="font-weight:800;font-size:13px;padding:10px 14px;color:var(--text-dark);">
                            Rp. {{ number_format(collect($rows)->sum('sub_total'), 0, ',', '.') }}
                        </td>
                        <td colspan="2" style="padding:10px 14px;font-weight:800;font-size:13px;color:var(--gold-dark);">
                            Rp. {{ number_format(collect($rows)->sum('harga'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Sub nav --}}
<div style="margin-top:16px;display:flex;gap:10px;">
    <a href="{{ route('admin.laporan.transaksi') }}" class="nav-tab">
        <i class="fa-solid fa-receipt"></i> Kelola Transaksi
    </a>
    <a href="{{ route('admin.laporan.orderan') }}" class="nav-tab active">
        <i class="fa-solid fa-clipboard-list"></i> Orderan per Periode
    </a>
</div>

@endsection

@push('styles')
<style>
.periode-input {
    background: #fff;
    border: 1.5px solid var(--cream-dark);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: var(--text-dark);
    transition: border-color .2s;
    width: 100%;
}
.periode-input:focus { border-color: var(--gold); outline: none; }

.btn-periode-search {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 22px;
    background: rgba(201,162,39,.2);
    border: 1.5px solid var(--gold);
    border-radius: 10px;
    color: var(--brown);
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: all .2s;
}
.btn-periode-search:hover { background: rgba(201,162,39,.35); }

.btn-periode-reload {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 22px;
    background: rgba(201,162,39,.15);
    border: 1.5px solid rgba(201,162,39,.35);
    border-radius: 10px;
    color: var(--brown);
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: all .2s;
}
.btn-periode-reload:hover { background: rgba(201,162,39,.25); }

.tabel-orderan thead th {
    background: var(--brown);
    color: var(--gold);
    font-size: 12.5px;
    padding: 12px 14px;
}
.tabel-orderan tbody tr:hover { background: var(--cream); }
.tabel-orderan tbody td { padding: 10px 14px; font-size: 13px; }

.nav-tab {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600;
    text-decoration:none;
    background:#fff; color:var(--text-mid);
    border:1.5px solid var(--cream-dark);
    transition:var(--transition);
}
.nav-tab:hover, .nav-tab.active {
    background:var(--brown); color:var(--gold);
    border-color:var(--brown);
}
@media print {
    .sidebar,.topbar,div[style*="margin-top:16px"],
    .breadcrumb-bar,.card:first-of-type .card-body form { display:none!important; }
    .main-content { margin-left:0!important; }
}
</style>
@endpush

@push('scripts')
<script>
function reloadPage() {
    window.location.href = '{{ route("admin.laporan.orderan") }}';
}
</script>
@endpush