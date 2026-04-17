@extends('layouts.app')
@section('title', 'Proses Transaksi — Dapur Nusantara')
@section('page-title', 'Proses Pembayaran')

@section('breadcrumb')
    <a href="{{ route('kasir.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('kasir.order') }}">Transaksi</a>
    <span class="sep">/</span>
    <span class="current">{{ $order->kd_order }}</span>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    {{-- ===== KIRI: Detail Order ===== --}}
    <div>
        {{-- Info Order --}}
        <div class="card mb-20">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-receipt" style="margin-right:8px;"></i>Detail Order</span>
                <span class="badge badge-gold">{{ $order->kd_order }}</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div>
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);margin-bottom:4px;">No. Meja</div>
                        <div style="font-size:16px;font-weight:800;color:var(--brown);">Meja {{ $order->no_meja }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);margin-bottom:4px;">Pelanggan</div>
                        <div style="font-size:15px;font-weight:700;color:var(--brown);">{{ $order->nama_user ?? 'Tamu' }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);margin-bottom:4px;">Waktu Order</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text-mid);">{{ \Carbon\Carbon::parse($order->waktu)->format('H:i, d/m/Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);margin-bottom:4px;">Status</div>
                        <span class="badge badge-gold">{{ ucfirst($order->status_order) }}</span>
                    </div>
                </div>

                @if($order->keterangan)
                <div style="background:var(--cream);border-radius:10px;padding:10px 14px;font-size:13px;color:var(--text-mid);">
                    <i class="fa-solid fa-note-sticky" style="color:var(--gold-dark);margin-right:6px;"></i>
                    {{ $order->keterangan }}
                </div>
                @endif
            </div>
        </div>

        {{-- Tabel Item --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list-check" style="margin-right:8px;"></i>Item Pesanan</span>
                <span style="font-size:11px;color:rgba(245,233,192,.5);">{{ $order->detailOrders->count() }} item</span>
            </div>
            <div class="card-body" style="padding:0;">
                <table>
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Harga Satuan</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->detailOrders as $item)
                        <tr>
                            <td>
                                <div style="font-weight:700;color:var(--brown);">{{ $item->menu->name_menu ?? '-' }}</div>
                                @if($item->menu->kategori)
                                <div style="font-size:11px;color:var(--text-light);">{{ $item->menu->kategori->name_kategori }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;font-weight:700;">{{ $item->total }}</td>
                            <td style="text-align:right;color:var(--text-mid);">Rp {{ number_format($item->menu->harga ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align:right;font-weight:700;color:var(--gold-dark);">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--cream);">
                            <td colspan="3" style="text-align:right;font-weight:800;font-size:14px;color:var(--brown);padding:12px 14px;">TOTAL</td>
                            <td style="text-align:right;font-weight:800;font-size:16px;color:var(--gold-dark);padding:12px 14px;">
                                Rp {{ number_format($order->detailOrders->sum('sub_total'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== KANAN: Form Pembayaran ===== --}}
    <div class="card" style="position:sticky;top:80px;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-cash-register" style="margin-right:8px;"></i>Transaksi Pembayaran</span>
        </div>
        <div class="card-body">
            @php $total = $order->detailOrders->sum('sub_total'); @endphp

            <form action="{{ route('kasir.proses-bayar', $order->kd_order) }}" method="POST" id="formBayar">
                @csrf

                <div class="form-group">
                    <label class="form-label">Total Tagihan</label>
                    <div style="background:var(--cream);border:2px solid var(--gold);border-radius:10px;padding:12px 14px;
                        font-size:20px;font-weight:800;color:var(--gold-dark);text-align:center;">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="metode" value="cash" checked style="display:none;" id="radioCash">
                            <div class="metode-btn" id="btnCash" onclick="selectMetode('cash')"
                                style="border:2px solid var(--gold);background:rgba(201,162,39,.12);border-radius:10px;padding:12px;text-align:center;">
                                <i class="fa-solid fa-money-bills" style="font-size:22px;color:var(--gold-dark);display:block;margin-bottom:5px;"></i>
                                <span style="font-size:12px;font-weight:700;color:var(--brown);">Cash</span>
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="metode" value="qris" style="display:none;" id="radioQris">
                            <div class="metode-btn" id="btnQris" onclick="selectMetode('qris')"
                                style="border:2px solid var(--cream-dark);background:var(--cream);border-radius:10px;padding:12px;text-align:center;">
                                <i class="fa-solid fa-qrcode" style="font-size:22px;color:var(--text-light);display:block;margin-bottom:5px;"></i>
                                <span style="font-size:12px;font-weight:700;color:var(--text-mid);">QRIS</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="cashSection">
                    <div class="form-group">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" id="fBayar" class="form-control"
                            placeholder="Masukkan jumlah uang" min="{{ $total }}"
                            oninput="hitungKembalian({{ $total }})" style="font-size:15px;padding:11px 13px;">
                    </div>
                    <div class="form-group" id="kembalianGroup" style="display:none;">
                        <label class="form-label">Kembalian</label>
                        <div id="fKembalian" style="background:rgba(26,122,74,.08);border:2px solid var(--success);
                            border-radius:10px;padding:11px 14px;font-size:16px;font-weight:800;
                            color:var(--success);text-align:center;"></div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:4px;">
                    <a href="{{ route('kasir.order') }}" class="btn-secondary"
                        style="flex:1;justify-content:center;padding:11px;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn-gold" id="btnSimpan"
                        style="flex:1;justify-content:center;padding:11px;font-size:14px;">
                        <i class="fa-solid fa-check"></i> Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.metode-btn { transition: all .2s; }
.metode-btn:hover { transform: translateY(-2px); }
@media (max-width:900px) {
    .page-content > div { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@push('scripts')
<script>
let metodeAktif = 'cash';

function selectMetode(m) {
    metodeAktif = m;
    document.getElementById('radioCash').checked = m === 'cash';
    document.getElementById('radioQris').checked = m === 'qris';

    const gold  = 'border:2px solid var(--gold);background:rgba(201,162,39,.12);';
    const plain = 'border:2px solid var(--cream-dark);background:var(--cream);';

    document.getElementById('btnCash').style.cssText  += m === 'cash'  ? gold : plain;
    document.getElementById('btnQris').style.cssText  += m === 'qris'  ? gold : plain;

    document.getElementById('btnCash').querySelector('i').style.color  = m === 'cash'  ? 'var(--gold-dark)' : 'var(--text-light)';
    document.getElementById('btnQris').querySelector('i').style.color  = m === 'qris'  ? 'var(--gold-dark)' : 'var(--text-light)';

    document.getElementById('cashSection').style.display = m === 'cash' ? '' : 'none';
}

function hitungKembalian(total) {
    const bayar = parseInt(document.getElementById('fBayar').value) || 0;
    const group = document.getElementById('kembalianGroup');
    const el    = document.getElementById('fKembalian');

    if (bayar >= total) {
        const kembalian = bayar - total;
        el.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        group.style.display = '';
        el.style.color = 'var(--success)';
        el.parentElement.querySelector('.form-label').style.color = 'var(--success)';
    } else if (bayar > 0) {
        const kurang = total - bayar;
        el.textContent = '⚠ Kurang: Rp ' + kurang.toLocaleString('id-ID');
        group.style.display = '';
        el.style.color = 'var(--danger)';
    } else {
        group.style.display = 'none';
    }
}
</script>
@endpush