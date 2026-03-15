<?php
    $dm                 = new Resto();
    $getMenu            = $dm->selectWhere("tb_menu", "kd_menu", $_GET['kd']);
    $kd                 = $_GET['kd'];
    @$getKategori       = $dm->selectWhere("tb_kategori", "kd_kategori", $_GET['kategori']);
    @$kate              = $_GET['kategori'];
    $autokodedetail     = $dm->autokode("tb_detail_order", "kd_detail", "DM");
    $autokodedetailTemp = $dm->autokode("tb_detail_order_temporary", "kd_detail", "DM");
    $authPelanggan      = $dm->AuthPelanggan($_SESSION['username']);
    $authUser           = $dm->AuthUser($_SESSION['username']);
    $no_meja2           = $authPelanggan['no_meja'];

    // Ambil kd_order berdasarkan no_meja
    $sql2    = "SELECT kd_order FROM tb_order WHERE no_meja='$no_meja2'";
    $exe2    = mysqli_query($con, $sql2);
    $dta2    = mysqli_fetch_assoc($exe2);
    $data_kd = $dta2['kd_order'] ?? null;

    $kduser = $authUser['kd_user'] ?? null;

    // Ambil data keranjang
    $data = [];
    if ($data_kd) {
        $data = $dm->editWhere("tb_detail_order_temporary", 'order_kd', $data_kd, 'user_kd', $kduser);
    }

    // Sum total
    $assoc = ['sub' => 0];
    if ($data_kd) {
        $sql   = "SELECT SUM(sub_total) as sub FROM tb_detail_order_temporary WHERE order_kd = '$data_kd'";
        $exec  = mysqli_query($con, $sql);
        $assoc = mysqli_fetch_assoc($exec);
    }

    // Jumlah item keranjang
    $jumlahKeranjang = count($data);

    // Tambah ke keranjang
    if (isset($_POST['btnTambah'])) {
        $nama_user     = $authUser['name'];
        $kd_user       = $authUser['kd_user'];
        $status_detail = "pending";
        $total         = (int)$_POST['total'];
        $menu_kd       = $getMenu['kd_menu'];
        $sub_total     = (int)$_POST['sub_total'];

        if ($total <= 0 || $sub_total <= 0) {
            $response = ['response' => 'negative', 'alert' => 'Isi jumlah dengan benar'];
        } elseif (!$data_kd) {
            $response = ['response' => 'negative', 'alert' => 'Sesi order tidak ditemukan, silakan login ulang'];
        } else {
            $sql = "SELECT * FROM tb_detail_order_temporary WHERE menu_kd='$menu_kd' AND order_kd='$data_kd'";
            $exe = mysqli_query($con, $sql);
            $num = mysqli_num_rows($exe);
            $dta = mysqli_fetch_assoc($exe);

            if ($num > 0) {
                $total     = $dta['total'] + $total;
                $sub_total = $dta['sub_total'] + $sub_total;
                $value     = "total='$total', sub_total='$sub_total'";
                $response  = $dm->update("tb_detail_order_temporary", $value, "menu_kd = '$menu_kd' AND order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd");
                $dm->update("tb_detail_order", $value, "menu_kd= '$menu_kd' AND order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd");
            } else {
                $valueDetailTemp = "'$autokodedetailTemp', '$data_kd', '$kd_user', '$menu_kd', '', '$total', '$sub_total', '', '', '', '$status_detail'";
                $response        = $dm->insert("tb_detail_order_temporary", $valueDetailTemp, "?page=detail_menu&kategori=$kate&kd=$kd");
                $valueDetail     = "'$autokodedetail', '$data_kd', '$kd_user', '$menu_kd', '', '$total', '$sub_total', '', '', '', '$status_detail'";
                $dm->insert("tb_detail_order", $valueDetail, "?page=detail_menu&kategori=$kate&kd=$kd");
                $valueUp  = "status_order='belum_bayar'";
                $dm->update("tb_order", $valueUp, "kd_order", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd");
            }
        }
    }

    // Kirim catatan
    if (isset($_POST['kirimCatatan'])) {
        $keterangan = $_POST['keterangan'];
        if ($keterangan == "") {
            $response = ['response' => 'negative', 'alert' => 'Lengkapi Field'];
        } else {
            $value    = "keterangan='$keterangan', status_keterangan='N'";
            $response = $dm->update("tb_detail_order_temporary", $value, "order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd");
            $dm->update("tb_detail_order", $value, "order_kd", $data_kd, "?page=detail_menu&kategori=$kate&kd=$kd");
        }
    }

    // Hapus item keranjang
    if (isset($_GET['hapus2'])) {
        $kdHapus  = $_GET['kd'];
        $response = $dm->delete("tb_detail_order_temporary", "kd_detail", $kdHapus, "?page=detail_menu&kategori=$kate&kd=" . $_GET['menu']);
    }
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap');
:root {
    --primary: #FF6B35; --primary-dk: #e85520;
    --bg: #F7F5F2; --card-bg: #fff;
    --text-main: #1a1a1a; --text-muted: #888;
    --radius: 18px;
    --shadow: 0 4px 24px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 40px rgba(255,107,53,0.18);
}
.dm-wrap { font-family: 'Nunito', sans-serif; padding-bottom: 20px; }
.dm-header {
    display: flex; align-items: center; gap: 12px;
    padding: 16px 16px 12px;
}
.dm-header a.back-btn {
    width: 38px; height: 38px; border-radius: 50%;
    background: var(--card-bg); display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow); color: var(--text-main); text-decoration: none;
    font-size: 16px; flex-shrink: 0; transition: background .2s;
}
.dm-header a.back-btn:hover { background: #ffe8e0; color: var(--primary); }
.dm-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 700; flex: 1;
}
.dm-header .cart-btn {
    position: relative; width: 42px; height: 42px; border-radius: 50%;
    background: var(--primary); color: #fff; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; cursor: pointer; box-shadow: var(--shadow-lg);
    flex-shrink: 0;
}
.dm-header .cart-btn .cbadge {
    position: absolute; top: -3px; right: -3px;
    background: #fff; color: var(--primary);
    font-size: 10px; font-weight: 900;
    width: 18px; height: 18px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--primary);
}
.dm-img { width: 100%; max-height: 240px; object-fit: cover; }
.dm-info { padding: 16px; }
.dm-info h1 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 6px; }
.dm-info .dm-harga { font-size: 22px; font-weight: 900; color: var(--primary); margin-bottom: 10px; }
.dm-info p { font-size: 14px; color: var(--text-muted); line-height: 1.6; }
.dm-form-card {
    margin: 0 16px;
    background: var(--card-bg); border-radius: var(--radius);
    box-shadow: var(--shadow); padding: 20px;
}
.dm-form-card label { font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block; }
.dm-form-card input[type=number] {
    width: 100%; padding: 12px 16px;
    border: 2px solid #eee; border-radius: 12px;
    font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 700;
    outline: none; transition: border .2s; background: var(--bg);
}
.dm-form-card input[type=number]:focus { border-color: var(--primary); background: #fff; }
.dm-subtotal {
    background: linear-gradient(135deg, var(--primary), var(--primary-dk));
    border-radius: 12px; padding: 14px 16px; margin-top: 14px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.dm-subtotal span:first-child { font-size: 13px; opacity: .85; }
.dm-subtotal span:last-child { font-size: 20px; font-weight: 900; }
.btn-tambah-keranjang {
    display: block; width: 100%; margin: 16px 16px 0;
    width: calc(100% - 32px);
    padding: 15px; border: none; border-radius: var(--radius);
    background: var(--primary); color: #fff;
    font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 900;
    cursor: pointer; box-shadow: var(--shadow-lg);
    transition: background .2s, transform .15s;
}
.btn-tambah-keranjang:hover { background: var(--primary-dk); transform: scale(1.02); }

/* Keranjang Modal */
.keranjang-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 500;
    background: rgba(0,0,0,.5); align-items: flex-end;
}
.keranjang-modal-overlay.open { display: flex; }
.keranjang-modal {
    background: var(--card-bg); border-radius: 24px 24px 0 0;
    width: 100%; max-height: 80vh; overflow-y: auto;
    padding: 20px; animation: slideUp .3s ease;
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to   { transform: translateY(0); }
}
.km-handle {
    width: 40px; height: 4px; background: #ddd; border-radius: 2px;
    margin: 0 auto 16px;
}
.km-title { font-size: 18px; font-weight: 900; margin-bottom: 16px; }
.km-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 0; border-bottom: 1px solid #f0f0f0;
}
.km-item .km-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--bg); display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.km-item .km-detail { flex: 1; }
.km-item .km-detail h6 { font-size: 14px; font-weight: 800; margin-bottom: 2px; }
.km-item .km-detail span { font-size: 12px; color: var(--text-muted); }
.km-item .km-price { font-size: 14px; font-weight: 900; color: var(--primary); }
.km-item .km-del {
    width: 30px; height: 30px; border-radius: 50%;
    background: #fff0ec; color: var(--primary); border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 13px; flex-shrink: 0;
}
.km-total {
    background: var(--bg); border-radius: 14px;
    padding: 14px 16px; margin: 16px 0;
    display: flex; justify-content: space-between; align-items: center;
}
.km-total span:first-child { font-size: 14px; color: var(--text-muted); font-weight: 700; }
.km-total span:last-child { font-size: 20px; font-weight: 900; color: var(--primary); }
.km-catatan textarea {
    width: 100%; padding: 12px 16px;
    border: 2px solid #eee; border-radius: 12px;
    font-family: 'Nunito', sans-serif; font-size: 13px;
    outline: none; resize: none; transition: border .2s;
}
.km-catatan textarea:focus { border-color: var(--primary); }
.km-catatan label { font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block; }
.btn-kirim-catatan {
    width: 100%; padding: 13px; border: none; border-radius: 12px;
    background: var(--secondary, #2D2D2D); color: #fff;
    font-family: 'Nunito', sans-serif; font-size: 14px; font-weight: 800;
    cursor: pointer; margin-top: 10px; transition: opacity .2s;
}
.btn-kirim-catatan:hover { opacity: .85; }
.km-empty { text-align: center; padding: 40px 0; color: var(--text-muted); font-weight: 700; }
.km-empty i { font-size: 40px; display: block; margin-bottom: 10px; opacity: .3; }
</style>

<!-- Keranjang Modal -->
<div class="keranjang-modal-overlay" id="keranjangOverlay">
    <div class="keranjang-modal">
        <div class="km-handle"></div>
        <div class="km-title">🛒 Keranjang Kamu</div>

        <?php if (count($data) > 0): ?>
            <form method="post">
            <?php
            $dk = null;
            foreach ($data as $datas):
                $dk = $datas;
            ?>
            <div class="km-item">
                <div class="km-icon">🍽️</div>
                <div class="km-detail">
                    <h6><?= htmlspecialchars($datas['name_menu'] ?? $datas['menu_kd']) ?></h6>
                    <span>
                        <?= $datas['total'] ?> pcs &nbsp;·&nbsp;
                        <?php
                        $sd = $datas['status_detail'];
                        $badges = ['pending'=>'⏳','dimasak'=>'🔥','siap'=>'✅','diambil'=>'📦'];
                        echo ($badges[$sd] ?? '') . ' ' . $sd;
                        ?>
                    </span>
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

            <div class="km-catatan">
                <label>📝 Catatan untuk dapur (opsional)</label>
                <textarea name="keterangan" rows="3"
                    placeholder="Contoh: Ayam tidak pedes, gado-gado tanpa mentimun"><?= htmlspecialchars($dk['keterangan'] ?? '') ?></textarea>
                <?php
                $st = $dk['status_keterangan'] ?? '';
                if ($st == "S"): ?>
                    <div style="color:green;font-size:12px;margin-top:6px;font-weight:700;">
                        <i class="fa fa-check-circle"></i> Catatan telah dikonfirmasi dapur
                    </div>
                <?php elseif ($st == "T"): ?>
                    <div style="color:red;font-size:12px;margin-top:6px;font-weight:700;">
                        <i class="fa fa-times-circle"></i> Catatan tidak bisa dipenuhi (bumbu habis)
                    </div>
                <?php endif; ?>
                <button type="submit" name="kirimCatatan" class="btn-kirim-catatan">
                    <i class="fa fa-paper-plane"></i> Kirim Catatan
                </button>
            </div>
            </form>

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
        <a href="?page=order_menu&kategori&menu&kd=<?= $getKategori['kd_kategori'] ?? '' ?>" class="back-btn">
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
            <label>Jumlah</label>
            <input type="number" id="jumjum" name="total" min="1" value="1" placeholder="1">

            <input type="hidden" name="harga" id="hargas" value="<?= $getMenu['harga'] ?? 0 ?>">
            <input type="hidden" name="sub_total" id="totals" value="<?= $getMenu['harga'] ?? 0 ?>">

            <div class="dm-subtotal">
                <span>Sub Total</span>
                <span id="subtotalShow">Rp <?= number_format($getMenu['harga'] ?? 0, 0, ',', '.') ?></span>
            </div>
        </div>

        <button type="submit" name="btnTambah" class="btn-tambah-keranjang">
            <i class="fa fa-shopping-basket"></i> Tambah ke Keranjang
        </button>
    </form>
</div>

<script>
// Hitung subtotal
var harga = <?= (int)($getMenu['harga'] ?? 0) ?>;
document.getElementById('jumjum').addEventListener('input', function() {
    var jumlah = parseInt(this.value) || 0;
    var sub = harga * jumlah;
    document.getElementById('totals').value = sub;
    document.getElementById('subtotalShow').textContent = 'Rp ' + sub.toLocaleString('id-ID');
});

// Buka keranjang
document.getElementById('openKeranjang').addEventListener('click', function() {
    document.getElementById('keranjangOverlay').classList.add('open');
});
// Tutup keranjang klik overlay
document.getElementById('keranjangOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});

// Hapus item
function hapusItem(kdDetail) {
    if (confirm('Yakin hapus item ini dari keranjang?')) {
        window.location.href = '?page=detail_menu&hapus2&kd=' + kdDetail + '&menu=<?= $kd ?>&kategori=<?= $kate ?>';
    }
}
</script>