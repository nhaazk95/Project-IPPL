<?php
    global $con;
    $tr            = new Resto();
    $authPelanggan = $tr->AuthPelanggan($_SESSION['username']);
    $authUser      = $tr->AuthUser($_SESSION['username']);
    $no_meja       = $authPelanggan['no_meja'] ?? null;
    $data_kd       = null;
    $pesanan       = [];
    $total_bayar   = 0;
    $hapusBerhasil = false; // FIX: flag untuk redirect via JS

    if ($no_meja) {
        $sql = "SELECT kd_order FROM tb_order WHERE no_meja='$no_meja'";
        $exe = mysqli_query($con, $sql);
        $dta = mysqli_fetch_assoc($exe);
        $data_kd = $dta['kd_order'] ?? null;
    }

    // FIX: Handle hapus SEBELUM query pesanan, gunakan flag bukan header()
    if (isset($_GET['hapus']) && isset($_GET['kd']) && $data_kd) {
        $tr->delete("tb_detail_order_temporary", "kd_detail", $_GET['kd'], "");
        $cekE = mysqli_query($con, "SELECT COUNT(*) as jml FROM tb_detail_order_temporary WHERE order_kd='$data_kd'");
        $cekD = mysqli_fetch_assoc($cekE);
        if ($cekD['jml'] == 0) {
            $tr->update("tb_order", "status_order='belum_beli'", "kd_order", $data_kd, "");
        }
        $hapusBerhasil = true; // ← set flag, redirect via JS di bawah
    }

    if ($data_kd) {
        $sqlP = "SELECT t.*, m.name_menu, m.photo FROM tb_detail_order_temporary t
                 LEFT JOIN tb_menu m ON t.menu_kd = m.kd_menu
                 WHERE t.order_kd = '$data_kd'";
        $exeP = mysqli_query($con, $sqlP);
        while ($row = mysqli_fetch_assoc($exeP)) $pesanan[] = $row;

        $sqlT = "SELECT SUM(sub_total) as sub FROM tb_detail_order_temporary WHERE order_kd='$data_kd'";
        $exeT = mysqli_query($con, $sqlT);
        $dtt  = mysqli_fetch_assoc($exeT);
        $total_bayar = $dtt['sub'] ?? 0;
    }
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Nunito:wght@400;600;700;800;900&display=swap');
:root {
    --brown-dark:  #3E1F00;
    --brown-mid:   #7B3F00;
    --brown-warm:  #A0522D;
    --brown-light: #C4956A;
    --cream:       #FDF6EC;
    --cream-dark:  #EDE0CC;
    --gold:        #C8973A;
    --text-dark:   #2C1A0E;
    --text-muted:  #A07850;
    --radius:      18px;
    --shadow:      0 4px 24px rgba(62,31,0,0.08);
    --shadow-lg:   0 8px 40px rgba(62,31,0,0.18);
}
.tr-wrap { font-family:'Nunito',sans-serif; padding-bottom:200px; background:var(--cream); }
.tr-header { padding:14px 16px; display:flex; align-items:center; gap:12px; background:var(--brown-dark); box-shadow:0 3px 16px rgba(62,31,0,0.3); position:sticky; top:0; z-index:10; }
.tr-header a { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; color:var(--gold); text-decoration:none; font-size:15px; transition:background .2s; }
.tr-header a:hover { background:rgba(200,151,58,0.25); }
.tr-header h2 { font-family:'Lora',serif; font-size:19px; font-weight:700; color:var(--gold); flex:1; }
.tr-status { margin:14px; border-radius:var(--radius); padding:13px 16px; display:flex; align-items:center; gap:12px; font-size:13px; font-weight:700; border:1.5px solid transparent; }
.tr-status.pending { background:#fff8e6; color:#b07d00; border-color:#f0d080; }
.tr-status.dimasak { background:#fff0e6; color:#c44b00; border-color:#f0b080; }
.tr-status.siap    { background:#e8f8ec; color:#1a7a35; border-color:#80c090; }
.tr-status.diambil { background:#e8f0ff; color:#1a3fa0; border-color:#80a0e0; }
.tr-status .st-icon { font-size:22px; }
.tr-sec-title { padding:14px 14px 8px; font-family:'Lora',serif; font-size:16px; font-weight:700; color:var(--brown-dark); }
.tr-item { margin:0 14px 12px; background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; display:flex; border:1.5px solid var(--cream-dark); animation:fadeUp .35s ease both; }
.tr-item img { width:85px; height:85px; object-fit:cover; flex-shrink:0; }
.tr-body { flex:1; padding:11px 12px; display:flex; flex-direction:column; justify-content:space-between; }
.tr-body h5 { font-size:13px; font-weight:800; color:var(--text-dark); margin-bottom:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tr-meta { font-size:11px; color:var(--text-muted); font-weight:600; }
.tr-bottom { display:flex; align-items:center; justify-content:space-between; margin-top:6px; }
.tr-price { font-size:14px; font-weight:900; color:var(--brown-warm); }
.btn-del { width:28px; height:28px; border-radius:50%; background:#fff0e6; color:var(--brown-warm); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:12px; transition:background .2s; }
.btn-del:hover { background:var(--brown-warm); color:#fff; }
.sb { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:800; }
.sb.pending { background:#fff3cd; color:#856404; }
.sb.dimasak { background:#fde8d8; color:#c44b00; }
.sb.siap    { background:#d4edda; color:#155724; }
.sb.diambil { background:#cce5ff; color:#004085; }
.tr-summary-box { position:fixed; bottom:65px; left:0; right:0; z-index:10; background:var(--brown-dark); border-top:2px solid var(--gold); padding:14px 20px 10px; box-shadow:0 -4px 20px rgba(62,31,0,0.3); }
.sum-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; }
.sum-label { font-size:13px; color:rgba(255,255,255,0.6); font-weight:700; }
.sum-items { font-size:12px; color:rgba(255,255,255,0.5); }
.sum-total { font-size:22px; font-weight:900; color:var(--gold); }
.btn-checkout { display:block; width:100%; padding:13px; border:none; border-radius:var(--radius); background:linear-gradient(135deg, var(--brown-warm), var(--gold)); color:var(--brown-dark); font-family:'Nunito',sans-serif; font-size:15px; font-weight:900; cursor:pointer; margin-top:10px; text-align:center; text-decoration:none; transition:opacity .2s; box-shadow:0 4px 16px rgba(200,151,58,0.3); }
.btn-checkout:hover { opacity:.9; color:var(--brown-dark); text-decoration:none; }
.tr-empty { text-align:center; padding:80px 20px; color:var(--text-muted); }
.tr-empty i { font-size:60px; display:block; margin-bottom:16px; opacity:.2; }
.tr-empty h3 { font-family:'Lora',serif; font-size:18px; font-weight:700; color:var(--brown-dark); margin-bottom:8px; }
.tr-empty p  { font-size:13px; }
.tr-empty a { display:inline-block; margin-top:20px; background:var(--brown-dark); color:var(--gold); padding:12px 28px; border-radius:50px; font-weight:800; text-decoration:none; border:1.5px solid var(--gold); }
.tr-empty a:hover { background:var(--brown-mid); color:var(--gold); text-decoration:none; }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }
</style>

<!-- FIX: Redirect via JS setelah hapus, bukan header() -->
<?php if ($hapusBerhasil): ?>
<script>window.location.href = '?page=transaksi';</script>
<?php endif; ?>

<div class="tr-wrap">
    <div class="tr-header">
        <a href="?page=dashboard"><i class="fa fa-arrow-left"></i></a>
        <h2>🛒 Keranjang Pesanan</h2>
    </div>

    <?php if (count($pesanan) > 0): ?>
        <?php
        $allStatus = array_column($pesanan, 'status_detail');
        $ds = 'pending';
        if (in_array('dimasak', $allStatus)) $ds = 'dimasak';
        if (in_array('siap',    $allStatus)) $ds = 'siap';
        if (in_array('diambil', $allStatus)) $ds = 'diambil';
        $si = [
            'pending' => ['⏳', 'Menunggu konfirmasi dapur'],
            'dimasak' => ['🔥', 'Sedang dimasak'],
            'siap'    => ['✅', 'Siap diambil!'],
            'diambil' => ['📦', 'Sudah diambil'],
        ];
        ?>
        <div class="tr-status <?= $ds ?>">
            <span class="st-icon"><?= $si[$ds][0] ?></span>
            <span><?= $si[$ds][1] ?></span>
        </div>

        <div class="tr-sec-title">Pesananmu (<?= count($pesanan) ?> item)</div>

        <?php foreach ($pesanan as $i => $p): ?>
        <div class="tr-item" style="animation-delay:<?= $i * 0.07 ?>s">
            <img src="img/<?= htmlspecialchars($p['photo'] ?? '') ?>"
                 onerror="this.src='images/icon/logo-blue.png'"
                 alt="menu">
            <div class="tr-body">
                <div>
                    <h5><?= htmlspecialchars($p['name_menu'] ?? $p['menu_kd']) ?></h5>
                    <div class="tr-meta">
                        <?= $p['total'] ?> pcs &nbsp;·&nbsp;
                        <span class="sb <?= $p['status_detail'] ?>"><?= $p['status_detail'] ?></span>
                    </div>
                </div>
                <div class="tr-bottom">
                    <span class="tr-price">Rp <?= number_format($p['sub_total'], 0, ',', '.') ?></span>
                    <?php if ($p['status_detail'] == 'pending'): ?>
                    <button class="btn-del" onclick="hapusItem('<?= $p['kd_detail'] ?>')">
                        <i class="fa fa-trash"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="tr-summary-box">
            <div class="sum-row">
                <span class="sum-label">Total Bayar</span>
                <span class="sum-items"><?= count($pesanan) ?> item</span>
            </div>
            <div class="sum-row">
                <span class="sum-total">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
            </div>
            <a href="pagePelanggan.php?page=checkout&kd=<?= urlencode($data_kd) ?>&total=<?= $total_bayar ?>"
               class="btn-checkout">
                <i class="fa fa-credit-card"></i> &nbsp;Lanjut Pembayaran
            </a>
        </div>

    <?php else: ?>
        <div class="tr-empty">
            <i class="fa fa-shopping-basket"></i>
            <h3>Keranjang Kosong</h3>
            <p>Kamu belum memesan apapun.<br>Yuk mulai pesan!</p>
            <a href="?page=dashboard">Lihat Menu</a>
        </div>
    <?php endif; ?>
</div>

<script>
function hapusItem(kd) {
    if (confirm('Yakin hapus item ini?')) {
        window.location.href = '?page=transaksi&hapus=1&kd=' + kd;
    }
}
</script>