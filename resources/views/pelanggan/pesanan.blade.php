@extends('layouts.pelanggan')
@section('title', 'Pesanan Saya — Dapur Nusantara')

@push('styles')
<style>
    :root { --coklat:#2c1810; --emas:#c9a227; --krem:#faf5ee; --krem-dark:#f0e8d8; --teks-muted:#7a6552; }
    body { background: var(--krem); }
    .pesanan-body { padding: 1rem; padding-bottom: 5rem; }

    /* Tab */
    .tab-bar { display:flex; background:#fff; border-radius:12px; border:1.5px solid var(--krem-dark); margin-bottom:1rem; overflow:hidden; }
    .tab-btn { flex:1; padding:.6rem; font-size:.82rem; font-weight:700; border:none; background:transparent; cursor:pointer; color:var(--teks-muted); transition:all .15s; font-family:inherit; }
    .tab-btn.active { background:var(--coklat); color:var(--emas); }

    /* Card pesanan */
    .pesanan-card { background:#fff; border-radius:16px; box-shadow:0 2px 10px rgba(44,24,16,.07); margin-bottom:1rem; overflow:hidden; border:1.5px solid var(--krem-dark); }
    .pesanan-header { background:var(--coklat); padding:.75rem 1rem; display:flex; justify-content:space-between; align-items:center; }
    .pesanan-header .kode { color:var(--emas); font-weight:700; font-size:.85rem; }
    .pesanan-header .waktu { color:rgba(255,255,255,.5); font-size:.72rem; margin-top:2px; }
    .status-badge { padding:.3em .9em; border-radius:20px; font-size:.7rem; font-weight:700; }
    .status-pending  { background:#fff3cd; color:#856404; }
    .status-diproses { background:#cfe2ff; color:#084298; }
    .status-siap     { background:#d1e7dd; color:#0a3622; }
    .status-selesai  { background:#e2e3e5; color:#41464b; }

    .pesanan-inner { padding:.85rem 1rem; }
    .meja-info { font-size:.78rem; color:var(--teks-muted); display:flex; align-items:center; gap:.3rem; margin-bottom:.6rem; }
    .pesanan-item { display:flex; justify-content:space-between; margin-bottom:.3rem; font-size:.875rem; }
    .pesanan-item .n { color:var(--teks-muted); }
    .pesanan-item .p { font-weight:600; color:var(--coklat); }
    .pesanan-divider { border:none; border-top:1.5px dashed #e8ddd0; margin:.5rem 0; }
    .pesanan-total { display:flex; justify-content:space-between; align-items:center; }
    .pesanan-total .label { font-weight:700; font-size:.88rem; color:var(--coklat); }
    .pesanan-total .amount { font-weight:800; color:var(--emas); font-size:1rem; }

    /* Info box status */
    .info-box { border-radius:8px; padding:.55rem .85rem; margin-top:.6rem; font-size:.8rem; display:flex; align-items:center; gap:.4rem; }
    .info-pending  { background:#fff3cd; color:#856404; }
    .info-diproses { background:#cfe2ff; color:#084298; }
    .info-siap     { background:#d1e7dd; color:#0a3622; }
    .info-selesai  { background:#e2e3e5; color:#41464b; }

    /* Nota card (muncul hanya jika selesai) */
    .nota-card {
        background: var(--coklat); border-radius:12px;
        padding:1rem; margin-top:.75rem;
    }
    .nota-header { color:var(--emas); font-weight:800; font-size:.9rem; text-align:center; margin-bottom:.75rem; letter-spacing:.04em; }
    .nota-row { display:flex; justify-content:space-between; font-size:.82rem; color:rgba(245,233,192,.75); margin-bottom:.35rem; }
    .nota-row .val { color:var(--emas); font-weight:600; }
    .nota-divider { border:none; border-top:1px dashed rgba(201,162,39,.3); margin:.6rem 0; }
    .nota-total { display:flex; justify-content:space-between; align-items:center; }
    .nota-total .lbl { font-weight:700; font-size:.9rem; color:rgba(245,233,192,.9); }
    .nota-total .amt { font-weight:800; font-size:1.1rem; color:var(--emas); }
    .nota-metode { text-align:center; margin-top:.6rem; font-size:.75rem; color:rgba(201,162,39,.6); }
    .nota-thanks { text-align:center; margin-top:.75rem; font-size:.8rem; color:rgba(245,233,192,.5); font-style:italic; }

    /* Empty */
    .empty-state { text-align:center; padding:3rem 1rem; }
    .empty-state i { font-size:3rem; color:var(--emas); display:block; margin-bottom:.75rem; }
    .empty-state p { color:var(--teks-muted); font-size:.875rem; }
</style>
@endpush

@section('content')

<div class="topbar">
    <a href="{{ route('pelanggan.beranda') }}" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
    <span class="topbar-icon"><i class="bi bi-receipt"></i></span>
    <span class="topbar-title">Pesanan Saya</span>
</div>

<div class="pesanan-body">

    @if(session('success'))
    <div style="background:#d4edda;color:#155724;font-size:.8rem;padding:.6rem 1rem;border-left:4px solid #27ae60;border-radius:8px;margin-bottom:.75rem;">
        {{ session('success') }}
    </div>
    @endif

    @php
        $aktif   = $orders->whereIn('status_order', ['pending','diproses','siap']);
        $riwayat = $orders->where('status_order', 'selesai');
    @endphp

    <div class="tab-bar">
        <button class="tab-btn active" id="tabAktif" onclick="switchTab('aktif')">
            Aktif
            @if($aktif->count() > 0)
                <span style="background:var(--emas);color:var(--coklat);border-radius:20px;padding:1px 7px;font-size:.68rem;margin-left:4px;">
                    {{ $aktif->count() }}
                </span>
            @endif
        </button>
        <button class="tab-btn" id="tabRiwayat" onclick="switchTab('riwayat')">Riwayat</button>
    </div>

    {{-- TAB AKTIF --}}
    <div id="panelAktif">
        @forelse($aktif as $order)
        @php
            $total = $order->detailOrders->sum('sub_total');
            $sc    = match($order->status_order) { 'diproses'=>'diproses', 'siap'=>'siap', default=>'pending' };
        @endphp
        <div class="pesanan-card">
            <div class="pesanan-header">
                <div>
                    <div class="kode">{{ $order->kd_order }}</div>
                    <div class="waktu">{{ \Carbon\Carbon::parse($order->waktu)->format('d/m/Y H:i') }}</div>
                </div>
                <span class="status-badge status-{{ $sc }}">{{ ucfirst($order->status_order) }}</span>
            </div>
            <div class="pesanan-inner">
                <div class="meja-info"><i class="bi bi-grid-3x3-gap"></i> Meja {{ $order->no_meja }}</div>

                @foreach($order->detailOrders as $item)
                <div class="pesanan-item">
                    <span class="n">{{ $item->menu->name_menu ?? '-' }} × {{ $item->total }}</span>
                    <span class="p">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
                </div>
                @endforeach

                <hr class="pesanan-divider">
                <div class="pesanan-total">
                    <span class="label">Total</span>
                    <span class="amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- Info status — belum selesai, nota belum bisa dilihat --}}
                @if($order->status_order === 'pending')
                <div class="info-box info-pending">
                    <i class="bi bi-hourglass-split"></i>
                    <strong>Menunggu konfirmasi kasir...</strong>&nbsp; Nota tersedia setelah pembayaran selesai.
                </div>
                @elseif($order->status_order === 'diproses')
                <div class="info-box info-diproses">
                    <i class="bi bi-fire"></i> Pesanan sedang dimasak. Nota tersedia setelah pembayaran selesai.
                </div>
                @elseif($order->status_order === 'siap')
                <div class="info-box info-siap">
                    <i class="bi bi-check-circle"></i> Pesanan siap! Silakan ke kasir untuk membayar.
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="bi bi-clock-history"></i>
            <p>Tidak ada pesanan aktif saat ini</p>
        </div>
        @endforelse
    </div>

    {{-- TAB RIWAYAT --}}
    <div id="panelRiwayat" style="display:none;">
        @forelse($riwayat as $order)
        @php
            $total   = $order->detailOrders->sum('sub_total');
            $trx     = $order->transaksi;
            $metode  = $trx?->metode ?? null;
            $metodeLabel = match($metode) { 'cash'=>'Tunai', 'debit'=>'Debit/Kartu', 'qris'=>'QRIS', default=>'—' };
        @endphp
        <div class="pesanan-card">
            <div class="pesanan-header">
                <div>
                    <div class="kode">{{ $order->kd_order }}</div>
                    <div class="waktu">{{ \Carbon\Carbon::parse($order->waktu)->format('d/m/Y H:i') }}</div>
                </div>
                <span class="status-badge status-selesai">Selesai</span>
            </div>
            <div class="pesanan-inner">
                <div class="meja-info"><i class="bi bi-grid-3x3-gap"></i> Meja {{ $order->no_meja }}</div>

                @foreach($order->detailOrders as $item)
                <div class="pesanan-item">
                    <span class="n">{{ $item->menu->name_menu ?? '-' }} × {{ $item->total }}</span>
                    <span class="p">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
                </div>
                @endforeach

                <hr class="pesanan-divider">
                <div class="pesanan-total">
                    <span class="label">Total</span>
                    <span class="amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- NOTA — hanya muncul setelah selesai, tampilan sama seperti struk kasir --}}
                @if($trx)
                <div style="background:#2c1810;border-radius:14px;overflow:hidden;margin-top:.85rem;">
                    {{-- Header struk --}}
                    <div style="padding:16px 16px 14px;text-align:center;border-bottom:1.5px dashed rgba(201,162,39,.25);">
                        <div style="font-size:22px;margin-bottom:4px;">☕</div>
                        <div style="font-family:'Playfair Display',serif;font-size:15px;font-weight:800;color:#c9a227;letter-spacing:2px;">DAPUR NUSANTARA</div>
                        <div style="font-size:10px;color:rgba(201,162,39,.5);margin-top:2px;">Cita Rasa Terbaik untuk Kamu</div>
                    </div>

                    {{-- Info transaksi --}}
                    <div style="padding:12px 16px;">
                        @php
                            $m = $trx->metode ?? 'cash';
                            $mLabel = match($m) { 'qris'=>'QRIS', 'debit'=>'Debit/Kartu', default=>'Tunai' };
                            $mIcon  = match($m) { 'qris'=>'📱', 'debit'=>'💳', default=>'💵' };
                        @endphp
                        <div style="display:flex;justify-content:space-between;font-size:11.5px;color:rgba(245,233,192,.6);padding:2px 0;">
                            <span>No. Transaksi</span>
                            <span style="color:var(--emas);font-weight:700;font-size:10.5px;font-family:monospace;">{{ $trx->kd_transaksi }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:11.5px;color:rgba(245,233,192,.6);padding:2px 0;">
                            <span>Tanggal</span>
                            <span style="color:rgba(245,233,192,.9);font-weight:600;">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:11.5px;color:rgba(245,233,192,.6);padding:2px 0;">
                            <span>Waktu</span>
                            <span style="color:rgba(245,233,192,.9);font-weight:600;">{{ \Carbon\Carbon::parse($trx->waktu)->format('H:i') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:11.5px;color:rgba(245,233,192,.6);padding:2px 0;">
                            <span>Meja</span>
                            <span style="color:rgba(245,233,192,.9);font-weight:600;">{{ $order->no_meja }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:11.5px;color:rgba(245,233,192,.6);padding:2px 0;">
                            <span>Metode Bayar</span>
                            <span style="background:rgba(201,162,39,.15);color:var(--emas);border-radius:20px;padding:2px 9px;font-size:10.5px;font-weight:700;">
                                {{ $mIcon }} {{ $mLabel }}
                            </span>
                        </div>

                        <div style="border-top:1.5px dashed rgba(201,162,39,.2);margin:.6rem 0;"></div>

                        {{-- Item --}}
                        @foreach($order->detailOrders as $item)
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:4px 0;border-bottom:1px solid rgba(201,162,39,.08);">
                            <span style="color:rgba(245,233,192,.8);">{{ $item->menu->name_menu ?? '-' }}</span>
                            <span style="color:rgba(245,233,192,.5);margin:0 8px;">×{{ $item->total }}</span>
                            <span style="color:var(--emas);font-weight:600;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
                        </div>
                        @endforeach

                        <div style="border-top:2px solid rgba(201,162,39,.35);margin:.6rem 0;"></div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;font-weight:800;color:rgba(245,233,192,.9);">TOTAL</span>
                            <span style="font-size:17px;font-weight:800;color:var(--emas);">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="background:rgba(0,0,0,.2);border-top:1.5px dashed rgba(201,162,39,.2);padding:12px 16px;text-align:center;">
                        <div style="font-size:12px;font-weight:700;color:rgba(245,233,192,.85);margin-bottom:3px;">Terima kasih sudah berkunjung! 🙏</div>
                        <div style="font-size:10.5px;color:rgba(245,233,192,.4);line-height:1.7;">Semoga harimu menyenangkan<br>Sampai jumpa kembali di Dapur Nusantara</div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="bi bi-receipt-cutoff"></i>
            <p>Belum ada riwayat pesanan</p>
        </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
function switchTab(tab) {
    const isAktif = tab === 'aktif';
    document.getElementById('panelAktif').style.display   = isAktif ? 'block' : 'none';
    document.getElementById('panelRiwayat').style.display = isAktif ? 'none'  : 'block';
    document.getElementById('tabAktif').classList.toggle('active',   isAktif);
    document.getElementById('tabRiwayat').classList.toggle('active', !isAktif);
}
</script>
@endpush

@endsection