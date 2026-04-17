@extends('layouts.app')
@section('title', 'Riwayat Transaksi — Kasir')
@section('page-title', 'Riwayat Transaksi')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Transaksi</span>
@endsection

@section('content')

<div class="flex-between mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-receipt" style="margin-right:8px;color:var(--gold);"></i>Transaksi Hari Ini</p>
        <p class="ph-sub">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    <div class="search-box">
        <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" placeholder="Cari transaksi..." oninput="filterTable()">
    </div>
</div>

{{-- Summary --}}
<div class="grid-3 mb-20">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $transaksis->count() }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" style="font-size:17px;">Rp {{ number_format($transaksis->sum('total_harga'), 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-chart-bar"></i></div>
        <div>
            @php $avg = $transaksis->count() > 0 ? $transaksis->sum('total_harga') / $transaksis->count() : 0; @endphp
            <div class="stat-value" style="font-size:17px;">Rp {{ number_format($avg, 0, ',', '.') }}</div>
            <div class="stat-label">Rata-rata / Transaksi</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table-list" style="margin-right:7px;"></i>Data Transaksi</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table id="trxTable">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>No. Meja</th>
                        <th>Pelanggan</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;font-size:12.5px;color:var(--brown);">{{ $trx->kd_transaksi }}</span></td>
                        <td><span class="badge badge-brown">Meja {{ $trx->order->no_meja ?? '-' }}</span></td>
                        <td style="color:var(--text-mid);">{{ $trx->order->nama_user ?? '-' }}</td>
                        <td style="color:var(--text-light);font-size:12.5px;">{{ $trx->waktu?->format('H:i') }}</td>
                        <td><strong style="color:var(--gold-dark);">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</strong></td>
                        <td style="text-align:center;">
                            <a href="{{ route('kasir.struk', $trx->kd_transaksi) }}" class="btn-secondary btn-sm" target="_blank">
                                <i class="fa-solid fa-print"></i> Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6">
                        <div class="empty-state"><div class="empty-icon">🧾</div><p>Belum ada transaksi hari ini</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#trxTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endpush