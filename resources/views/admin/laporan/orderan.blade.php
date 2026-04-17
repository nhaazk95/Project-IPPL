@extends('layouts.app')
@section('title', 'Laporan Orderan — Dapur Nusantara')
@section('page-title', 'Laporan Orderan per Periode')

@section('content')

<div class="page-header mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-clipboard-list" style="margin-right:8px;color:var(--gold);"></i>Laporan Orderan per Periode</p>
        <p class="ph-sub">Filter berdasarkan rentang tanggal</p>
    </div>
    <div style="display:flex;gap:9px;flex-wrap:wrap;">
        <a href="{{ route('admin.laporan.export', request()->all()) }}" class="btn-secondary">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
        <button class="btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Cetak
        </button>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-20">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-filter" style="margin-right:7px;"></i>Filter Periode</span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan.orderan') }}"
              style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap;">
            <div class="form-group" style="margin:0;flex:1;min-width:155px;">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control"
                    value="{{ $dari ?? now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:155px;">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control"
                    value="{{ $sampai ?? now()->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="grid-3 mb-20">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalTransaksi ?? 0 }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" style="font-size:17px;">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-chart-bar"></i></div>
        <div>
            <div class="stat-value" style="font-size:17px;">Rp {{ number_format($rataRata ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Rata-rata / Transaksi</div>
        </div>
    </div>
</div>

{{-- Grafik per hari --}}
@if(($perHari ?? collect())->count() > 1)
<div class="card mb-20">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-chart-area" style="margin-right:7px;"></i>Grafik Pendapatan Harian</span>
    </div>
    <div class="card-body">
        <canvas id="hariChart" height="160"></canvas>
    </div>
</div>
@endif

{{-- Top Menu --}}
@if(($topMenus ?? collect())->count())
<div class="card mb-20">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-fire" style="margin-right:7px;"></i>Menu Terlaris Periode Ini</span>
    </div>
    <div class="card-body" style="padding:0;">
        @foreach($topMenus as $i => $menu)
        <div style="display:flex;align-items:center;gap:12px;padding:11px 18px;border-bottom:1px solid var(--cream-dark);">
            <div style="width:26px;height:26px;border-radius:50%;
                background:{{ $i === 0 ? 'var(--gold)' : ($i === 1 ? 'var(--cream-mid)' : 'var(--cream-dark)') }};
                color:{{ $i === 0 ? 'var(--brown)' : 'var(--text-mid)' }};
                display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">
                {{ $i + 1 }}
            </div>
            <span style="flex:1;font-weight:600;color:var(--brown);">{{ $menu->name_menu }}</span>
            <span style="font-weight:800;color:var(--gold-dark);">{{ $menu->total_terjual ?? 0 }}x terjual</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Tabel Transaksi --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-table" style="margin-right:7px;"></i>Detail Transaksi</span>
        <span style="font-size:11px;color:rgba(245,233,192,.45);">
            {{ \Carbon\Carbon::parse($dari ?? now()->startOfMonth())->format('d/m/Y') }}
            — {{ \Carbon\Carbon::parse($sampai ?? now())->format('d/m/Y') }}
        </span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>No. Meja</th>
                        <th>Kasir</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;font-size:12.5px;color:var(--brown);">{{ $trx->kd_transaksi }}</span></td>
                        <td><span class="badge badge-brown">Meja {{ $trx->order->no_meja ?? '-' }}</span></td>
                        <td>{{ $trx->kasir->name ?? '-' }}</td>
                        <td style="color:var(--text-light);font-size:12.5px;">{{ $trx->tanggal?->format('d/m/Y') }}</td>
                        <td style="color:var(--text-light);font-size:12.5px;">{{ $trx->waktu?->format('H:i') }}</td>
                        <td><strong style="color:var(--gold-dark);">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="6">
                        <div class="empty-state"><div class="empty-icon">📊</div><p>Tidak ada data pada periode ini</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const hariCtx = document.getElementById('hariChart')?.getContext('2d');
if (hariCtx) {
    new Chart(hariCtx, {
        type: 'line',
        data: {
            labels: @json(($perHari ?? collect())->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
            datasets: [{
                label: 'Pendapatan',
                data: @json(($perHari ?? collect())->pluck('total')),
                borderColor: '#c9a227',
                backgroundColor: 'rgba(201,162,39,.1)',
                borderWidth: 2.5,
                pointBackgroundColor: '#c9a227',
                pointRadius: 4,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false },
                tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID') }}
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0)+'k', font:{size:11} }, grid:{color:'rgba(0,0,0,.04)'} },
                x: { grid: { display: false }, ticks: { font:{size:11} } }
            }
        }
    });
}
</script>
@endpush