<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Order;

class PembayaranController extends Controller
{
    public function index($kd_order)
    {
        $order = Order::with(['detailOrders.menu'])->findOrFail($kd_order);

        return view('pelanggan.pembayaran', compact('order'));
    }
}