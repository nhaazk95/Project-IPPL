<?php
    $db    = new Resto();
    $table = "tb_kategori";
    $data  = $db->select($table);
    // FIX: Pakai $auth2 dari pagePelanggan.php, tidak perlu query ulang
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap');

:root {
    --primary:    #FF6B35;
    --primary-dk: #e85520;
    --secondary:  #2D2D2D;
    --bg:         #F7F5F2;
    --card-bg:    #FFFFFF;
    --text-main:  #1a1a1a;
    --text-muted: #888;
    --radius:     18px;
    --shadow:     0 4px 24px rgba(0,0,0,0.08);
    --shadow-lg:  0 8px 40px rgba(255,107,53,0.18);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    color: var(--text-main);
}

/* ── TOP SEARCH BAR ── */
.pg-topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--card-bg);
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.pg-topbar .app-name {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--primary);
    flex: 1;
}
.pg-topbar .search-btn,
.pg-topbar .cart-btn {
    width: 40px; height: 40px;
    border-radius: 50%;
    border: none;
    background: var(--bg);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: var(--secondary);
    text-decoration: none;
    position: relative;
    transition: background .2s;
}
.pg-topbar .cart-btn:hover,
.pg-topbar .search-btn:hover { background: #ffe8e0; color: var(--primary); }
.cart-badge {
    position: absolute;
    top: -3px; right: -3px;
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    width: 18px; height: 18px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
}

/* ── HERO BANNER ── */
.pg-hero {
    margin: 16px 16px 0;
    border-radius: var(--radius);
    overflow: hidden;
    position: relative;
    height: 180px;
    background: linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.3)),
                url('images/bg3.jpg') center/cover no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 20px;
}
.pg-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    color: #fff;
    line-height: 1.2;
}
.pg-hero p {
    font-size: 13px;
    color: rgba(255,255,255,.8);
    margin-top: 4px;
}
.pg-hero .meja-badge {
    position: absolute;
    top: 14px; right: 14px;
    background: var(--primary);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: 30px;
    letter-spacing: .5px;
    box-shadow: var(--shadow-lg);
}

/* ── SEARCH BAR (inline) ── */
.pg-search {
    margin: 16px 16px 0;
    position: relative;
}
.pg-search input {
    width: 100%;
    padding: 13px 20px 13px 46px;
    border-radius: 50px;
    border: 2px solid #eee;
    background: var(--card-bg);
    font-family: 'Nunito', sans-serif;
    font-size: 14px;
    outline: none;
    transition: border .2s;
}
.pg-search input:focus { border-color: var(--primary); }
.pg-search .search-icon {
    position: absolute;
    left: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 17px;
}

/* ── SECTION TITLE ── */
.pg-section-title {
    padding: 22px 20px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.pg-section-title h2 {
    font-size: 20px;
    font-weight: 900;
    color: var(--text-main);
}
.pg-section-title a {
    font-size: 13px;
    color: var(--primary);
    font-weight: 700;
    text-decoration: none;
}

/* ── KATEGORI HORIZONTAL SCROLL ── */
.kategori-scroll {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 0 16px 10px;
    scrollbar-width: none;
}
.kategori-scroll::-webkit-scrollbar { display: none; }

.kat-card {
    flex: 0 0 140px;
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: var(--shadow);
    text-decoration: none;
    color: var(--text-main);
    transition: transform .2s, box-shadow .2s;
    display: block;
}
.kat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    color: var(--text-main);
    text-decoration: none;
}
.kat-card img {
    width: 100%;
    height: 90px;
    object-fit: cover;
    display: block;
}
.kat-card .kat-info {
    padding: 10px 10px 12px;
}
.kat-card .kat-info h6 {
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kat-card .kat-info p {
    font-size: 11px;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kat-card .kat-footer {
    padding: 0 10px 12px;
}
.kat-card .btn-lihat {
    display: block;
    text-align: center;
    background: var(--primary);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    padding: 7px;
    border-radius: 10px;
    text-decoration: none;
    transition: background .2s;
}
.kat-card .btn-lihat:hover { background: var(--primary-dk); }

/* ── SEMUA KATEGORI GRID ── */
.all-kat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    padding: 0 16px 24px;
}
.all-kat-item {
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: var(--shadow);
    text-decoration: none;
    color: var(--text-main);
    transition: transform .2s, box-shadow .2s;
}
.all-kat-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    text-decoration: none;
    color: var(--text-main);
}
.all-kat-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}
.all-kat-item .kat-body {
    padding: 12px;
}
.all-kat-item .kat-body h5 {
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 4px;
}
.all-kat-item .kat-body p {
    font-size: 12px;
    color: var(--text-muted);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.all-kat-item .kat-action {
    padding: 0 12px 14px;
}
.all-kat-item .btn-lihat2 {
    display: block;
    text-align: center;
    background: var(--primary);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    padding: 8px;
    border-radius: 10px;
    text-decoration: none;
    transition: background .2s;
}
.all-kat-item .btn-lihat2:hover { background: var(--primary-dk); }

/* ── INFO STRIP ── */
.info-strip {
    margin: 0 16px 16px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dk));
    border-radius: var(--radius);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    color: #fff;
    box-shadow: var(--shadow-lg);
}
.info-strip .info-icon {
    font-size: 28px;
    flex-shrink: 0;
}
.info-strip h5 {
    font-size: 15px;
    font-weight: 800;
    margin-bottom: 2px;
}
.info-strip p {
    font-size: 12px;
    opacity: .85;
}

/* ── SKELETON ANIMATION ── */
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.skeleton {
    background: linear-gradient(90deg, #eee 25%, #f5f5f5 50%, #eee 75%);
    background-size: 400px 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 8px;
}

/* ── FADE IN ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.kat-card, .all-kat-item {
    animation: fadeUp .4s ease both;
}
</style>

<!-- TOP BAR -->
<div class="pg-topbar">
    <span class="app-name">Food</span>
    <button class="search-btn" onclick="document.querySelector('.pg-search input').focus()">
        <i class="fa fa-search"></i>
    </button>
    <a href="?page=transaksi" class="cart-btn">
        <i class="fa fa-shopping-basket"></i>
        <span class="cart-badge" id="cartCount">0</span>
    </a>
</div>

<!-- HERO -->
<div class="pg-hero">
    <span class="meja-badge">
        <i class="fa fa-cutlery"></i>
        Meja <?= $auth2['no_meja'] ?? '-' ?>
    </span>
    <h1>Selamat Datang 👋</h1>
    <p>Pesan makanan favoritmu sekarang!</p>
</div>

<!-- SEARCH -->
<div class="pg-search">
    <i class="fa fa-search search-icon"></i>
    <input type="text" id="searchInput" placeholder="Cari kategori makanan...">
</div>

<!-- INFO STRIP -->
<div class="info-strip" style="margin-top:16px;">
    <div class="info-icon">🍽️</div>
    <div>
        <h5>Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Pelanggan') ?>!</h5>
        <p>Pilih kategori di bawah untuk mulai memesan</p>
    </div>
</div>

<!-- KATEGORI HORIZONTAL (Best Seller style) -->
<div class="pg-section-title">
    <h2>🔥 Kategori</h2>
</div>

<div class="kategori-scroll" id="kategoriScroll">
    <?php foreach ($data as $i => $data2): ?>
    <a href="?page=order_menu&kategori&menu&kd=<?= $data2['kd_kategori'] ?>"
       class="kat-card"
       style="animation-delay: <?= $i * 0.07 ?>s">
        <img src="img/<?= htmlspecialchars($data2['photo']) ?>"
             alt="<?= htmlspecialchars($data2['name_kategori']) ?>"
             onerror="this.src='images/icon/logo-blue.png'">
        <div class="kat-info">
            <h6><?= htmlspecialchars($data2['name_kategori']) ?></h6>
            <p><?= htmlspecialchars($data2['description']) ?></p>
        </div>
        <div class="kat-footer">
            <span class="btn-lihat">Lihat Menu</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- SEMUA KATEGORI GRID -->
<div class="pg-section-title">
    <h2>🗂️ Semua Kategori</h2>
</div>

<div class="all-kat-grid" id="allKatGrid">
    <?php foreach ($data as $i => $data2): ?>
    <a href="?page=order_menu&kategori&menu&kd=<?= $data2['kd_kategori'] ?>"
       class="all-kat-item"
       data-name="<?= strtolower(htmlspecialchars($data2['name_kategori'])) ?>"
       style="animation-delay: <?= $i * 0.08 ?>s">
        <img src="img/<?= htmlspecialchars($data2['photo']) ?>"
             alt="<?= htmlspecialchars($data2['name_kategori']) ?>"
             onerror="this.src='images/icon/logo-blue.png'">
        <div class="kat-body">
            <h5><?= htmlspecialchars($data2['name_kategori']) ?></h5>
            <p><?= htmlspecialchars($data2['description']) ?></p>
        </div>
        <div class="kat-action">
            <span class="btn-lihat2">
                <i class="fa fa-shopping-basket"></i> Tambah ke Keranjang
            </span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<script>
// Search filter
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#allKatGrid .all-kat-item').forEach(function(el) {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});

// Cart badge count (ambil dari session/localStorage jika ada)
// Ini placeholder — bisa dihubungkan ke data order sungguhan
const cartCount = <?= $num3 ?? 0 ?>;
document.getElementById('cartCount').textContent = cartCount > 0 ? cartCount : '0';
</script>