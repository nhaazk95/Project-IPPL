<?php
    $dm                 = new Resto();
    $getMenu            = $dm->selectWhere("tb_menu", "kd_menu", $_GET['kd']);
    $kd                 = $_GET['kd'];
    @$getKategori       = $dm->selectWhere("tb_kategori", "kd_kategori", $_GET['kategori']);
    @$kate              = $_GET['kategori'];
    $autokodedetail     = $dm->autokode("tb_detail_order", "kd_detail", "DM");
    $autokodedetailTemp = $dm->autokode("tb_detail_order_temporary", "kd_detail", "DM");
    $authPelanggan      = $dm->AuthPelanggan($_SESSION['username']);

    // FIX: AuthUser pakai name bukan username (karena session simpan name)
    global $con;
    $sessionUser = $_SESSION['username'] ?? '';
    $sqlAuthUser = "SELECT * FROM tb_user WHERE name = '$sessionUser' AND level = 'Pelanggan' AND status = 1 LIMIT 1";
    $exeAuthUser = mysqli_query($con, $sqlAuthUser);
    $authUser    = mysqli_fetch_assoc($exeAuthUser);

    $no_meja2 = $authPelanggan['no_meja'] ?? null;

    // Ambil parameter 'from' untuk menentukan tujuan tombol back
    $from = $_GET['from'] ?? 'order_menu';

    // Tentukan URL back berdasarkan asal halaman
    if ($from == 'dashboard') {
        $backUrl = '?page=dashboard';
    } else {
        $backUrl = '?page=order_menu&kategori&menu&kd=' . ($getKategori['kd_kategori'] ?? '');
    }

    $sql2    = "SELECT kd_order FROM tb_order WHERE no_meja='$no_meja2'";
    $exe2    = mysqli_query($con, $sql2);
    $dta2    = mysqli_fetch_assoc($exe2);
    $data_kd = $dta2['kd_order'] ?? null;
    $kduser  = $authUser['kd_user'] ?? null;

    $data  = [];
    $assoc = ['sub' => 0];
    if ($data_kd) {
        $data  = $dm->editWhere("tb_detail_order_temporary", 'order_kd', $data_kd, 'user_kd', $kduser);
        $sql   = "SELECT SUM(sub_total) as sub FROM tb_detail_order_temporary WHERE order_kd = '$data_kd'";
        $exec  = mysqli_query($con, $sql);
        $assoc = mysqli_fetch_assoc($exec);
    }
    $jumlahKeranjang = count($data);

    if (isset($_POST['btnTambah'])) {
        $kd_user       = $authUser['kd_user'] ?? null;
        $status_detail = "pending";
        $total         = (int)$_POST['total'];
        $menu_kd       = $getMenu['kd_menu'];
        $sub_total     = (int)$_POST['sub_total'];
        $keterangan    = trim($_POST['keterangan'] ?? '');

        if (!$kd_user) {
            $response = ['response' => 'negative', 'alert' => 'Sesi user tidak ditemukan, silakan login ulang'];
        } elseif ($total <= 0 || $sub_total <= 0) {
            $response = ['response' => 'negative', 'alert' => 'Isi jumlah dengan benar'];
        } elseif (!$data_kd) {
            $response = ['response' => 'negative', 'alert' => 'Sesi order tidak ditemukan, silakan login ulang'];
        } else {
            $sql = "SELECT * FROM tb_detail_order_temporary WHERE menu_kd='$menu_kd' AND order_kd='$data_kd'";
            $exe = mysqli_query($con, $sql);
            $num = mysqli_num_rows($exe);
            $dta = mysqli_fetch_assoc($exe);

            if ($num > 0) {
                // Update jumlah jika menu sudah ada di keranjang
                $total     = $dta['total'] + $total;
                $sub_total = $dta['sub_total'] + $sub_total;
                $value     = "total='$total', sub_total='$sub_total'";
                $response  = $dm->update("tb_detail_order_temporary", $value, "menu_kd = '$menu_kd' AND order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd&from=$from");

                // Update tb_detail_order hanya jika baris sudah ada
                $sqlCekDO = "SELECT kd_detail FROM tb_detail_order WHERE menu_kd='$menu_kd' AND order_kd='$data_kd'";
                $exeCekDO = mysqli_query($con, $sqlCekDO);
                if (mysqli_num_rows($exeCekDO) > 0) {
                    $dm->update("tb_detail_order", $value, "menu_kd= '$menu_kd' AND order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd&from=$from");
                }
            } else {
                // Insert ke tb_detail_order_temporary
                $valueDetailTemp = "'$autokodedetailTemp', '$data_kd', '$kd_user', '$menu_kd', '', '$total', '$sub_total', '', '', '', '$status_detail'";
                $response        = $dm->insert("tb_detail_order_temporary", $valueDetailTemp, "?page=detail_menu&kategori=$kate&kd=$kd&from=$from");

                // FIX: Insert ke tb_detail_order dengan transaksi_kd = NULL
                // Kolom transaksi_kd sudah diubah jadi nullable di database
                $valueDetail = "'$autokodedetail', '$data_kd', '$kd_user', '$menu_kd', NULL, '$total', '$sub_total', '', '', '', '$status_detail'";
                $dm->insert("tb_detail_order", $valueDetail, "?page=detail_menu&kategori=$kate&kd=$kd&from=$from");

                $dm->update("tb_order", "status_order='belum_bayar'", "kd_order", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd&from=$from");
            }

            if (!empty($keterangan)) {
                $keterangan = mysqli_real_escape_string($con, $keterangan);
                $valCat     = "keterangan='$keterangan', status_keterangan='N'";
                $dm->update("tb_detail_order_temporary", $valCat, "order_kd", $data_kd, "");
                $dm->update("tb_detail_order", $valCat, "order_kd", $data_kd, "");
            }
        }
    }

    if (isset($_GET['hapus2'])) {
        $kdHapus  = $_GET['kd'];
        $response = $dm->delete("tb_detail_order_temporary", "kd_detail", $kdHapus, "?page=detail_menu&kategori=$kate&kd=" . $_GET['menu'] . "&from=$from");
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
    --shadow:      0 4px 24px rgba(62,31,0,0.1);
    --shadow-lg:   0 8px 40px rgba(62,31,0,0.18);
}
.dm-wrap { font-family:'Nunito',sans-serif; padding-bottom:20px; background:var(--cream); }
.dm-header { display:flex; align-items:center; gap:12px; padding:14px 16px 12px; background:var(--brown-dark); box-shadow:0 3px 16px rgba(62,31,0,0.3); position:sticky; top:0; z-index:10; }
.dm-header a.back-btn { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; font-size:15px; flex-shrink:0; transition:background .2s; }
.dm-header a.back-btn:hover { background:rgba(200,151,58,0.3); color:var(--gold); }
.dm-header h2 { font-family:'Lora',serif; font-size:17px; font-weight:700; color:var(--gold); flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dm-header .cart-btn { position:relative; width:38px; height:38px; border-radius:50%; background:var(--gold); color:var(--brown-dark); border:none; display:flex; align-items:center; justify-content:center; font-size:16px; cursor:pointer; flex-shrink:0; box-shadow:0 3px 12px rgba(200,151,58,0.4); }
.dm-header .cart-btn .cbadge { position:absolute; top:-3px; right:-3px; background:var(--brown-dark); color:var(--gold); font-size:9px; font-weight:900; width:17px; height:17px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid var(--gold); }
.dm-img { width:100%; max-height:240px; object-fit:cover; display:block; }
.dm-info { padding:16px 16px 8px; }
.dm-info h1 { font-family:'Lora',serif; font-size:22px; color:var(--text-dark); margin-bottom:6px; }
.dm-info .dm-harga { font-size:22px; font-weight:900; color:var(--brown-warm); margin-bottom:10px; }
.dm-info p { font-size:13px; color:var(--text-muted); line-height:1.6; }
.dm-form-card { margin:0 14px; background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:18px; border:1.5px solid var(--cream-dark); }
.dm-form-card .field-label { font-size:11px; font-weight:800; color:var(--text-muted); margin-bottom:7px; display:block; text-transform:uppercase; letter-spacing:.5px; }
.dm-form-card input[type=number] { width:100%; padding:12px 14px; border:2px solid var(--cream-dark); border-radius:12px; font-family:'Nunito',sans-serif; font-size:16px; font-weight:700; outline:none; transition:border .2s; background:var(--cream); color:var(--text-dark); }
.dm-form-card input[type=number]:focus { border-color:var(--brown-light); background:#fff; }
.dm-subtotal { background:linear-gradient(135deg,var(--brown-warm),var(--brown-mid)); border-radius:12px; padding:14px 16px; margin-top:14px; display:flex; align-items:center; justify-content:space-between; color:#fff; }
.dm-subtotal span:first-child { font-size:13px; opacity:.85; }
.dm-subtotal span:last-child  { font-size:20px; font-weight:900; }
.catatan-field { margin-top:14px; }
.catatan-field .field-label { font-size:11px; font-weight:800; color:var(--text-muted); margin-bottom:7px; display:block; text-transform:uppercase; letter-spacing:.5px; }
.catatan-field textarea { width:100%; padding:11px 14px; border:2px solid var(--cream-dark); border-radius:12px; font-family:'Nunito',sans-serif; font-size:13px; outline:none; resize:none; transition:border .2s; background:var(--cream); color:var(--text-dark); line-height:1.5; }
.catatan-field textarea:focus { border-color:var(--brown-light); background:#fff; }
.catatan-field textarea::placeholder { color:var(--text-muted); }
.catatan-hint { font-size:11px; color:var(--text-muted); margin-top:5px; font-style:italic; }
.btn-tambah-keranjang { display:block; width:calc(100% - 28px); margin:14px 14px 0; padding:15px; border:none; border-radius:var(--radius); background:linear-gradient(135deg,var(--brown-warm),var(--brown-dark)); color:#fff; font-family:'Nunito',sans-serif; font-size:15px; font-weight:900; cursor:pointer; box-shadow:var(--shadow-lg); transition:opacity .2s,transform .15s; }
.btn-tambah-keranjang:hover { opacity:.9; transform:scale(1.01); }
.keranjang-modal-overlay { display:none; position:fixed; inset:0; z-index:500; background:rgba(62,31,0,0.55); align-items:flex-end; }
.keranjang-modal-overlay.open { display:flex; }
.keranjang-modal { background:#fff; border-radius:24px 24px 0 0; width:100%; max-height:80vh; overflow-y:auto; padding:20px; animation:slideUp .3s ease; border-top:3px solid var(--gold); }
@keyframes slideUp { from{transform:translateY(100%);}to{transform:translateY(0);} }
.km-handle { width:40px; height:4px; background:var(--cream-dark); border-radius:2px; margin:0 auto 16px; }
.km-title { font-family:'Lora',serif; font-size:18px; font-weight:700; color:var(--brown-dark); margin-bottom:14px; }
.km-item { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--cream-dark); }
.km-item .km-icon { width:42px; height:42px; border-radius:10px; background:var(--cream); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.km-item .km-detail { flex:1; }
.km-item .km-detail h6 { font-size:13px; font-weight:800; color:var(--text-dark); margin-bottom:2px; }
.km-item .km-detail span { font-size:11px; color:var(--text-muted); }
.km-item .km-price { font-size:13px; font-weight:900; color:var(--brown-warm); }
.km-item .km-del { width:28px; height:28px; border-radius:50%; background:#fff0e6; color:var(--brown-warm); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:12px; flex-shrink:0; }
.km-item .km-del:hover { background:var(--brown-warm); color:#fff; }
.km-total { background:var(--cream); border-radius:12px; padding:13px 16px; margin:14px 0; display:flex; justify-content:space-between; align-items:center; border:1.5px solid var(--cream-dark); }
.km-total span:first-child { font-size:13px; color:var(--text-muted); font-weight:700; }
.km-total span:last-child  { font-size:19px; font-weight:900; color:var(--brown-warm); }
.km-empty { text-align:center; padding:40px 0; color:var(--text-muted); font-weight:700; }
.km-empty i { font-size:40px; display:block; margin-bottom:10px; opacity:.25; }
</style>

<!-- Keranjang Modal -->
<div class="keranjang-modal-overlay" id="keranjangOverlay">
    <div class="keranjang-modal">
        <div class="km-handle"></div>
        <div class="km-title">🛒 Keranjang Kamu</div>
        <?php if (count($data) > 0): ?>
            <?php foreach ($data as $datas): ?>
            <div class="km-item">
                <div class="km-icon">🍽️</div>
                <div class="km-detail">
                    <h6><?= htmlspecialchars($datas['name_menu'] ?? $datas['menu_kd']) ?></h6>
                    <span><?= $datas['total'] ?> pcs &nbsp;·&nbsp;
                    <?php
                    $sd = $datas['status_detail'];
                    $badges = ['pending'=>'⏳','dimasak'=>'🔥','siap'=>'✅','diambil'=>'📦'];
                    echo ($badges[$sd] ?? '') . ' ' . $sd;
                    ?></span>
                </div>
                <div class="km-price">Rp <?= number_format($datas['sub_total'], 0, ',', '.') ?></div>
                <button type="button" class="km-del" onclick="hapusItem('<?= $datas['kd_detail'] ?>')">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <?php endforeach; ?>
            <div class="km-total">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($assoc['sub'] ?? 0, 0, ',', '.') ?></span>
            </div>
            <a href="?page=transaksi" style="display:block;text-align:center;padding:13px;background:var(--brown-dark);color:var(--gold);border-radius:12px;font-weight:900;font-size:14px;text-decoration:none;margin-top:4px;">
                Lihat Keranjang Lengkap →
            </a>
        <?php else: ?>
            <div class="km-empty">
                <i class="fa fa-shopping-basket"></i>
                Keranjang masih kosong
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Page Content -->
<div class="dm-wrap">
    <div class="dm-header">
        <a href="<?= $backUrl ?>" class="back-btn">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h2><?= htmlspecialchars($getMenu['name_menu'] ?? '') ?></h2>
        <button class="cart-btn" id="openKeranjang">
            <i class="fa fa-shopping-basket"></i>
            <?php if ($jumlahKeranjang > 0): ?>
            <span class="cbadge"><?= $jumlahKeranjang ?></span>
            <?php endif; ?>
        </button>
    </div>

    <img class="dm-img"
         src="img/<?= htmlspecialchars($getMenu['photo'] ?? '') ?>"
         alt="<?= htmlspecialchars($getMenu['name_menu'] ?? '') ?>"
         onerror="this.src='images/icon/logo-blue.png'">

    <div class="dm-info">
        <h1><?= htmlspecialchars($getMenu['name_menu'] ?? '') ?></h1>
        <div class="dm-harga">Rp <?= number_format($getMenu['harga'] ?? 0, 0, ',', '.') ?></div>
        <p><?= htmlspecialchars($getMenu['description'] ?? '') ?></p>
    </div>

    <form method="post">
        <div class="dm-form-card">
            <label class="field-label">Jumlah</label>
            <input type="number" id="jumjum" name="total" min="1" value="1" placeholder="1">
            <input type="hidden" name="harga"     id="hargas" value="<?= $getMenu['harga'] ?? 0 ?>">
            <input type="hidden" name="sub_total" id="totals" value="<?= $getMenu['harga'] ?? 0 ?>">
            <div class="dm-subtotal">
                <span>Sub Total</span>
                <span id="subtotalShow">Rp <?= number_format($getMenu['harga'] ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="catatan-field">
                <label class="field-label">📝 Catatan untuk dapur (opsional)</label>
                <textarea name="keterangan" rows="3"
                    placeholder="Contoh: tidak pedes, tanpa mentimun, nasi dipisah..."><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                <div class="catatan-hint">Catatan tersimpan otomatis saat tambah ke keranjang</div>
            </div>
        </div>
        <button type="submit" name="btnTambah" class="btn-tambah-keranjang">
            <i class="fa fa-shopping-basket"></i> Tambah ke Keranjang
        </button>
    </form>
</div>

<script>
var harga = <?= (int)($getMenu['harga'] ?? 0) ?>;
document.getElementById('jumjum').addEventListener('input', function() {
    var jumlah = parseInt(this.value) || 0;
    var sub    = harga * jumlah;
    document.getElementById('totals').value = sub;
    document.getElementById('subtotalShow').textContent = 'Rp ' + sub.toLocaleString('id-ID');
});
document.getElementById('openKeranjang').addEventListener('click', function() {
    document.getElementById('keranjangOverlay').classList.add('open');
});
document.getElementById('keranjangOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
function hapusItem(kdDetail) {
    if (confirm('Yakin hapus item ini dari keranjang?')) {
        window.location.href = '?page=detail_menu&hapus2&kd=' + kdDetail + '&menu=<?= $kd ?>&kategori=<?= $kate ?>&from=<?= $from ?>';
    }
}
</script>