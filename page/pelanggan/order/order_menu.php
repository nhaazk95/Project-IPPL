<?php
    $mn      = new Resto();
    $table   = "tb_menu";
    $data    = $mn->edit($table, "kategori_id", $_GET['kd']);
    $getName = $mn->selectWhere("tb_kategori", "kd_kategori", $_GET['kd']);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap');
:root {
    --primary: #FF6B35;
    --primary-dk: #e85520;
    --bg: #F7F5F2;
    --card-bg: #FFFFFF;
    --text-main: #1a1a1a;
    --text-muted: #888;
    --radius: 18px;
    --shadow: 0 4px 24px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 40px rgba(255,107,53,0.18);
}
.om-wrap { padding: 16px 16px 0; font-family: 'Nunito', sans-serif; }
.om-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.om-header a {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: var(--card-bg);
    display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow);
    color: var(--text-main);
    text-decoration: none;
    font-size: 16px;
    flex-shrink: 0;
    transition: background .2s;
}
.om-header a:hover { background: #ffe8e0; color: var(--primary); }
.om-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    flex: 1;
}
.om-search {
    position: relative;
    margin-bottom: 20px;
}
.om-search input {
    width: 100%;
    padding: 12px 20px 12px 44px;
    border-radius: 50px;
    border: 2px solid #eee;
    background: var(--card-bg);
    font-family: 'Nunito', sans-serif;
    font-size: 14px;
    outline: none;
    transition: border .2s;
}
.om-search input:focus { border-color: var(--primary); }
.om-search .si {
    position: absolute;
    left: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 16px;
}
.om-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    padding-bottom: 24px;
}
.om-card {
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: var(--shadow);
    text-decoration: none;
    color: var(--text-main);
    display: block;
    transition: transform .2s, box-shadow .2s;
    animation: fadeUp .4s ease both;
}
.om-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    text-decoration: none;
    color: var(--text-main);
}
.om-card img {
    width: 100%; height: 120px;
    object-fit: cover; display: block;
}
.om-card .om-body { padding: 10px 12px 6px; }
.om-card .om-body h5 {
    font-size: 13px; font-weight: 800;
    margin-bottom: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.om-card .om-body .harga {
    font-size: 13px; font-weight: 900;
    color: var(--primary);
}
.om-card .om-footer { padding: 8px 12px 12px; }
.om-card .btn-order {
    display: block; text-align: center;
    background: var(--primary); color: #fff;
    font-size: 12px; font-weight: 800;
    padding: 8px; border-radius: 10px;
    text-decoration: none;
    transition: background .2s;
}
.om-card .btn-order:hover { background: var(--primary-dk); }
.om-empty {
    text-align: center; padding: 60px 20px;
    color: var(--text-muted); font-size: 15px; font-weight: 700;
}
.om-empty i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .3; }
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<div class="om-wrap">
    <!-- Header -->
    <div class="om-header">
        <a href="?page=dashboard"><i class="fa fa-arrow-left"></i></a>
        <h2>Menu <?= htmlspecialchars($getName['name_kategori'] ?? '') ?></h2>
    </div>

    <!-- Search -->
    <div class="om-search">
        <i class="fa fa-search si"></i>
        <input type="text" id="omSearch" placeholder="Cari menu...">
    </div>

    <!-- Grid Menu -->
    <?php if (count($data) > 0): ?>
    <div class="om-grid" id="omGrid">
        <?php foreach ($data as $i => $dataB): ?>
        <a href="?page=detail_menu&kategori=<?= $dataB['kategori_id'] ?>&kd=<?= $dataB['kd_menu'] ?>"
           class="om-card"
           data-name="<?= strtolower(htmlspecialchars($dataB['name_menu'])) ?>"
           style="animation-delay: <?= $i * 0.07 ?>s">
            <img src="img/<?= htmlspecialchars($dataB['photo']) ?>"
                 alt="<?= htmlspecialchars($dataB['name_menu']) ?>"
                 onerror="this.src='images/icon/logo-blue.png'">
            <div class="om-body">
                <h5><?= htmlspecialchars($dataB['name_menu']) ?></h5>
                <div class="harga">Rp <?= number_format($dataB['harga'], 0, ',', '.') ?></div>
            </div>
            <div class="om-footer">
                <span class="btn-order"><i class="fa fa-plus"></i> Order</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="om-empty">
        <i class="fa fa-cutlery"></i>
        Belum ada menu untuk kategori ini
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('omSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#omGrid .om-card').forEach(function(el) {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>