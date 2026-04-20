<?php
// ============================================================
// routes/web.php — DNUSA Resto (FIXED)
// ============================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\MejaController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksi;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\OrderController;
use App\Http\Controllers\Kasir\TransaksiController as KasirTransaksi;
use App\Http\Controllers\Pelanggan\AuthController as PelangganAuth;
use App\Http\Controllers\Pelanggan\BerandaController;
use App\Http\Controllers\Pelanggan\MenuController as PelangganMenu;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\PesananController;
use App\Http\Controllers\Pelanggan\PembayaranController;

// ── Root ──────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth Staff ────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    Route::get('/forgot-password',        [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/confirm-password',  [AuthController::class, 'showConfirm'])->name('password.confirm');
    Route::post('/confirm-password', [AuthController::class, 'confirm']);
});

// ── Admin ─────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resource('level', LevelController::class)
        ->only(['index','update','destroy']);

    Route::resource('menu',     MenuController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('meja',     MejaController::class);

    Route::get('/transaksi',                    [AdminTransaksi::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi/{kd_order}/bayar',  [AdminTransaksi::class, 'bayar'])->name('transaksi.bayar');
    Route::get('/transaksi/{id}',               [AdminTransaksi::class, 'show'])->name('transaksi.show');

    Route::get('/laporan/orderan',              [LaporanController::class, 'orderan'])->name('laporan.orderan');
    Route::get('/laporan/orderan/export',       [LaporanController::class, 'exportOrderan'])->name('laporan.export');
    Route::get('/laporan/transaksi',            [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('/laporan/transaksi/export',     [LaporanController::class, 'exportTransaksi'])->name('laporan.transaksi.export');
});

// ── Kasir (FIXED DI SINI) ─────────────────────────────────
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard',               [KasirDashboard::class, 'index'])->name('dashboard');

    Route::get('/order',                   [OrderController::class, 'index'])->name('order');
    Route::get('/order/{kd_order}',        [OrderController::class, 'detail'])->name('order.detail');
    Route::post('/order/{kd_order}/bayar', [OrderController::class, 'prosesBayar'])->name('proses-bayar');

    // ✅ FIX DI SINI (sebelumnya 'transaksi')
    Route::get('/transaksi',               [KasirTransaksi::class, 'index'])->name('transaksi.index');

    Route::get('/struk/{kd_transaksi}',    [KasirTransaksi::class, 'struk'])->name('struk');
    Route::get('/laporan',                 [KasirTransaksi::class, 'laporan'])->name('laporan');

    // API endpoint untuk notifikasi order baru (polling)
    Route::get('/api/notif-order',         [KasirDashboard::class, 'notifOrder'])->name('api.notif');
});

// ── Pelanggan ─────────────────────────────────────────────
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/masuk',  [PelangganAuth::class, 'showLogin'])->name('login');
    Route::post('/masuk', [PelangganAuth::class, 'login'])->name('masuk');
    Route::post('/keluar',[PelangganAuth::class, 'logout'])->name('logout');

    Route::middleware('pelanggan.session')->group(function () {
        Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

        Route::get('/menu',           [PelangganMenu::class, 'index'])->name('menu');
        Route::get('/menu/{kd_menu}', [PelangganMenu::class, 'show'])->name('menu.detail');

        Route::get('/keranjang',                [KeranjangController::class, 'index'])->name('keranjang');
        Route::post('/keranjang/tambah',        [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
        Route::put('/keranjang/{kd_detail}',    [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{kd_detail}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
        Route::post('/checkout',                [KeranjangController::class, 'checkout'])->name('checkout');

        Route::get('/pesanan',                            [PesananController::class, 'index'])->name('pesanan');
        Route::get('/pembayaran/{kd_order}',              [PesananController::class, 'pembayaran'])->name('pembayaran');
        Route::post('/pembayaran/{kd_order}/konfirmasi',  [PesananController::class, 'konfirmasi'])->name('konfirmasi-bayar');
    });
});