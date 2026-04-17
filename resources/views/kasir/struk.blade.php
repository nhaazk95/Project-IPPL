<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk — {{ $transaksi->kd_transaksi }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #2c1810;
            display: flex; align-items: flex-start; justify-content: center;
            min-height: 100vh; padding: 32px 16px;
        }
        .struk {
            background: #fff;
            width: 340px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }
        .struk-header {
            background: #2c1810;
            padding: 24px 20px 20px;
            text-align: center;
        }
        .struk-icon { font-size: 32px; margin-bottom: 6px; }
        .struk-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 800;
            color: #c9a227; letter-spacing: 2px;
        }
        .struk-tagline { font-size: 11px; color: rgba(201,162,39,.6); margin-top: 2px; }
        .struk-body { padding: 20px 20px; }
        .divider { border: none; border-top: 1.5px dashed #e0d5c5; margin: 14px 0; }
        .info-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 12.5px; color: #7a6552; padding: 3px 0;
        }
        .info-row strong { color: #2c1810; font-weight: 700; }
        .item {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 7px 0; border-bottom: 1px solid #f5ece0;
        }
        .item-name { font-size: 13px; font-weight: 600; color: #2c1810; flex: 1; }
        .item-qty { font-size: 12px; color: #7a6552; margin: 0 10px; white-space: nowrap; }
        .item-price { font-size: 13px; font-weight: 700; color: #2c1810; white-space: nowrap; }
        .total-row {
            display: flex; justify-content: space-between;
            font-size: 13px; color: #7a6552; padding: 3px 0;
        }
        .total-final {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 0 0; margin-top: 10px;
            border-top: 2.5px solid #2c1810;
        }
        .total-final .label { font-size: 14px; font-weight: 800; color: #2c1810; }
        .total-final .amount { font-size: 20px; font-weight: 800; color: #c9a227; }
        .bayar-box {
            background: #faf5ee; border-radius: 10px;
            padding: 12px 14px; margin-top: 14px;
            border: 1.5px solid #e0d5c5;
        }
        .bayar-row {
            display: flex; justify-content: space-between;
            font-size: 13px; color: #5a3e2b; padding: 2px 0; font-weight: 500;
        }
        .bayar-row.kembalian { color: #1a7a4a; font-weight: 800; }
        .struk-footer {
            background: #faf5ee;
            border-top: 1.5px dashed #e0d5c5;
            padding: 16px 20px;
            text-align: center;
        }
        .struk-footer .thanks { font-size: 14px; font-weight: 700; color: #2c1810; margin-bottom: 4px; }
        .struk-footer p { font-size: 11.5px; color: #7a6552; line-height: 1.8; }
        .struk-footer .kode { font-family: monospace; font-size: 10px; color: #b0a090; margin-top: 10px; }
        .btn-actions {
            display: flex; gap: 10px; padding: 16px 20px;
            background: #fff; border-top: 1px solid #f0e8d8;
        }
        .btn-print {
            flex: 1; padding: 11px;
            background: #2c1810; color: #c9a227;
            border: none; border-radius: 10px;
            font-size: 13.5px; font-weight: 700;
            cursor: pointer; font-family: inherit;
        }
        .btn-back {
            flex: 1; padding: 11px;
            background: #faf5ee; color: #5a3e2b;
            border: 1.5px solid #e0d5c5; border-radius: 10px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            text-decoration: none; display: flex;
            align-items: center; justify-content: center; gap: 6px;
        }
        @media print {
            body { background: white; padding: 0; }
            .struk { box-shadow: none; border-radius: 0; width: 100%; }
            .btn-actions { display: none; }
        }
    </style>
</head>
<body>
<div class="struk">

    <div class="struk-header">
        <div class="struk-icon">☕</div>
        <div class="struk-name">DAPUR NUSANTARA</div>
        <div class="struk-tagline">Cita Rasa Terbaik untuk Kamu</div>
    </div>

    <div class="struk-body">
        <div class="info-row"><span>No. Transaksi</span><strong>{{ $transaksi->kd_transaksi }}</strong></div>
        <div class="info-row"><span>Tanggal</span><strong>{{ $transaksi->tanggal?->format('d/m/Y') }}</strong></div>
        <div class="info-row"><span>Waktu</span><strong>{{ $transaksi->waktu?->format('H:i') }}</strong></div>
        <div class="info-row"><span>Kasir</span><strong>{{ $transaksi->kasir->name ?? '-' }}</strong></div>
        <div class="info-row"><span>No. Meja</span><strong>Meja {{ $transaksi->order->no_meja ?? '-' }}</strong></div>
        @if($transaksi->order->nama_user)
        <div class="info-row"><span>Pelanggan</span><strong>{{ $transaksi->order->nama_user }}</strong></div>
        @endif

        <hr class="divider">

        @foreach($transaksi->order->detailOrders ?? [] as $detail)
        <div class="item">
            <div class="item-name">{{ $detail->menu->name_menu ?? '-' }}</div>
            <div class="item-qty">x{{ $detail->total }}</div>
            <div class="item-price">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</div>
        </div>
        @endforeach

        <div class="total-row" style="margin-top:10px;">
            <span>Subtotal</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Pajak</span><span>Rp 0</span>
        </div>
        <div class="total-final">
            <span class="label">TOTAL</span>
            <span class="amount">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>

        @if(isset($jumlahBayar) && $jumlahBayar > 0)
        <div class="bayar-box">
            <div class="bayar-row"><span>Dibayar</span><span>Rp {{ number_format($jumlahBayar, 0, ',', '.') }}</span></div>
            <div class="bayar-row kembalian">
                <span>Kembalian</span>
                <span>Rp {{ number_format(max(0, $jumlahBayar - $transaksi->total_harga), 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>

    <div class="struk-footer">
        <div class="thanks">Terima kasih sudah berkunjung! 🙏</div>
        <p>Semoga harimu menyenangkan<br>Sampai jumpa kembali di Dapur Nusantara</p>
        <div class="kode">{{ $transaksi->kd_transaksi }}</div>
    </div>

    <div class="btn-actions">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
        @auth
            @if(auth()->user()->isAdmin())
                <a class="btn-back" href="{{ route('admin.transaksi.index') }}">← Kembali</a>
            @else
                <a class="btn-back" href="{{ route('kasir.order') }}">← Kembali</a>
            @endif
        @endauth
    </div>
</div>
</body>
</html>