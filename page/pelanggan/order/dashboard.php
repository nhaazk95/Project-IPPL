<?php
    global $con;
    $db    = new Resto();
    $table = "tb_kategori";
    $data  = $db->select($table);

    // ── Best Seller: menu paling banyak dipesan ──
    $sqlBS = "SELECT m.*, SUM(d.total) as total_terjual
              FROM tb_menu m
              JOIN tb_detail_order d ON m.kd_menu = d.menu_kd
              WHERE m.status = 'tersedia'
              GROUP BY m.kd_menu
              ORDER BY total_terjual DESC
              LIMIT 6";
    $exeBS      = mysqli_query($con, $sqlBS);
    $bestSeller = [];
    if ($exeBS) while ($row = mysqli_fetch_assoc($exeBS)) $bestSeller[] = $row;

    if (count($bestSeller) == 0) {
        $exeF = mysqli_query($con, "SELECT * FROM tb_menu WHERE status='tersedia' LIMIT 6");
        if ($exeF) while ($row = mysqli_fetch_assoc($exeF)) $bestSeller[] = $row;
    }
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;600;700;800;900&display=swap');
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
    --radius-sm:   10px;
    --radius-md:   16px;
    --radius-lg:   22px;
    --shadow-sm:   0 2px 12px rgba(62,31,0,0.08);
    --shadow-md:   0 6px 24px rgba(62,31,0,0.13);
    --shadow-lg:   0 12px 40px rgba(62,31,0,0.2);
}
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Nunito',sans-serif; background:var(--cream); color:var(--text-dark); }
.pg-topbar { position:sticky; top:0; z-index:100; background:var(--brown-dark); padding:12px 18px; display:flex; align-items:center; gap:10px; box-shadow:0 3px 20px rgba(62,31,0,0.4); }
.pg-topbar .app-name { font-family:'Lora',serif; font-size:19px; font-weight:700; color:var(--gold); flex:1; letter-spacing:.3px; }
.topbar-btn { width:36px; height:36px; border-radius:50%; border:none; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; color:var(--cream); text-decoration:none; position:relative; transition:background .2s; }
.topbar-btn:hover { background:rgba(200,151,58,0.3); color:var(--gold); }
.cart-badge { position:absolute; top:-3px; right:-3px; background:var(--gold); color:var(--brown-dark); font-size:9px; font-weight:900; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid var(--brown-dark); }
.pg-hero { margin:14px 14px 0; border-radius:var(--radius-lg); overflow:hidden; position:relative; height:170px; background:linear-gradient(135deg,rgba(62,31,0,.8),rgba(123,63,0,.55)),url('images/bg3.jpg') center/cover no-repeat; display:flex; flex-direction:column; justify-content:flex-end; padding:18px 20px; }
.pg-hero::before { content:''; position:absolute; inset:0; background:linear-gradient(to top,rgba(62,31,0,.85) 0%,transparent 65%); }
.pg-hero h1 { font-family:'Lora',serif; font-size:23px; color:#fff; line-height:1.3; position:relative; z-index:1; }
.pg-hero p { font-size:12px; color:rgba(255,255,255,.75); margin-top:4px; position:relative; z-index:1; }
.meja-badge { position:absolute; top:12px; right:12px; background:var(--gold); color:var(--brown-dark); font-size:11px; font-weight:900; padding:4px 12px; border-radius:20px; z-index:1; box-shadow:0 3px 12px rgba(200,151,58,0.45); }
.pg-search { margin:12px 14px 0; position:relative; }
.pg-search input { width:100%; padding:11px 16px 11px 40px; border-radius:50px; border:2px solid var(--cream-dark); background:#fff; font-family:'Nunito',sans-serif; font-size:13px; outline:none; color:var(--text-dark); transition:border .2s; box-shadow:var(--shadow-sm); }
.pg-search input:focus { border-color:var(--brown-light); }
.pg-search input::placeholder { color:var(--text-muted); }
.pg-search .si { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:14px; }
.greeting-strip { margin:12px 14px 0; background:linear-gradient(135deg,var(--brown-warm),var(--brown-mid)); border-radius:var(--radius-md); padding:13px 16px; display:flex; align-items:center; gap:12px; box-shadow:var(--shadow-md); }
.greeting-strip .g-icon { font-size:24px; flex-shrink:0; }
.greeting-strip h5 { font-size:14px; font-weight:800; color:#fff; margin-bottom:2px; }
.greeting-strip p  { font-size:11px; color:rgba(255,255,255,.75); }
.sec-title { padding:18px 14px 10px; display:flex; align-items:center; gap:10px; }
.sec-title h2 { font-family:'Lora',serif; font-size:17px; font-weight:700; color:var(--brown-dark); white-space:nowrap; }
.sec-title .divider { height:1.5px; flex:1; background:linear-gradient(to right,var(--cream-dark),transparent); border-radius:2px; }
.bs-scroll { display:flex; gap:10px; overflow-x:auto; padding:0 14px 10px; scrollbar-width:none; }
.bs-scroll::-webkit-scrollbar { display:none; }
.bs-card { flex:0 0 130px; border-radius:var(--radius-md); overflow:hidden; background:#fff; box-shadow:var(--shadow-sm); border:1.5px solid var(--cream-dark); display:flex; flex-direction:column; transition:transform .2s,box-shadow .2s; animation:fadeUp .4s ease both; text-decoration:none; color:var(--text-dark); }
.bs-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); border-color:var(--brown-light); text-decoration:none; color:var(--text-dark); }
.bs-card img { width:100%; height:90px; object-fit:cover; display:block; }
.bs-ribbon { background:var(--gold); color:var(--brown-dark); font-size:9px; font-weight:900; text-align:center; padding:3px 0; letter-spacing:.5px; }
.bs-body { padding:8px 9px 4px; flex:1; }
.bs-body h6 { font-size:12px; font-weight:800; color:var(--text-dark); margin-bottom:4px; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.bs-body .bs-harga { font-size:12px; font-weight:900; color:var(--brown-warm); }
.bs-footer { padding:6px 9px 9px; }
.bs-btn { display:block; width:100%; padding:7px; border:none; border-radius:9px; background:var(--brown-dark); color:var(--gold); font-family:'Nunito',sans-serif; font-size:11px; font-weight:900; text-align:center; transition:background .2s; text-decoration:none; }
.bs-btn:hover { background:var(--brown-mid); color:var(--gold); text-decoration:none; }
.ornament { text-align:center; padding:6px 0 0; font-size:13px; color:var(--brown-light); letter-spacing:8px; opacity:.5; }
.kat-scroll { display:flex; gap:12px; overflow-x:auto; padding:0 14px 90px; scrollbar-width:none; }
.kat-scroll::-webkit-scrollbar { display:none; }
.kat-chip { flex:0 0 140px; border-radius:var(--radius-md); overflow:hidden; background:#fff; box-shadow:var(--shadow-sm); text-decoration:none; color:var(--text-dark); border:1.5px solid var(--cream-dark); transition:transform .2s,box-shadow .2s; animation:fadeUp .4s ease both; display:block; }
.kat-chip:hover { transform:translateY(-4px); box-shadow:var(--shadow-md); color:var(--text-dark); text-decoration:none; border-color:var(--brown-light); }
.kat-chip img { width:100%; height:100px; object-fit:cover; display:block; }
.kat-chip .chip-body { padding:8px 10px 10px; }
.kat-chip .chip-body h6 { font-size:12px; font-weight:800; color:var(--text-dark); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:6px; }
.kat-chip .chip-btn { display:block; text-align:center; background:var(--brown-dark); color:var(--gold); font-size:11px; font-weight:800; padding:6px; border-radius:8px; text-decoration:none; transition:background .2s; }
.kat-chip .chip-btn:hover { background:var(--brown-mid); }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }
</style>

<!-- TOPBAR -->
<div class="pg-topbar">
    <span class="app-name">🍛 Dapur Nusantara</span>
    <a href="?page=transaksi" class="topbar-btn">
        <i class="fa fa-shopping-basket"></i>
        <span class="cart-badge"><?= $num3 ?? 0 ?></span>
    </a>
</div>

<!-- HERO -->
<div class="pg-hero">
    <span class="meja-badge">🪑 Meja <?= $auth2['no_meja'] ?? '-' ?></span>
    <h1>Selamat Datang 👋</h1>
    <p>Cita rasa nusantara, langsung antar ke mejamu</p>
</div>

<!-- SEARCH -->
<div class="pg-search">
    <i class="fa fa-search si"></i>
    <input type="text" id="searchInput" placeholder="Cari menu atau kategori...">
</div>

<!-- GREETING -->
<div class="greeting-strip">
    <div class="g-icon">🍽️</div>
    <div>
        <h5>Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Pelanggan') ?>!</h5>
        <p>Mau makan apa hari ini?</p>
    </div>
</div>

<!-- BEST SELLER -->
<?php if (count($bestSeller) > 0): ?>
<div class="sec-title">
    <h2>Best Seller ⭐</h2>
    <div class="divider"></div>
</div>
<div class="bs-scroll">
    <?php foreach ($bestSeller as $i => $bs): ?>
    <!-- FIX: tambah &from=dashboard agar tombol back di detail_menu ke halaman awal -->
    <a href="?page=detail_menu&kategori=<?= htmlspecialchars($bs['kategori_id']) ?>&kd=<?= htmlspecialchars($bs['kd_menu']) ?>&from=dashboard"
       class="bs-card" style="animation-delay:<?= $i * 0.07 ?>s">
        <img src="img/<?= htmlspecialchars($bs['photo']) ?>"
             alt="<?= htmlspecialchars($bs['name_menu']) ?>"
             onerror="this.src='images/icon/logo-blue.png'">
        <div class="bs-ribbon">★ BEST SELLER</div>
        <div class="bs-body">
            <h6><?= htmlspecialchars($bs['name_menu']) ?></h6>
            <div class="bs-harga">Rp <?= number_format($bs['harga'], 0, ',', '.') ?></div>
        </div>
        <div class="bs-footer">
            <span class="bs-btn">Pesan →</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<div class="ornament">✦ ✦ ✦</div>
<?php endif; ?>

<!-- SEMUA KATEGORI -->
<div class="sec-title">
    <h2>Semua Kategori</h2>
    <div class="divider"></div>
</div>
<div class="kat-scroll">
    <?php foreach ($data as $i => $d): ?>
    <a href="?page=order_menu&kategori&menu&kd=<?= $d['kd_kategori'] ?>"
       class="kat-chip" style="animation-delay:<?= $i * 0.07 ?>s">
        <img src="img/<?= htmlspecialchars($d['photo']) ?>"
             alt="<?= htmlspecialchars($d['name_kategori']) ?>"
             onerror="this.src='images/icon/logo-blue.png'">
        <div class="chip-body">
            <h6><?= htmlspecialchars($d['name_kategori']) ?></h6>
            <span class="chip-btn">Lihat Menu →</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>