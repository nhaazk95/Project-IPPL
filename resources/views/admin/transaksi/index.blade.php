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

            {{-- Search & Show entries --}}
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
                            <tr class="order-row" style="cursor:pointer;" onclick="pilihOrder('{{ $order->kd_order }}','{{ $order->no_meja }}','{{ $order->nama_user ?? 'Tamu' }}','{{ $order->detailOrders->sum('sub_total') }}')">
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
                <form method="POST" id="formBayar" action="">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Kode Transaksi</label>
                        <input type="text" id="fKdTrx" class="form-control" readonly
                            value="TRX-{{ now()->format('YmdHis') }}"
                            style="background:var(--cream);font-family:monospace;font-weight:700;color:var(--brown);">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode Order</label>
                        <input type="text" id="fKdOrder" name="kd_order_display" class="form-control" readonly
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
                        <input type="number" id="fBayar" name="jumlah_bayar" class="form-control"
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
                        <button type="submit" id="btnSimpan" class="btn-gold" disabled
                            style="flex:1;justify-content:center;padding:11px;opacity:.5;cursor:not-allowed;">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
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
.trx-layout { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
.order-row:hover { background:var(--gold-pale) !important; }
.order-row.selected { background:rgba(201,162,39,.1) !important; }
@media(max-width:900px) { .trx-layout { grid-template-columns:1fr; } }
</style>
@endpush

@push('scripts')
<script>
let selectedOrderKd = '';
let selectedTotal = 0;

function pilihOrder(kd, meja, nama, total) {
    selectedOrderKd = kd;
    selectedTotal   = parseInt(total) || 0;

    // Highlight baris
    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('selected'));
    event?.currentTarget?.closest('tr')?.classList.add('selected');

    document.getElementById('fKdOrder').value = kd + ' — Meja ' + meja + ' (' + nama + ')';
    document.getElementById('fTotal').textContent = 'Rp ' + selectedTotal.toLocaleString('id-ID');

    // Set form action
    document.getElementById('formBayar').action = '/admin/transaksi/' + kd + '/bayar';

    // Enable tombol simpan
    const btn = document.getElementById('btnSimpan');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';

    // Reset bayar & kembalian
    document.getElementById('fBayar').value = '';
    document.getElementById('fKembalian').textContent = '—';
    document.getElementById('fKembalian').style.cssText = 'background:var(--cream);border:1.5px solid var(--cream-dark);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:700;color:var(--text-light);text-align:center;';
}

function hitungKembalian() {
    const bayar = parseInt(document.getElementById('fBayar').value) || 0;
    const el = document.getElementById('fKembalian');
    if (!selectedOrderKd) return;

    if (bayar >= selectedTotal) {
        const kembalian = bayar - selectedTotal;
        el.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        el.style.cssText = 'background:rgba(26,122,74,.08);border:2px solid var(--success);border-radius:10px;padding:11px 14px;font-size:16px;font-weight:800;color:var(--success);text-align:center;';
    } else if (bayar > 0) {
        const kurang = selectedTotal - bayar;
        el.textContent = '⚠ Kurang: Rp ' + kurang.toLocaleString('id-ID');
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
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true; btn.style.opacity = '.5'; btn.style.cursor = 'not-allowed';
}
</script>
@endpush