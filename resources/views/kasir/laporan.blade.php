@extends('layouts.app')
@section('title', 'Laporan — Kasir')
@section('page-title', 'Laporan Transaksi')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Laporan</span>
@endsection

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-file-invoice" style="margin-right:8px;color:var(--gold);"></i>Laporan Semua Transaksi</p>
        <p class="ph-sub">Riwayat seluruh transaksi</p>
    </div>
    <div style="display:flex;gap:9px;">
        <button onclick="window.print()" class="btn-secondary">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- Cari Transaksi --}}
<div class="card mb-20" style="max-width:500px;">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-magnifying-glass" style="margin-right:8px;"></i>Filter Transaksi</span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('kasir.laporan') }}" style="display:flex;gap:10px;">
            <select name="kd_transaksi" class="form-control" style="flex:1;">
                <option value="">Semua Transaksi</option>
                @foreach($transaksis as $t)
                    <option value="{{ $t->kd_transaksi }}" {{ request('kd_transaksi') == $t->kd_transaksi ? 'selected' : '' }}>
                        {{ $t->kd_transaksi }} — {{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-search"></i>
            </button>
            @if(request('kd_transaksi'))
                <a href="{{ route('kasir.laporan') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header" style="justify-content:space-between;">
        <span class="card-title"><i class="fa-solid fa-table-list" style="margin-right:8px;"></i>Data Semua Transaksi</span>
        <span style="font-size:11px;color:rgba(245,233,192,.5);">Dicetak: {{ now()->format('Y-m-d H:i') }}</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table id="laporanTable">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Kasir</th>
                        <th>Total Harga</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                        <tr>
                            <td style="font-weight:700;color:var(--brown);font-family:monospace;font-size:12.5px;">{{ $trx->kd_transaksi }}</td>
                            <td>{{ $trx->kasir->name ?? '-' }}</td>
                            <td><strong style="color:var(--gold-dark);">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</strong></td>
                            <td style="color:var(--text-light);font-size:12.5px;">{{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <div class="empty-state"><div class="empty-icon">🧾</div><p>Tidak ada transaksi</p></div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .breadcrumb-bar,
    .card:first-of-type, .card:nth-of-type(2) .card-header ~ *,
    .btn-secondary, .btn-gold { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
}
</style>
@endpush

@endsection