@extends('layouts.app')
@section('title', 'Transaksi — Admin')
@section('page-title', 'Transaksi')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span class="current">Transaksi</span>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success mb-16">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="trx-layout">

    {{-- ===== KIRI: Pilih Orderan ===== --}}
    <div class="trx-left">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-clipboard-list" style="margin-right:8px;"></i>Pilih Orderan</span>
                <span style="font-size:11px;color:rgba(245,233,192,.5);">{{ $orders->total() }} order aktif</span>
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
                <form method="GET" style="display:flex;gap:6px;align-items:center;">
                    <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
                    <label style="font-size:13px;color:var(--text-light);">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" style="width:150px;padding:5px 10px;font-size:12px;" placeholder="Kode / nama...">
                </form>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Order</th>
                                <th style="text-align:center;">No Meja</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th style="text-align:center;">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            @php
                                $total = $order->detailOrders->sum('sub_total');
                                $nama  = $order->pelanggan->name_pelanggan ?? $order->nama_user ?? 'Tamu';
                                $ket   = strtolower($order->keterangan ?? '');
                                $isQris = str_contains($ket, 'qris');
                                $metodeLabel = $isQris ? 'QRIS' : 'Kasir';
                            @endphp
                            <tr class="order-row" style="cursor:pointer;"
                                onclick="pilihOrder('{{ $order->kd_order }}','{{ $order->no_meja }}','{{ addslashes($nama) }}',{{ $total }},'{{ $metodeLabel }}')">
                                <td style="font-weight:700;font-family:monospace;font-size:12.5px;color:var(--brown);">
                                    {{ $order->kd_order }}
                                    @if($order->pelanggan)
                                        <span class="badge badge-info" style="font-size:9px;margin-left:4px;">Online</span>
                                    @endif
                                </td>
                                <td style="text-align:center;"><span class="badge badge-brown">{{ $order->no_meja }}</span></td>
                                <td>
                                    <div style="font-weight:600;font-size:13px;">{{ $nama }}</div>
                                    <div style="font-size:10.5px;color:var(--text-light);">
                                        <i class="fa-solid fa-{{ $isQris ? 'qrcode' : 'money-bill' }}" style="font-size:9px;"></i>
                                        Bayar via {{ $metodeLabel }}
                                    </div>
                                </td>
                                <td style="font-weight:700;color:var(--gold-dark);">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td style="text-align:center;">
                                    <button class="btn-gold btn-sm"
                                        onclick="event.stopPropagation();pilihOrder('{{ $order->kd_order }}','{{ $order->no_meja }}','{{ addslashes($nama) }}',{{ $total }},'{{ $metodeLabel }}')">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5">
                                <div class="empty-state" style="padding:32px;">
                                    <div class="empty-icon">✅</div><p>Tidak ada order aktif</p>
                                </div>
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

    {{-- ===== KANAN: Form Pembayaran ===== --}}
    <div class="trx-right">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-cash-register" style="margin-right:8px;"></i>Transaksi Pembayaran</span>
            </div>
            <div class="card-body">
                <form method="POST" id="formBayar" action="">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Kode Order</label>
                        <input type="text" id="fKdOrder" class="form-control" readonly
                            placeholder="Pilih order dari tabel" style="background:var(--cream);">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pelanggan</label>
                        <input type="text" id="fNama" class="form-control" readonly
                            placeholder="—" style="background:var(--cream);">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Harga</label>
                        <div id="fTotal" style="background:var(--cream);border:2px solid var(--gold);
                            border-radius:10px;padding:11px 14px;font-size:20px;font-weight:800;
                            color:var(--gold-dark);text-align:center;">Rp 0</div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="form-group" id="metodeGroup" style="display:none;">
                        <label class="form-label">Metode Pembayaran</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;" id="metodeOptions">
                            {{-- Diisi JS --}}
                        </div>
                        <input type="hidden" name="metode" id="inputMetode" value="cash">
                    </div>

                    {{-- Input bayar cash --}}
                    <div class="form-group" id="cashGroup" style="display:none;">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" id="fBayar" name="jumlah_bayar" class="form-control"
                            placeholder="Masukkan jumlah uang" min="0"
                            oninput="hitungKembalian()" style="font-size:15px;">
                    </div>

                    <div class="form-group" id="kembalianGroup" style="display:none;margin-bottom:0;">
                        <label class="form-label">Kembalian</label>
                        <div id="fKembalian" style="background:var(--cream);border:1.5px solid var(--cream-dark);
                            border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;
                            color:var(--text-light);text-align:center;">—</div>
                    </div>

                    {{-- Info non-cash --}}
                    <div id="nonCashInfo" style="display:none;margin-bottom:14px;">
                        <div style="background:var(--cream);border-radius:10px;padding:12px 14px;font-size:13px;color:var(--text-mid);text-align:center;">
                            <i class="fa-solid fa-circle-check" style="color:var(--success);margin-right:6px;"></i>
                            Klik <strong>Konfirmasi</strong> untuk menyelesaikan transaksi
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:16px;">
                        <button type="button" class="btn-secondary"
                            style="flex:1;justify-content:center;padding:11px;" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                        <button type="submit" id="btnSimpan" class="btn-gold" disabled
                            style="flex:1;justify-content:center;padding:11px;opacity:.5;cursor:not-allowed;">
                            <i class="fa-solid fa-check"></i> Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.mb-16 { margin-bottom:16px; }
.trx-layout { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
.order-row:hover { background:var(--gold-pale) !important; }
.order-row.selected { background:rgba(201,162,39,.08) !important; outline:2px solid var(--gold); }
@media(max-width:900px) { .trx-layout { grid-template-columns:1fr; } }
</style>
@endpush

@push('scripts')
<script>
let selectedOrderKd = '';
let selectedTotal   = 0;
let selectedMetode  = 'cash'; // default

const GOLD_STYLE  = 'border:2px solid var(--gold);background:rgba(201,162,39,.12);border-radius:10px;padding:10px;text-align:center;cursor:pointer;';
const PLAIN_STYLE = 'border:2px solid var(--cream-dark);background:var(--cream);border-radius:10px;padding:10px;text-align:center;cursor:pointer;';

function pilihOrder(kd, meja, nama, total, metodeHint) {
    selectedOrderKd = kd;
    selectedTotal   = parseInt(total) || 0;

    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('selected'));
    event?.currentTarget?.closest('tr')?.classList.add('selected');

    document.getElementById('fKdOrder').value = kd + ' — Meja ' + meja;
    document.getElementById('fNama').value    = nama;
    document.getElementById('fTotal').textContent = 'Rp ' + selectedTotal.toLocaleString('id-ID');
    document.getElementById('formBayar').action = '/admin/transaksi/' + kd + '/bayar';

    // Tentukan pilihan metode berdasarkan hint dari order
    const isQris = metodeHint === 'QRIS';
    buildMetodeButtons(isQris);

    const btn = document.getElementById('btnSimpan');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';

    document.getElementById('fBayar').value = '';
    document.getElementById('kembalianGroup').style.display = 'none';
    document.getElementById('metodeGroup').style.display = '';
}

function buildMetodeButtons(isQris) {
    const container = document.getElementById('metodeOptions');

    if (isQris) {
        // QRIS — hanya 1 pilihan
        container.style.gridTemplateColumns = '1fr';
        container.innerHTML = `
            <div style="${GOLD_STYLE}" onclick="selectMetode('qris')">
                <i class="fa-solid fa-qrcode" style="font-size:20px;color:var(--gold-dark);display:block;margin-bottom:4px;"></i>
                <span style="font-size:11px;font-weight:700;color:var(--brown);">QRIS</span>
            </div>`;
        selectMetode('qris');
    } else {
        // Kasir — Cash & Debit
        container.style.gridTemplateColumns = '1fr 1fr';
        container.innerHTML = `
            <div id="btnCash" style="${GOLD_STYLE}" onclick="selectMetode('cash')">
                <i class="fa-solid fa-money-bill-wave" style="font-size:20px;color:var(--gold-dark);display:block;margin-bottom:4px;"></i>
                <span style="font-size:11px;font-weight:700;color:var(--brown);">Cash</span>
            </div>
            <div id="btnDebit" style="${PLAIN_STYLE}" onclick="selectMetode('debit')">
                <i class="fa-solid fa-credit-card" style="font-size:20px;color:var(--text-light);display:block;margin-bottom:4px;"></i>
                <span style="font-size:11px;font-weight:700;color:var(--text-mid);">Debit/Kartu</span>
            </div>`;
        selectMetode('cash');
    }
}

function selectMetode(m) {
    selectedMetode = m;
    document.getElementById('inputMetode').value = m;

    const isCash = m === 'cash';
    document.getElementById('cashGroup').style.display    = isCash ? '' : 'none';
    document.getElementById('nonCashInfo').style.display  = isCash ? 'none' : '';
    document.getElementById('kembalianGroup').style.display = 'none';
    document.getElementById('fBayar').required = isCash;

    // Update visual active
    ['cash','debit'].forEach(k => {
        const btn = document.getElementById('btn' + k.charAt(0).toUpperCase() + k.slice(1));
        if (!btn) return;
        const ico = btn.querySelector('i');
        const lbl = btn.querySelector('span');
        const active = k === m;
        btn.style.cssText = (active ? GOLD_STYLE : PLAIN_STYLE);
        if (ico) ico.style.color = active ? 'var(--gold-dark)' : 'var(--text-light)';
        if (lbl) lbl.style.color = active ? 'var(--brown)'     : 'var(--text-mid)';
    });
}

function hitungKembalian() {
    const bayar = parseInt(document.getElementById('fBayar').value) || 0;
    if (!selectedOrderKd || selectedMetode !== 'cash') return;

    const el    = document.getElementById('fKembalian');
    const group = document.getElementById('kembalianGroup');

    if (bayar >= selectedTotal) {
        const kembalian = bayar - selectedTotal;
        el.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        el.style.cssText = 'background:rgba(26,122,74,.08);border:2px solid var(--success);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:800;text-align:center;color:var(--success);';
        group.style.display = '';
    } else if (bayar > 0) {
        const kurang = selectedTotal - bayar;
        el.textContent = '⚠ Kurang: Rp ' + kurang.toLocaleString('id-ID');
        el.style.cssText = 'background:#fde8e8;border:2px solid var(--danger);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;text-align:center;color:var(--danger);';
        group.style.display = '';
    } else {
        group.style.display = 'none';
    }
}

function resetForm() {
    selectedOrderKd = ''; selectedTotal = 0;
    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('selected'));
    document.getElementById('fKdOrder').value = '';
    document.getElementById('fNama').value    = '';
    document.getElementById('fBayar').value   = '';
    document.getElementById('fTotal').textContent = 'Rp 0';
    document.getElementById('metodeGroup').style.display    = 'none';
    document.getElementById('cashGroup').style.display      = 'none';
    document.getElementById('kembalianGroup').style.display = 'none';
    document.getElementById('nonCashInfo').style.display    = 'none';
    document.getElementById('fKembalian').textContent = '—';
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true; btn.style.opacity = '.5'; btn.style.cursor = 'not-allowed';
}
</script>
@endpush