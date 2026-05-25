@extends('layouts.app')
@section('title', 'Transaksi — Admin')
@section('page-title', 'Transaksi')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Transaksi</span>
@endsection

@section('content')

<div class="trx-layout">

    {{-- ===== KIRI: Pilih Orderan ===== --}}
    <div class="trx-left">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-clipboard-list" style="margin-right:8px;"></i>Pilih Orderan</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:rgba(245,233,192,.5);">{{ $orders->total() }} order</span>
                </div>
            </div>

            <div style="padding:12px 16px;border-bottom:1px solid var(--cream-dark);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-light);">
                    Show
                    <select onchange="window.location.href='?per_page='+this.value" class="form-control" style="width:60px;padding:4px 8px;font-size:12px;">
                        @foreach([10,25,50] as $n)
                            <option value="{{ $n }}" {{ request('per_page',10)==$n?'selected':'' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    entries
                </div>
                <form method="GET" style="display:flex;gap:6px;">
                    <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
                    <label style="font-size:13px;color:var(--text-light);align-self:center;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" style="width:150px;padding:5px 10px;font-size:12px;"
                        placeholder="Kode / nama...">
                </form>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table id="orderTable">
                        <thead>
                            <tr>
                                <th>Kode Order</th>
                                <th style="text-align:center;">No Meja</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th style="text-align:center;">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr class="order-row" style="cursor:pointer;"
                                onclick="pilihOrder('{{ $order->kd_order }}','{{ $order->no_meja }}','{{ $order->nama_user ?? 'Tamu' }}','{{ $order->detailOrders->sum('sub_total') }}')">
                                <td style="font-weight:700;font-family:monospace;font-size:12.5px;color:var(--brown);">{{ $order->kd_order }}</td>
                                <td style="text-align:center;"><span class="badge badge-brown">{{ $order->no_meja }}</span></td>
                                <td>{{ $order->nama_user ?? 'Tamu' }}</td>
                                <td style="font-size:12px;color:var(--text-light);">{{ \Carbon\Carbon::parse($order->tanggal)->format('Y-m-d') }}</td>
                                <td style="text-align:center;">
                                    <button class="btn-gold btn-sm"
                                        onclick="event.stopPropagation();pilihOrder('{{ $order->kd_order }}','{{ $order->no_meja }}','{{ $order->nama_user ?? 'Tamu' }}','{{ $order->detailOrders->sum('sub_total') }}')">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5">
                                <div class="empty-state" style="padding:28px;"><div class="empty-icon">📋</div><p>Tidak ada order aktif</p></div>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="padding:10px 16px;border-top:1px solid var(--cream-dark);font-size:12px;color:var(--text-light);display:flex;justify-content:space-between;align-items:center;">
                <span>Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries</span>
                <div style="display:flex;gap:6px;">
                    @if($orders->onFirstPage())
                        <span class="btn-secondary btn-sm" style="opacity:.4;cursor:default;">Previous</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="btn-secondary btn-sm">Previous</a>
                    @endif
                    <span class="btn-gold btn-sm">{{ $orders->currentPage() }}</span>
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="btn-secondary btn-sm">Next</a>
                    @else
                        <span class="btn-secondary btn-sm" style="opacity:.4;cursor:default;">Next</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== KANAN: Transaksi Pembayaran ===== --}}
    <div class="trx-right">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-cash-register" style="margin-right:8px;"></i>Transaksi Pembayaran</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Kode Order</label>
                    <input type="text" id="fKdOrder" class="form-control" readonly
                        placeholder="Pilih order dari tabel" style="background:var(--cream);">
                </div>

                <div class="form-group">
                    <label class="form-label">Total Harga</label>
                    <div id="fTotal" style="background:var(--cream);border:2px solid var(--gold);border-radius:10px;
                        padding:11px 14px;font-size:18px;font-weight:800;color:var(--gold-dark);text-align:center;">
                        Rp 0
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Bayar</label>
                    <input type="number" id="fBayar" class="form-control"
                        placeholder="Masukkan jumlah uang" min="0"
                        oninput="hitungKembalian()" style="font-size:15px;">
                </div>

                <div class="form-group">
                    <label class="form-label">Kembalian</label>
                    <div id="fKembalian" style="background:var(--cream);border:1.5px solid var(--cream-dark);
                        border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;
                        color:var(--text-light);text-align:center;transition:all .2s;">
                        —
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px;">
                    <button type="button" class="btn-secondary" style="flex:1;justify-content:center;padding:11px;"
                        onclick="resetForm()">
                        <i class="fa-solid fa-rotate-left"></i> Kembali
                    </button>
                    <button type="button" id="btnBayar" class="btn-gold" disabled
                        style="flex:1;justify-content:center;padding:11px;opacity:.5;cursor:not-allowed;"
                        onclick="prosesBayar()">
                        <i class="fa-solid fa-money-bill-wave"></i> Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL STRUK ===== --}}
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

@push('styles')
<style>
.trx-layout { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
.order-row:hover { background:var(--gold-pale) !important; }
.order-row.selected { background:rgba(201,162,39,.1) !important; }
@media(max-width:900px) { .trx-layout { grid-template-columns:1fr; } }
</style>
@endpush

@push('scripts')
<script>
let selectedOrderKd = '';
let selectedTotal   = 0;

function pilihOrder(kd, meja, nama, total) {
    selectedOrderKd = kd;
    selectedTotal   = parseInt(total) || 0;

    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('selected'));
    document.querySelectorAll('.order-row').forEach(r => {
        if (r.textContent.includes(kd)) r.classList.add('selected');
    });

    document.getElementById('fKdOrder').value = kd + ' — Meja ' + meja + ' (' + nama + ')';
    document.getElementById('fTotal').textContent = 'Rp ' + selectedTotal.toLocaleString('id-ID');

    const btn = document.getElementById('btnBayar');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';

    document.getElementById('fBayar').value = '';
    document.getElementById('fKembalian').textContent = '—';
    document.getElementById('fKembalian').style.cssText = 'background:var(--cream);border:1.5px solid var(--cream-dark);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;color:var(--text-light);text-align:center;';
}

function hitungKembalian() {
    const bayar = parseInt(document.getElementById('fBayar').value) || 0;
    const el = document.getElementById('fKembalian');
    if (!selectedOrderKd) return;

    if (bayar >= selectedTotal) {
        el.textContent = 'Rp ' + (bayar - selectedTotal).toLocaleString('id-ID');
        el.style.cssText = 'background:rgba(26,122,74,.08);border:2px solid var(--success);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:800;color:var(--success);text-align:center;';
    } else if (bayar > 0) {
        el.textContent = '⚠ Kurang: Rp ' + (selectedTotal - bayar).toLocaleString('id-ID');
        el.style.cssText = 'background:#fde8e8;border:2px solid var(--danger);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;color:var(--danger);text-align:center;';
    } else {
        el.textContent = '—';
        el.style.cssText = 'background:var(--cream);border:1.5px solid var(--cream-dark);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;color:var(--text-light);text-align:center;';
    }
}

function resetForm() {
    selectedOrderKd = '';
    selectedTotal   = 0;
    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('selected'));
    document.getElementById('fKdOrder').value = '';
    document.getElementById('fBayar').value = '';
    document.getElementById('fTotal').textContent = 'Rp 0';
    document.getElementById('fKembalian').textContent = '—';
    const btn = document.getElementById('btnBayar');
    btn.disabled = true; btn.style.opacity = '.5'; btn.style.cursor = 'not-allowed';
}

function fmt(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function prosesBayar() {
    if (!selectedOrderKd) return;
    const jumlahBayar = parseInt(document.getElementById('fBayar').value) || 0;

    const btn = document.getElementById('btnBayar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

    fetch('/admin/transaksi/' + selectedOrderKd + '/bayar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ jumlah_bayar: jumlahBayar }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i> Bayar';
            return;
        }
        tampilkanStruk(data);
        resetForm();
    })
    .catch(() => {
        alert('Terjadi kesalahan. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i> Bayar';
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

    let bayarBox = '';
    if (d.jumlah_bayar > 0) {
        bayarBox = `
        <div style="background:#faf5ee;border-radius:10px;padding:12px 14px;margin-top:14px;border:1.5px solid #e0d5c5;">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#5a3e2b;padding:2px 0;font-weight:500;">
                <span>Dibayar</span><span>${fmt(d.jumlah_bayar)}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:2px 0;font-weight:800;color:#1a7a4a;">
                <span>Kembalian</span><span>${fmt(d.kembalian)}</span>
            </div>
        </div>`;
    }

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

        ${bayarBox}

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