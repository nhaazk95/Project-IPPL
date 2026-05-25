@extends('layouts.app')
@section('title', 'Dashboard Kasir — Dapur Nusantara')
@section('page-title', 'Dashboard Kasir')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Home</a>
    <span class="sep">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid-4 mb-20">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value" style="color:#2563eb;">{{ $orderPending }}</div>
            <div class="stat-label">Order Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-fire"></i></div>
        <div>
            <div class="stat-value">{{ $orderDiproses }}</div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $transaksiHari }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fa-solid fa-coins"></i></div>
        <div>
            <div class="stat-value" style="font-size:16px;">Rp {{ number_format($pendapatanHari, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
</div>

{{-- Header Order Section --}}
<div class="flex-between mb-20">
    <div>
        <p class="ph-title"><i class="fa-solid fa-bell" style="color:var(--gold);margin-right:8px;"></i>Order Aktif</p>
        <p class="ph-sub">{{ $orderPending + $orderDiproses }} order menunggu tindakan</p>
    </div>
    <a href="{{ route('kasir.order') }}" class="btn-primary">
        <i class="fa-solid fa-clipboard-list"></i> Kelola Semua Order
    </a>
</div>

{{-- Order Cards --}}
@forelse($orderTerbaru as $order)
@php $isOnline = !empty($order->kd_pelanggan) && $order->kd_pelanggan !== 'GUEST'; @endphp
<div class="order-card">
    {{-- Header --}}
    <div class="order-card-header">
        <span class="meja-badge"><i class="fa-solid fa-chair" style="font-size:10px;margin-right:4px;"></i>Meja {{ $order->no_meja }}</span>
        <span class="order-code">{{ $order->kd_order }} · {{ \Carbon\Carbon::parse($order->waktu)->format('H:i') }}</span>
        <div style="display:flex;align-items:center;gap:6px;">
            @if($isOnline)
                <span style="background:rgba(37,99,235,.1);border:1px solid #2563eb;color:#2563eb;
                    font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">
                    <i class="fa-solid fa-qrcode" style="font-size:9px;"></i> QRIS
                </span>
            @else
                <span style="background:rgba(201,162,39,.1);border:1px solid var(--gold);color:var(--gold-dark);
                    font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">
                    <i class="fa-solid fa-money-bill" style="font-size:9px;"></i> Kasir
                </span>
            @endif
            <span class="status-badge" style="
                background:{{ $order->status_order === 'pending' ? 'rgba(37,99,235,.1)' : 'rgba(201,162,39,.12)' }};
                border-color:{{ $order->status_order === 'pending' ? '#2563eb' : 'var(--gold)' }};
                color:{{ $order->status_order === 'pending' ? '#2563eb' : 'var(--gold-dark)' }};">
                {{ $order->status_order === 'pending' ? 'Menunggu' : 'Diproses' }}
            </span>
        </div>
    </div>

    {{-- Items --}}
    <div class="order-card-body">
        @foreach($order->detailOrders as $item)
            <div class="order-item-row">
                <span>{{ $item->menu->name_menu ?? '-' }}</span>
                <span style="color:var(--text-light);">x{{ $item->total }}</span>
                <span style="font-weight:600;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="order-item-total">
            <span>Total Order</span>
            <span style="font-weight:800;color:var(--gold-dark);">
                Rp {{ number_format($order->detailOrders->sum('sub_total'), 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="order-card-footer">
        @if($order->transaksi)
            <span style="font-size:12px;color:var(--success);font-weight:600;">
                <i class="fa-solid fa-check-circle"></i> Sudah Dibayar
            </span>
        @elseif($isOnline)
            {{-- QRIS: konfirmasi langsung --}}
            <button class="btn-gold btn-sm" onclick="konfirmasiQris('{{ $order->kd_order }}', this)">
                <i class="fa-solid fa-qrcode"></i> Konfirmasi QRIS
            </button>
        @else
            {{-- Kasir: ke halaman transaksi dengan order terpilih --}}
            <a href="{{ route('kasir.transaksi.index') }}?pilih={{ $order->kd_order }}"
                class="btn-gold btn-sm">
                <i class="fa-solid fa-cash-register"></i> Proses Bayar
            </a>
        @endif
    </div>
</div>
@empty
    <div class="card" style="padding:48px 24px;text-align:center;">
        <div style="font-size:3rem;margin-bottom:12px;">✅</div>
        <p style="font-weight:700;color:var(--brown);font-size:16px;margin-bottom:4px;">Semua beres!</p>
        <p style="color:var(--text-light);font-size:13px;">Tidak ada order aktif saat ini</p>
    </div>
@endforelse

{{-- Modal Struk (QRIS) --}}
<div id="modalStruk" style="display:none;position:fixed;inset:0;z-index:9999;
    background:rgba(0,0,0,.6);backdrop-filter:blur(3px);
    align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;width:100%;max-width:380px;border-radius:16px;
        overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.4);
        position:relative;max-height:90vh;overflow-y:auto;">

        <button onclick="tutupStruk()" style="position:absolute;top:12px;right:12px;
            background:rgba(0,0,0,.08);border:none;border-radius:50%;
            width:32px;height:32px;font-size:16px;cursor:pointer;
            display:flex;align-items:center;justify-content:center;z-index:10;">✕</button>

        <div id="strukContent" style="padding:24px 20px 0;"></div>

        <div style="padding:16px 20px 20px;text-align:center;">
            <button onclick="cetakStruk()" style="padding:12px 32px;
                background:#2c1810;color:#c9a227;border:none;border-radius:10px;
                font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
                🖨️ Cetak Struk
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function fmt(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function konfirmasiQris(kdOrder, btn) {
    if (!confirm('Konfirmasi pembayaran QRIS untuk order ' + kdOrder + '?')) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

    fetch('/kasir/order/' + kdOrder + '/bayar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ jumlah_bayar: 0 }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-qrcode"></i> Konfirmasi QRIS';
            return;
        }
        tampilkanStruk(data);
    })
    .catch(() => {
        alert('Terjadi kesalahan. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-qrcode"></i> Konfirmasi QRIS';
    });
}

function tampilkanStruk(d) {
    let items = '';
    d.items.forEach(i => {
        items += `
        <div style="display:flex;justify-content:space-between;align-items:flex-start;
            padding:7px 0;border-bottom:1px solid #f5ece0;">
            <div style="font-size:13px;font-weight:600;color:#2c1810;flex:1;">${i.nama}</div>
            <div style="font-size:12px;color:#7a6552;margin:0 10px;">x${i.qty}</div>
            <div style="font-size:13px;font-weight:700;color:#2c1810;">${fmt(i.sub_total)}</div>
        </div>`;
    });

    let pelanggan = d.nama_user
        ? `<div style="display:flex;justify-content:space-between;font-size:12.5px;color:#7a6552;padding:3px 0;">
               <span>Pelanggan</span><strong style="color:#2c1810;">${d.nama_user}</strong>
           </div>` : '';

    document.getElementById('strukContent').innerHTML = `
        <div style="font-size:12.5px;color:#7a6552;">
            <div style="display:flex;justify-content:space-between;padding:3px 0;">
                <span>No. Transaksi</span><strong style="color:#2c1810;font-family:monospace;">${d.kd_transaksi}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:3px 0;">
                <span>Tanggal</span><strong style="color:#2c1810;">${d.tanggal}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:3px 0;">
                <span>Waktu</span><strong style="color:#2c1810;">${d.waktu}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:3px 0;">
                <span>Kasir</span><strong style="color:#2c1810;">${d.kasir}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:3px 0;">
                <span>No. Meja</span><strong style="color:#2c1810;">Meja ${d.no_meja}</strong>
            </div>
            ${pelanggan}
        </div>

        <hr style="border:none;border-top:1.5px dashed #e0d5c5;margin:14px 0;">

        ${items}

        <div style="display:flex;justify-content:space-between;font-size:13px;color:#7a6552;padding:3px 0;margin-top:10px;">
            <span>Subtotal</span><span>${fmt(d.total_harga)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#7a6552;padding:3px 0;">
            <span>Pajak</span><span>Rp 0</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;
            padding:12px 0 0;margin-top:10px;border-top:2.5px solid #2c1810;">
            <span style="font-size:14px;font-weight:800;color:#2c1810;">TOTAL</span>
            <span style="font-size:20px;font-weight:800;color:#c9a227;">${fmt(d.total_harga)}</span>
        </div>

        <div style="background:#faf5ee;border-radius:10px;padding:12px 14px;margin-top:14px;border:1.5px solid #e0d5c5;">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#5a3e2b;padding:2px 0;font-weight:700;">
                <span><i class="fa-solid fa-qrcode" style="margin-right:4px;"></i>Metode</span>
                <span>QRIS</span>
            </div>
        </div>

        <div style="background:#faf5ee;border-top:1.5px dashed #e0d5c5;
            margin:16px -20px 0;padding:16px 20px;text-align:center;">
            <div style="font-size:14px;font-weight:700;color:#2c1810;margin-bottom:4px;">
                Terima kasih sudah berkunjung! 🙏
            </div>
            <p style="font-size:11.5px;color:#7a6552;line-height:1.8;">
                Semoga harimu menyenangkan<br>Sampai jumpa kembali di Dapur Nusantara
            </p>
            <div style="font-family:monospace;font-size:10px;color:#b0a090;margin-top:10px;">
                ${d.kd_transaksi}
            </div>
        </div>
    `;

    const modal = document.getElementById('modalStruk');
    modal.style.display = 'flex';
}

function tutupStruk() {
    document.getElementById('modalStruk').style.display = 'none';
    window.location.reload();
}

function cetakStruk() {
    const content = document.getElementById('strukContent').innerHTML;
    const printWin = window.open('', '_blank', 'width=420,height=700');
    printWin.document.write(`
        <html><head><title>Struk</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>* { margin:0;padding:0;box-sizing:border-box; } body { font-family:'Plus Jakarta Sans',sans-serif;padding:20px; }</style>
        </head><body>${content}</body></html>
    `);
    printWin.document.close();
    printWin.onload = () => { printWin.print(); };
}
</script>
@endpush