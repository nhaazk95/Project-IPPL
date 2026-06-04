<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DetailOrderTemporary;
use App\Models\Order;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    public function index()
    {
        $sess   = session('pelanggan');
        $orders = Order::with(['detailOrders.menu', 'transaksi'])
            ->where('kd_pelanggan', $sess['kd_pelanggan'])
            ->latest('waktu')
            ->get();

        return view('pelanggan.pesanan', compact('orders'));
    }

    /**
     * Halaman preview pembayaran — tampilkan isi keranjang + pilih metode.
     * Order BELUM dibuat, keranjang masih ada.
     */
    public function preview()
    {
        $kdPelanggan = session('pelanggan.kd_pelanggan');

        $keranjang = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)
            ->with('menu')
            ->get();

        if ($keranjang->isEmpty()) {
            return redirect()->route('pelanggan.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        $totalHarga  = $keranjang->sum('sub_total');
        $keterangan  = session('checkout_keterangan');
        $noMeja      = session('pelanggan.no_meja');

        return view('pelanggan.pembayaran-preview', compact('keranjang', 'totalHarga', 'keterangan', 'noMeja'));
    }

    /**
     * Konfirmasi metode — baru buat Order, masuk ke kasir, kosongkan keranjang.
     */
    public function konfirmasiMetode(Request $request)
    {
        $request->validate([
            'metode' => 'required|in:kasir,qris',
        ]);

        $kdPelanggan = session('pelanggan.kd_pelanggan');
        $noMeja      = session('pelanggan.no_meja');
        $keterangan  = session('checkout_keterangan');

        $keranjang = DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)->with('menu')->get();

        if ($keranjang->isEmpty()) {
            return redirect()->route('pelanggan.keranjang')->with('error', 'Keranjang kosong!');
        }

        // Buat Order — baru masuk ke kasir
        $kdOrder = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        $order = Order::create([
            'kd_order'     => $kdOrder,
            'no_meja'      => $noMeja,
            'kd_pelanggan' => $kdPelanggan,
            'nama_user'    => session('pelanggan.name'),
            'tanggal'      => now()->toDateString(),
            'waktu'        => now(),
            'keterangan'   => $keterangan . ($keterangan ? ' | ' : '') . 'Metode: ' . strtoupper($request->metode),
            'status_order' => 'pending',
        ]);

        foreach ($keranjang as $item) {
            DetailOrder::create([
                'kd_detail'     => 'DTL-' . strtoupper(Str::random(8)),
                'order_kd'      => $kdOrder,
                'pelanggan_kd'  => $kdPelanggan,
                'menu_kd'       => $item->menu_kd,
                'total'         => $item->total,
                'sub_total'     => $item->sub_total,
                'keterangan'    => $item->keterangan,
                'status_detail' => 'pending',
            ]);
        }

        // Kosongkan keranjang sekarang — order sudah masuk
        DetailOrderTemporary::where('pelanggan_kd', $kdPelanggan)->delete();
        session(['keranjang_count' => 0, 'checkout_keterangan' => null]);

        // Redirect ke halaman pembayaran sesuai metode
        return redirect()->route('pelanggan.pembayaran', [
            'kd_order' => $kdOrder,
            'metode'   => $request->metode,
        ]);
    }

    /**
     * Halaman pembayaran setelah order dibuat (QRIS / Kasir).
     */
    public function pembayaran(string $kd_order, Request $request)
    {
        $sess = session('pelanggan');

        $order = Order::with('detailOrders.menu')
            ->where('kd_pelanggan', $sess['kd_pelanggan'])
            ->where('kd_order', $kd_order)
            ->firstOrFail();

        $totalHarga = $order->detailOrders->sum('sub_total');
        $metode     = $request->query('metode');

        return view('pelanggan.pembayaran', compact('order', 'totalHarga', 'metode'));
    }

    public function konfirmasi(Request $request, string $kd_order)
    {
        $request->validate(['metode' => 'required|in:cash,qris']);

        $sess  = session('pelanggan');
        $order = Order::where('kd_pelanggan', $sess['kd_pelanggan'])
            ->where('kd_order', $kd_order)
            ->firstOrFail();

        $order->update(['keterangan' => ($order->keterangan ? $order->keterangan . ' | ' : '') . 'Metode: ' . strtoupper($request->metode)]);

        return response()->json(['success' => true]);
    }
}