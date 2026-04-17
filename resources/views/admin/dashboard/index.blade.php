@extends('layouts.app')
@section('title', 'Dashboard — Dapur Nusantara')
@section('page-title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-20">
    <div>
        <p class="ph-title">Selamat datang, {{ auth()->user()->name }}! 👋</p>
        <p class="ph-sub">Ringkasan aktivitas Dapur Nusantara hari ini · {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    <div style="background:rgba(201,162,39,.1);border:1px solid rgba(201,162,39,.2);border-radius:12px;padding:10px 16px;text-align:center;">
        <div style="font-size:11px;color:rgba(201,162,39,.7);font-weight:600;letter-spacing:.07em;text-transform:uppercase;">Jam Sekarang</div>
        <div style="font-size:22px;font-weight:800;color:var(--gold);font-family:monospace;" id="dashClock">--:--</div>
    </div>
</div>

{{-- Stats --}}
<div class="grid-4 mb-20">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalTransaksiHari ?? 0 }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" style="font-size:17px;">Rp {{ number_format($pendapatanHari ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-chair"></i></div>
        <div>
            <div class="stat-value">{{ $mejaAktif ?? 0 }}<span style="font-size:16px;color:var(--text-light);font-weight:400;">/{{ $totalMeja ?? 0 }}</span></div>
            <div class="stat-label">Meja Terisi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $orderPending ?? 0 }}</div>
            <div class="stat-label">Order Pending</div>
        </div>
    </div>
</div>

{{-- Chart + Recent Transactions --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-chart-column" style="margin-right:6px;"></i>Pendapatan 7 Hari Terakhir</span>
        <span style="font-size:11px;color:rgba(245,233,192,.45);">Update otomatis</span>
    </div>
    <div class="card-body">
        <canvas id="revenueChart" height="240"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="margin-right:6px;"></i>Transaksi Terkini</span>
        <a href="{{ route('admin.transaksi.index') }}"
           style="font-size:11px;color:var(--gold);text-decoration:none;font-weight:700;background:rgba(201,162,39,.15);padding:2px 10px;border-radius:20px;">
           Semua
        </a>
    </div>
    <div>
        @forelse(($recentTransaksi ?? []) as $trx)
        @php /** @var \App\Models\Transaksi $trx */ @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid var(--cream-dark);">
            <div style="width:34px;height:34px;background:var(--gold-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--gold-dark);flex-shrink:0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;font-size:12px;color:var(--brown);font-family:monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $trx->kd_transaksi ?? '-' }}</div>
                <div style="font-size:11px;color:var(--text-light);">{{ $trx->waktu ? \Carbon\Carbon::parse($trx->waktu)->format('H:i') : '-' }}</div>
            </div>
            <div style="font-weight:800;font-size:13px;color:var(--gold-dark);white-space:nowrap;">Rp {{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}</div>
        </div>
        @empty
        <div class="empty-state" style="padding:28px 20px;">
            <div class="empty-icon" style="font-size:2rem;">📋</div>
            <p>Belum ada transaksi hari ini</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Meja Status + Top Menu --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-chair" style="margin-right:6px;"></i>Status Meja</span>
        <a href="{{ route('admin.meja.index') }}" style="font-size:11px;color:var(--gold);text-decoration:none;font-weight:700;background:rgba(201,162,39,.15);padding:2px 10px;border-radius:20px;">Kelola</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(78px,1fr));gap:10px;">
            @forelse(($mejas ?? []) as $meja)
            @php /** @var \App\Models\Meja $meja */ @endphp
            @php $terisi = ($meja->status ?? '') === 'terisi'; @endphp
            <div style="
                background:{{ $terisi ? 'rgba(201,162,39,.12)' : 'var(--cream)' }};
                border:2px solid {{ $terisi ? 'var(--gold)' : 'var(--cream-dark)' }};
                border-radius:12px;padding:10px 6px;text-align:center;
            ">
                <div style="font-size:18px;margin-bottom:3px;">🪑</div>
                <div style="font-weight:700;font-size:13px;color:{{ $terisi ? 'var(--gold-dark)' : 'var(--brown)' }};">{{ $meja->no_meja ?? '-' }}</div>
                <div style="font-size:10px;margin-top:2px;color:{{ $terisi ? 'var(--gold-dark)' : 'var(--text-light)' }};font-weight:600;">{{ $terisi ? 'Terisi' : 'Kosong' }}</div>
            </div>
            @empty
            <div class="empty-state" style="grid-column:1/-1;padding:24px;">
                <p>Belum ada meja</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-fire" style="margin-right:6px;"></i>Menu Terlaris Bulan Ini</span>
    </div>
    <div class="card-body" style="padding:0;">
        @forelse(($topMenus ?? []) as $i => $menu)
        @php /** @var \App\Models\Menu $menu */ @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid var(--cream-dark);">
            <div style="width:28px;height:28px;border-radius:50%;
                background:{{ $i === 0 ? 'var(--gold)' : ($i === 1 ? 'var(--cream-mid)' : 'var(--cream-dark)') }};
                color:{{ $i === 0 ? 'var(--brown)' : 'var(--text-mid)' }};
                display:flex;align-items:center;justify-content:center;
                font-size:12px;font-weight:800;flex-shrink:0;">
                {{ $i + 1 }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;font-size:13px;color:var(--brown);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $menu->name_menu ?? '-' }}</div>
                <div style="font-size:11px;color:var(--text-light);">Rp {{ number_format($menu->harga ?? 0, 0, ',', '.') }}</div>
            </div>
            <div style="font-weight:800;font-size:13px;color:var(--gold-dark);">{{ $menu->total_terjual ?? 0 }}x</div>
        </div>
        @empty
        <div class="empty-state" style="padding:28px 20px;"><p>Belum ada data menu</p></div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// Dashboard clock
(function() {
    function tick() {
        const el = document.getElementById('dashClock');
        if (!el) return;
        const n = new Date();
        el.textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
    }
    tick(); setInterval(tick, 1000);
})();

// Revenue Chart
const ctx = document.getElementById('revenueChart')?.getContext('2d');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($chartData ?? []),
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
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: {
                        callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k',
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}
</script>
@endpush