@extends('layouts.app')
@section('title', 'Dashboard — Dapur Nusantara')
@section('page-title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div class="flex-between mb-20">
    <div>
        <p class="ph-title">Selamat datang, {{ auth()->user()->name }}! 👋</p>
        <p class="ph-sub">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    <div style="background:rgba(201,162,39,.1);border:1px solid rgba(201,162,39,.2);
        border-radius:12px;padding:10px 18px;text-align:center;">
        <div style="font-size:10px;color:rgba(201,162,39,.6);font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Jam</div>
        <div id="dashClock" style="font-size:22px;font-weight:800;color:var(--gold);font-family:monospace;">--:--</div>
    </div>
</div>

{{-- 4 Stat Cards --}}
<div class="grid-4 mb-20">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalTransaksiHari }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" style="font-size:18px;">Rp {{ number_format($pendapatanHari, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-chair"></i></div>
        <div>
            <div class="stat-value">{{ $mejaAktif }}<span style="font-size:15px;color:var(--text-light);font-weight:400;">/{{ $totalMeja }}</span></div>
            <div class="stat-label">Meja Terisi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $orderPending }}</div>
            <div class="stat-label">Order Pending</div>
        </div>
    </div>
</div>

{{-- Chart + Recent --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start;">

    {{-- Grafik --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-column" style="margin-right:7px;"></i>Pendapatan 7 Hari Terakhir</span>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>

    {{-- Transaksi terkini --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="margin-right:7px;"></i>Terkini</span>
            <a href="{{ route('admin.transaksi.index') }}"
                style="font-size:11px;color:var(--gold);text-decoration:none;font-weight:700;
                background:rgba(201,162,39,.15);padding:2px 10px;border-radius:20px;">Semua</a>
        </div>
        @forelse($recentTransaksi as $trx)
        <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid var(--cream-dark);">
            <div style="width:32px;height:32px;background:var(--gold-soft);border-radius:50%;
                display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--gold-dark);flex-shrink:0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;font-size:11.5px;color:var(--brown);font-family:monospace;
                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $trx->kd_transaksi }}</div>
                <div style="font-size:11px;color:var(--text-light);">{{ $trx->waktu?->format('H:i') }}</div>
            </div>
            <div style="font-weight:800;font-size:12px;color:var(--gold-dark);white-space:nowrap;">
                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:24px;"><p>Belum ada transaksi</p></div>
        @endforelse
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function tick() {
    const el = document.getElementById('dashClock');
    if (el) { const n = new Date(); el.textContent = String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0'); }
    setTimeout(tick, 1000);
})();

const ctx = document.getElementById('revenueChart')?.getContext('2d');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                data: @json($chartData),
                backgroundColor: 'rgba(201,162,39,.25)',
                borderColor: '#c9a227',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(201,162,39,.45)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => 'Rp ' + c.raw.toLocaleString('id-ID') } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: { callback: v => 'Rp '+(v/1000).toFixed(0)+'k', font:{size:11} } },
                x: { grid: { display:false }, ticks: { font:{size:11} } }
            }
        }
    });
}
</script>
@endpush