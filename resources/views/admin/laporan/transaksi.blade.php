@extends('layouts.app')
@section('title', 'Laporan Kelola Transaksi — Admin')
@section('page-title', 'Laporan Kelola Transaksi')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.laporan.transaksi') }}">Laporan</a>
    <span class="sep">/</span>
    <span class="current">Kelola Transaksi</span>
@endsection

@section('content')

{{-- Cari Transaksi --}}
<div class="card mb-20" style="max-width:560px;">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-magnifying-glass" style="margin-right:8px;"></i>Cari Transaksi</span>
    </div>
    <div class="card-body" style="padding:16px 18px;">
        <form method="GET" action="{{ route('admin.laporan.transaksi') }}" style="display:flex;gap:10px;align-items:center;">
            <select name="kd_transaksi" class="form-control" style="flex:1;">
                <option value="">— Pilih Transaksi —</option>
                @foreach($semuaTransaksi as $t)
                    <option value="{{ $t->kd_transaksi }}" {{ request('kd_transaksi')==$t->kd_transaksi?'selected':'' }}>
                        {{ $t->kd_transaksi }} — {{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-gold" style="padding:9px 16px;">
                <i class="fa-solid fa-search"></i> Cari
            </button>
        </form>
    </div>
</div>

{{-- Data Tabel --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table-list" style="margin-right:8px;"></i>Data Semua Transaksi</span>
    </div>

    {{-- Tanggal cetak --}}
    <div style="padding:10px 18px 0;text-align:right;">
        <span style="font-size:12px;color:var(--text-light);">Tanggal Cetak: {{ now()->format('Y-m-d') }}</span>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table id="laporanTable">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Nama Kasir</th>
                        <th>Total Harga</th>
                        <th>Tanggal Beli</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                    <tr>
                        <td style="font-weight:700;color:var(--brown);font-family:monospace;font-size:13px;">
                            {{ $trx->kd_transaksi }}
                        </td>
                        <td>{{ $trx->kasir->name ?? '-' }}</td>
                        <td>
                            <strong style="color:var(--gold-dark);">
                                Rp. {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td style="font-size:13px;color:var(--text-light);">
                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state" style="padding:36px 24px;">
                                <div class="empty-icon">🧾</div>
                                <p>Belum ada data transaksi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="padding:14px 18px;border-top:1px solid var(--cream-dark);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        {{-- Kiri --}}
        <button onclick="window.print()" class="btn-gold btn-sm">
            <i class="fa-solid fa-print"></i> Print
        </button>
        <a href="{{ route('admin.transaksi.index') }}" class="btn-brown btn-sm">
            <i class="fa-solid fa-cash-register"></i> Order
        </a>
        <button onclick="window.location.reload()" class="btn-secondary btn-sm">
            <i class="fa-solid fa-rotate"></i> Reload
        </button>

        {{-- Kanan --}}
        <div style="margin-left:auto;">
            <a href="{{ route('admin.laporan.transaksi.export', request()->all()) }}" class="btn-gold btn-sm">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
</div>

{{-- Laporan Orderan sub-nav --}}
<div style="margin-top:16px;display:flex;gap:10px;">
    <a href="{{ route('admin.laporan.transaksi') }}"
        class="nav-tab {{ request()->routeIs('admin.laporan.transaksi') && !request()->routeIs('admin.laporan.orderan') ? 'active' : '' }}">
        <i class="fa-solid fa-receipt"></i> Kelola Transaksi
    </a>
    <a href="{{ route('admin.laporan.orderan') }}"
        class="nav-tab {{ request()->routeIs('admin.laporan.orderan') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-list"></i> Orderan per Periode
    </a>
</div>

@endsection

@push('styles')
<style>
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
    .sidebar,.topbar,.breadcrumb-bar,.card:first-of-type,
    .card + .card .card-header ~ div:last-child,
    div[style*="margin-top:16px"] { display:none!important; }
    .main-content { margin-left:0!important; }
    .page-content { padding:0!important; }
}
</style>
@endpush