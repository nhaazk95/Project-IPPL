<?php
    global $con;
    $co            = new Resto();
    $authPelanggan = $co->AuthPelanggan($_SESSION['username']);
    $authUser      = $co->AuthUser($_SESSION['username']);
    $no_meja       = $authPelanggan['no_meja'] ?? '-';
    $nama_user     = $_SESSION['username'] ?? 'Pelanggan';
    $data_kd       = $_GET['kd'] ?? null;
    $total_bayar   = $_GET['total'] ?? 0;
    $metodePembayaran = $_GET['metode'] ?? null;
    $tanggal       = date('d/m/Y H:i');

    // Ambil detail pesanan
    $pesanan = [];
    if ($data_kd) {
        $sqlP = "SELECT t.*, m.name_menu, m.harga FROM tb_detail_order_temporary t
                 LEFT JOIN tb_menu m ON t.menu_kd = m.kd_menu
                 WHERE t.order_kd = '$data_kd'";
        $exeP = mysqli_query($con, $sqlP);
        while ($row = mysqli_fetch_assoc($exeP)) $pesanan[] = $row;
    }

    // Fallback jika $data_kd null
    if (!$data_kd) {
        $no_meja2 = $authPelanggan['no_meja'] ?? null;
        if ($no_meja2) {
            $sqlFallback = "SELECT kd_order FROM tb_order WHERE no_meja='$no_meja2'";
            $exeFallback = mysqli_query($con, $sqlFallback);
            $dtaFallback = mysqli_fetch_assoc($exeFallback);
            $data_kd     = $dtaFallback['kd_order'] ?? null;
        }
        if ($data_kd && $total_bayar == 0) {
            $sqlT = "SELECT SUM(sub_total) as sub FROM tb_detail_order_temporary WHERE order_kd='$data_kd'";
            $exeT = mysqli_query($con, $sqlT);
            $dtt  = mysqli_fetch_assoc($exeT);
            $total_bayar = $dtt['sub'] ?? 0;

            $sqlP2 = "SELECT t.*, m.name_menu, m.harga FROM tb_detail_order_temporary t
                      LEFT JOIN tb_menu m ON t.menu_kd = m.kd_menu
                      WHERE t.order_kd = '$data_kd'";
            $exeP2 = mysqli_query($con, $sqlP2);
            $pesanan = [];
            while ($row = mysqli_fetch_assoc($exeP2)) $pesanan[] = $row;
        }
    }

    // Encode data pesanan untuk nota (JSON ke JS)
    $pesananJson = json_encode($pesanan);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap');
:root {
    --primary:#FF6B35; --primary-dk:#e85520;
    --bg:#F7F5F2; --card-bg:#fff;
    --radius:18px;
    --shadow:0 4px 24px rgba(0,0,0,0.08);
    --shadow-lg:0 8px 40px rgba(255,107,53,0.18);
}
.co-wrap { font-family:'Nunito',sans-serif; padding-bottom:100px; }
.co-header {
    padding:16px; display:flex; align-items:center; gap:12px;
    background:var(--card-bg); box-shadow:0 2px 12px rgba(0,0,0,0.05);
    position:sticky; top:0; z-index:10;
}
.co-header a {
    width:38px; height:38px; border-radius:50%;
    background:var(--bg); display:flex; align-items:center; justify-content:center;
    color:#1a1a1a; text-decoration:none; font-size:16px; transition:background .2s;
}
.co-header a:hover { background:#ffe8e0; color:var(--primary); }
.co-header h2 { font-family:'Playfair Display',serif; font-size:20px; font-weight:700; flex:1; }

.co-ringkasan { margin:16px; background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow); padding:16px; }
.co-ringkasan h4 { font-size:15px; font-weight:900; margin-bottom:12px; }
.co-ring-item { display:flex; justify-content:space-between; font-size:13px; padding:6px 0; border-bottom:1px dashed #f0f0f0; }
.co-ring-item:last-child { border-bottom:none; }
.co-ring-item span:first-child { color:#888; }
.co-ring-item span:last-child { font-weight:800; }
.co-ring-total { display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; border-top:2px solid #f0f0f0; }
.co-ring-total span:first-child { font-size:14px; font-weight:700; color:#888; }
.co-ring-total span:last-child  { font-size:22px; font-weight:900; color:var(--primary); }

.co-metode-title { padding:20px 16px 10px; font-size:16px; font-weight:900; }
.co-metode-list  { display:flex; flex-direction:column; gap:12px; padding:0 16px; }
.metode-card {
    background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow);
    padding:16px 20px; display:flex; align-items:center; gap:16px;
    cursor:pointer; border:2px solid transparent; transition:border .2s, box-shadow .2s;
    text-decoration:none; color:#1a1a1a;
}
.metode-card:hover { border-color:var(--primary); box-shadow:var(--shadow-lg); color:#1a1a1a; text-decoration:none; }
.metode-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; }
.metode-icon.qr    { background:#e8f0ff; }
.metode-icon.kasir { background:#e8f8ec; }
.metode-info h5 { font-size:15px; font-weight:800; margin-bottom:2px; }
.metode-info p  { font-size:12px; color:#888; margin:0; }
.metode-arrow   { margin-left:auto; color:#ccc; font-size:18px; }

/* QRIS Panel */
.qr-panel { margin:16px; background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow); padding:24px; text-align:center; animation:fadeUp .4s ease; }
.qr-panel h4 { font-size:18px; font-weight:900; margin-bottom:6px; }
.qr-panel > p { font-size:13px; color:#888; margin-bottom:20px; }
.qr-img { width:220px; height:220px; margin:0 auto 16px; background:#f5f5f5; border-radius:16px; display:flex; align-items:center; justify-content:center; border:2px dashed #ddd; overflow:hidden; }
.qr-img img { width:100%; height:100%; object-fit:contain; }
.qr-placeholder { font-size:60px; opacity:.3; }
.qr-amount { font-size:24px; font-weight:900; color:var(--primary); margin-bottom:6px; }
.qr-note   { font-size:12px; color:#888; margin-bottom:20px; }
.qr-steps  { text-align:left; background:var(--bg); border-radius:12px; padding:14px 16px; font-size:13px; margin-bottom:0; }
.qr-steps li { margin-bottom:6px; font-weight:600; color:#444; }

/* Kasir Panel */
.kasir-panel { margin:16px; background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow); padding:24px; text-align:center; animation:fadeUp .4s ease; }
.kasir-big-icon { font-size:72px; margin-bottom:16px; }
.kasir-panel h4 { font-size:20px; font-weight:900; margin-bottom:10px; }
.kasir-msg { background:linear-gradient(135deg, var(--primary), var(--primary-dk)); color:#fff; border-radius:14px; padding:16px 20px; font-size:15px; font-weight:700; margin-bottom:16px; line-height:1.6; }
.kasir-total { font-size:28px; font-weight:900; color:var(--primary); margin-bottom:6px; }
.kasir-sub   { font-size:13px; color:#888; margin-bottom:16px; }
.kasir-info  { background:#fff8e6; border-radius:12px; padding:14px 16px; text-align:left; font-size:13px; color:#856404; font-weight:600; margin-bottom:0; }

/* QR Kasir (kode unik meja) */
.kasir-qr-box {
    margin:16px 0 0;
    background:var(--bg); border-radius:14px; padding:16px;
    text-align:center;
}
.kasir-qr-box p { font-size:12px; color:#888; font-weight:700; margin-bottom:10px; }
.kasir-qr-code {
    width:160px; height:160px; margin:0 auto;
    background:#fff; border-radius:12px; border:2px solid #eee;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800; color:#1a1a1a;
    overflow:hidden;
}
.kasir-qr-code canvas { border-radius:8px; }

/* Buttons */
.btn-back {
    display:block; margin:12px 16px; padding:14px;
    border:2px solid var(--primary); border-radius:var(--radius);
    background:#fff; color:var(--primary);
    font-family:'Nunito',sans-serif; font-size:15px; font-weight:900;
    text-align:center; text-decoration:none; transition:background .2s;
}
.btn-back:hover { background:#fff8f5; color:var(--primary); text-decoration:none; }

.btn-selesai-bayar {
    display:block; margin:0 16px 12px; padding:15px;
    border:none; border-radius:var(--radius);
    background:linear-gradient(135deg, var(--primary), var(--primary-dk));
    color:#fff; font-family:'Nunito',sans-serif;
    font-size:16px; font-weight:900;
    text-align:center; text-decoration:none; cursor:pointer;
    box-shadow:var(--shadow-lg); transition:transform .15s;
}
.btn-selesai-bayar:hover { transform:scale(1.02); color:#fff; text-decoration:none; }

.co-error { margin:40px 16px; text-align:center; padding:40px 20px; background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow); color:#888; }
.co-error i { font-size:48px; display:block; margin-bottom:16px; opacity:.3; }
.co-error h3 { font-size:18px; font-weight:800; margin-bottom:8px; color:#1a1a1a; }

/* ── NOTA MODAL ── */
.nota-overlay {
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.6); align-items:center; justify-content:center;
    padding:20px;
}
.nota-overlay.open { display:flex; }
.nota-box {
    background:#fff; border-radius:20px; width:100%; max-width:360px;
    max-height:85vh; overflow-y:auto;
    animation:fadeUp .3s ease;
    font-family:'Nunito',sans-serif;
}
.nota-header {
    background:linear-gradient(135deg, #1a1a1a, #2d2d2d);
    border-radius:20px 20px 0 0; padding:20px 24px;
    text-align:center; color:#fff;
}
.nota-header h3 { font-family:'Playfair Display',serif; font-size:20px; font-weight:700; margin-bottom:4px; }
.nota-header p  { font-size:12px; opacity:.6; margin:0; }
.nota-body { padding:20px 24px; }
.nota-divider { border:none; border-top:1px dashed #eee; margin:12px 0; }
.nota-info-row { display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px; }
.nota-info-row span:first-child { color:#888; font-weight:600; }
.nota-info-row span:last-child  { font-weight:800; }
.nota-item { display:flex; justify-content:space-between; font-size:13px; padding:6px 0; border-bottom:1px dashed #f5f5f5; }
.nota-item:last-child { border-bottom:none; }
.nota-item .item-name { color:#1a1a1a; font-weight:700; flex:1; }
.nota-item .item-qty  { color:#888; font-size:12px; width:40px; text-align:center; }
.nota-item .item-price { font-weight:800; color:#1a1a1a; }
.nota-total-row { display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; border-top:2px solid #1a1a1a; }
.nota-total-row span:first-child { font-size:14px; font-weight:800; }
.nota-total-row span:last-child  { font-size:20px; font-weight:900; color:var(--primary); }
.nota-thankyou { text-align:center; margin-top:16px; padding:14px; background:var(--bg); border-radius:12px; }
.nota-thankyou p { font-size:13px; font-weight:800; color:#1a1a1a; margin-bottom:2px; }
.nota-thankyou small { font-size:11px; color:#888; }
.nota-footer { padding:16px 24px 20px; display:flex; gap:10px; }
.btn-nota-print {
    flex:1; padding:13px; border:none; border-radius:12px;
    background:var(--primary); color:#fff;
    font-family:'Nunito',sans-serif; font-size:14px; font-weight:900;
    cursor:pointer; transition:background .2s;
}
.btn-nota-print:hover { background:var(--primary-dk); }
.btn-nota-close {
    flex:1; padding:13px; border:2px solid #eee; border-radius:12px;
    background:#fff; color:#666;
    font-family:'Nunito',sans-serif; font-size:14px; font-weight:900;
    cursor:pointer; transition:border .2s;
}
.btn-nota-close:hover { border-color:#aaa; color:#1a1a1a; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);} }

@media print {
    .bottom-nav, .btn-selesai, .btn-back, .nota-footer, .co-header { display:none !important; }
    .nota-overlay { position:static; background:none; padding:0; }
    .nota-box { box-shadow:none; max-height:none; border-radius:0; }
}
</style>

<!-- ── NOTA MODAL ── -->
<div class="nota-overlay" id="notaOverlay">
    <div class="nota-box" id="notaBox">
        <div class="nota-header">
            <h3>🧾 Nota Pembayaran</h3>
            <p>TechDiscovery Restaurant</p>
        </div>
        <div class="nota-body">
            <div class="nota-info-row"><span>No. Order</span><span><?= htmlspecialchars($data_kd ?? '-') ?></span></div>
            <div class="nota-info-row"><span>Meja</span><span><?= htmlspecialchars($no_meja) ?></span></div>
            <div class="nota-info-row"><span>Pelanggan</span><span><?= htmlspecialchars($nama_user) ?></span></div>
            <div class="nota-info-row"><span>Tanggal</span><span><?= $tanggal ?></span></div>
            <div class="nota-info-row"><span>Metode</span><span id="notaMetode">-</span></div>
            <hr class="nota-divider">
            <div style="font-size:12px;font-weight:800;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Detail Pesanan</div>
            <?php foreach ($pesanan as $p): ?>
            <div class="nota-item">
                <span class="item-name"><?= htmlspecialchars($p['name_menu'] ?? $p['menu_kd']) ?></span>
                <span class="item-qty">×<?= $p['total'] ?></span>
                <span class="item-price">Rp <?= number_format($p['sub_total'], 0, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
            <div class="nota-total-row">
                <span>TOTAL</span>
                <span>Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
            </div>
            <div class="nota-thankyou">
                <p>Terima kasih! 🙏</p>
                <small>Selamat menikmati makanan Anda</small>
            </div>
        </div>
        <div class="nota-footer">
            <button class="btn-nota-print" onclick="cetakNota()">🖨️ Cetak Nota</button>
            <button class="btn-nota-close" onclick="selesaiDanKeluar()">Selesai & Keluar</button>
        </div>
    </div>
</div>

<div class="co-wrap">
    <div class="co-header">
        <a href="?page=transaksi"><i class="fa fa-arrow-left"></i></a>
        <h2>💳 Pembayaran</h2>
    </div>

    <?php if (!$data_kd || count($pesanan) == 0): ?>
    <div class="co-error">
        <i class="fa fa-exclamation-circle"></i>
        <h3>Data pesanan tidak ditemukan</h3>
        <p>Silakan kembali ke keranjang dan coba lagi.</p>
        <a href="?page=transaksi" class="btn-back" style="margin:16px auto;display:inline-block;width:auto;padding:12px 24px;">
            Kembali ke Keranjang
        </a>
    </div>

    <?php else: ?>

    <!-- Ringkasan -->
    <div class="co-ringkasan">
        <h4>📋 Ringkasan Pesanan</h4>
        <?php foreach ($pesanan as $p): ?>
        <div class="co-ring-item">
            <span><?= htmlspecialchars($p['name_menu'] ?? $p['menu_kd']) ?> ×<?= $p['total'] ?></span>
            <span>Rp <?= number_format($p['sub_total'], 0, ',', '.') ?></span>
        </div>
        <?php endforeach; ?>
        <div class="co-ring-total">
            <span>Total Bayar</span>
            <span>Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
        </div>
    </div>

    <?php if (!$metodePembayaran): ?>
    <!-- Pilih Metode -->
    <div class="co-metode-title">Pilih Metode Pembayaran</div>
    <div class="co-metode-list">
        <a href="?page=checkout&kd=<?= urlencode($data_kd) ?>&total=<?= $total_bayar ?>&metode=qris" class="metode-card">
            <div class="metode-icon qr">📱</div>
            <div class="metode-info"><h5>QRIS</h5><p>Bayar via QR Code (GoPay, OVO, Dana, dll)</p></div>
            <i class="fa fa-chevron-right metode-arrow"></i>
        </a>
        <a href="?page=checkout&kd=<?= urlencode($data_kd) ?>&total=<?= $total_bayar ?>&metode=kasir" class="metode-card">
            <div class="metode-icon kasir">🏪</div>
            <div class="metode-info"><h5>Kasir</h5><p>Bayar langsung ke kasir (tunai/debit)</p></div>
            <i class="fa fa-chevron-right metode-arrow"></i>
        </a>
    </div>

    <?php elseif ($metodePembayaran == 'qris'): ?>
    <!-- ── QRIS ── -->
    <div class="qr-panel">
        <h4>Scan QR untuk Membayar</h4>
        <p>Gunakan aplikasi dompet digital untuk scan QR di bawah</p>
        <div class="qr-img">
            <!-- Ganti dengan: <img src="images/qris.png" alt="QRIS"> -->
            <span class="qr-placeholder">📱</span>
        </div>
        <div class="qr-amount">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
        <div class="qr-note">Meja <?= htmlspecialchars($no_meja) ?> &nbsp;·&nbsp; Order <?= htmlspecialchars($data_kd) ?></div>
        <ul class="qr-steps">
            <li>1. Buka aplikasi GoPay / OVO / Dana / BCA Mobile</li>
            <li>2. Pilih menu "Scan QR" atau "Bayar"</li>
            <li>3. Arahkan kamera ke QR Code di atas</li>
            <li>4. Konfirmasi jumlah pembayaran</li>
            <li>5. Tunjukkan bukti pembayaran ke kasir</li>
        </ul>
    </div>
    <!-- Tombol Pesanan Selesai -->
    <button class="btn-selesai-bayar" onclick="tampilNota('QRIS')">
        ✅ Pesanan Selesai — Lihat Nota
    </button>
    <a href="?page=transaksi" class="btn-back">
        <i class="fa fa-arrow-left"></i> Kembali ke Keranjang
    </a>

    <?php elseif ($metodePembayaran == 'kasir'): ?>
    <!-- ── KASIR ── -->
    <div class="kasir-panel">
        <div class="kasir-big-icon">🏪</div>
        <h4>Pembayaran di Kasir</h4>
        <div class="kasir-msg">
            Tunjukkan QR Code di bawah kepada kasir.<br>
            Kasir akan memindai kode pesananmu.
        </div>
        <div class="kasir-total">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
        <div class="kasir-sub">Total yang harus dibayar &nbsp;·&nbsp; Meja <?= htmlspecialchars($no_meja) ?></div>

        <!-- QR Code unik untuk kasir -->
        <div class="kasir-qr-box">
            <p>📷 QR Code Pesanan — Tunjukkan ke kasir</p>
            <div class="kasir-qr-code">
                <canvas id="kasirQrCanvas"></canvas>
            </div>
            <div style="margin-top:10px;font-size:12px;font-weight:800;color:#888;">
                <?= htmlspecialchars($data_kd) ?> · Meja <?= htmlspecialchars($no_meja) ?>
            </div>
        </div>

        <div class="kasir-info" style="margin-top:14px;">
            <i class="fa fa-info-circle"></i>
            Kasir menerima pembayaran tunai dan kartu debit/kredit.
        </div>
    </div>
    <!-- Tombol Pesanan Selesai -->
    <button class="btn-selesai-bayar" onclick="tampilNota('Kasir')">
        ✅ Pesanan Selesai — Lihat Nota
    </button>
    <a href="?page=transaksi" class="btn-back">
        <i class="fa fa-arrow-left"></i> Kembali ke Keranjang
    </a>

    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- QR Code library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Generate QR kasir
<?php if ($metodePembayayaran == 'kasir' || true): ?>
window.addEventListener('load', function() {
    var canvas = document.getElementById('kasirQrCanvas');
    if (canvas) {
        var qrData = "ORDER:<?= $data_kd ?>|MEJA:<?= $no_meja ?>|TOTAL:<?= $total_bayar ?>";
        new QRCode(canvas.parentElement, {
            text: qrData,
            width: 150,
            height: 150,
            colorDark: "#1a1a1a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
        canvas.remove(); // hapus canvas placeholder, QRCode buat sendiri
    }
});
<?php endif; ?>

// Tampilkan nota
function tampilNota(metode) {
    document.getElementById('notaMetode').textContent = metode;
    document.getElementById('notaOverlay').classList.add('open');
}

// Cetak nota
function cetakNota() {
    var nota = document.getElementById('notaBox').innerHTML;
    var win = window.open('', '_blank', 'width=400,height=600');
    win.document.write(`
        <html><head>
        <title>Nota - TechDiscovery</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            body { font-family:'Nunito',sans-serif; padding:20px; max-width:320px; margin:0 auto; }
            * { box-sizing:border-box; }
            .nota-header { background:#1a1a1a; color:#fff; padding:20px; text-align:center; border-radius:12px 12px 0 0; }
            .nota-header h3 { font-size:18px; margin-bottom:4px; }
            .nota-header p  { font-size:11px; opacity:.6; margin:0; }
            .nota-body { padding:16px; border:1px solid #eee; border-top:none; border-radius:0 0 12px 12px; }
            .nota-info-row { display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px; }
            .nota-info-row span:first-child { color:#888; }
            .nota-info-row span:last-child { font-weight:800; }
            hr { border:none; border-top:1px dashed #eee; margin:10px 0; }
            .nota-item { display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px dashed #f5f5f5; }
            .item-name { flex:1; font-weight:700; }
            .item-qty  { width:36px; text-align:center; color:#888; }
            .item-price { font-weight:800; }
            .nota-total-row { display:flex; justify-content:space-between; margin-top:10px; padding-top:10px; border-top:2px solid #1a1a1a; font-weight:900; }
            .nota-thankyou { text-align:center; margin-top:14px; padding:12px; background:#f7f5f2; border-radius:10px; font-size:12px; }
            .nota-footer { display:none; }
        </style>
        </head><body>${nota}</body></html>
    `);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); win.close(); }, 500);
}

// Selesai & Keluar (logout)
function selesaiDanKeluar() {
    if (confirm('Yakin ingin keluar setelah pembayaran selesai?')) {
        window.location.href = 'pagePelanggan.php?logout=true';
    }
}
</script>