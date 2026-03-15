<?php
    include "config/controller.php";
    $id = new Resto();
    session_start();

    if (isset($_GET['logout'])) {
        $id->logout2();
        exit();
    }

    $auth     = $id->AuthUser($_SESSION['username']);
    $auth2    = $id->AuthPelanggan($_SESSION['username']);
    $sessionStatus = $id->sessionCheck();

    if ($sessionStatus == "false") { header("Location:index.php"); exit(); }
    if (!$auth2) { session_destroy(); header("Location:index.php"); exit(); }
    if (!$auth)  { session_destroy(); header("Location:index.php"); exit(); }

    $no_meja  = $auth2['no_meja'];
    $sql2     = "SELECT kd_order FROM tb_order WHERE no_meja='$no_meja'";
    $exe2     = mysqli_query($con, $sql2);
    $dta2     = mysqli_fetch_assoc($exe2);
    $data_kd  = $dta2['kd_order'] ?? null;
    $data_kd2 = null;
    $data_kd3 = null;
    $num3     = 0;

    if ($data_kd) {
        $sql3     = "SELECT status_detail FROM tb_detail_order_temporary WHERE order_kd='$data_kd'";
        $exe3     = mysqli_query($con, $sql3);
        $num3     = mysqli_num_rows($exe3);
        $dta3     = mysqli_fetch_assoc($exe3);
        $data_kd2 = $dta3['status_detail'] ?? null;

        $sql4     = "SELECT status_order FROM tb_order WHERE kd_order='$data_kd'";
        $exe4     = mysqli_query($con, $sql4);
        $dta4     = mysqli_fetch_assoc($exe4);
        $data_kd3 = $dta4['status_order'] ?? null;
    }

    if (isset($_GET['delete'])) {
        if ($data_kd3 == "belum_beli") { ?>
            <script src="vendor/jquery-3.2.1.min.js"></script>
            <script src="js/sweetalert.min.js"></script>
            <script>
            $(document).ready(function(){
                swal({ title:"Tidak Order?", text:"Anda belum membeli apapun", type:"warning",
                    showCancelButton:true, confirmButtonText:"Ya, Tidak Beli", cancelButtonText:"Beli",
                    closeOnConfirm:false, closeOnCancel:true
                }, function(isConfirm){
                    if(isConfirm){ <?php $response=$id->delete("tb_order","kd_order",$_GET['kd'],"?page=dashboard"); $id->logout2(); ?> }
                    else { window.location.href="?"; }
                });
            });
            </script>
        <?php
        } elseif ($data_kd2 == "pending" || $data_kd2 == "dimasak") {
            $response = ['response'=>'negative','alert'=>'Pesanan anda belum sampai'];
        } elseif ($data_kd2 == "siap" || $data_kd2 == "diambil") {
            $response = $id->delete("tb_detail_order_temporary","order_kd",$_GET['kd'],"?page=dashboard");
            $id->logout2();
        } else {
            $response = ['response'=>'negative','alert'=>'Tidak ada pesanan aktif'];
        }
    }

    @$page = $_GET['page'];
    if (!$page) $page = 'dashboard';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pelanggan - Dapur Nusantara</title>
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="vendor/dropify/dist/css/dropify.css">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <style>
        :root {
            --brown-dark:  #3E1F00;
            --brown-mid:   #7B3F00;
            --brown-warm:  #A0522D;
            --gold:        #C8973A;
            --cream:       #FDF6EC;
            --cream-dark:  #EDE0CC;
        }

        body { background-color: var(--cream) !important; }
        .header-desktop4 { display: none !important; }
        .page-wrapper { padding-top: 0 !important; }
        .pg-page-content { padding-bottom: 100px; }
        .pg-copyright {
            text-align: center; padding: 10px 20px 20px;
            font-size: 12px; color: var(--brown-warm);
            font-family: 'Nunito', sans-serif; opacity: .6;
        }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 200;
            background: var(--brown-dark);
            border-top: 2px solid var(--gold);
            display: flex; justify-content: space-around; align-items: center;
            padding: 6px 0 12px;
            box-shadow: 0 -4px 20px rgba(62,31,0,0.3);
        }
        .nav-item {
            display: flex; flex-direction: column; align-items: center; gap: 3px;
            font-size: 10px; font-family: 'Nunito', sans-serif; font-weight: 700;
            color: rgba(255,255,255,0.4); text-decoration: none; cursor: pointer;
            background: none; border: none; padding: 2px 10px;
            transition: color .2s; min-width: 64px;
        }
        .nav-item:hover, .nav-item.active {
            color: var(--gold); text-decoration: none;
        }
        .nav-item svg { width: 24px; height: 24px; display: block; margin: 0 auto 2px; }
        .nav-item svg path { fill: rgba(255,255,255,0.4); transition: fill .2s; }
        .nav-item.active svg path { fill: var(--gold); }
        .nav-item:hover svg path { fill: var(--gold); }

        .cart-wrap { position: relative; display: inline-block; }
        .badge-cart {
            position: absolute; top: -4px; right: -8px;
            background: var(--gold); color: var(--brown-dark);
            font-size: 9px; font-weight: 900;
            width: 16px; height: 16px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--brown-dark);
        }

        /* ── FLOATING SELESAI ORDER ── */
        .btn-selesai {
            position: fixed; bottom: 75px; right: 16px; z-index: 150;
            background: linear-gradient(135deg, var(--brown-warm), var(--brown-dark));
            color: var(--gold); border: 1.5px solid var(--gold);
            border-radius: 50px; padding: 11px 18px;
            font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 13px;
            box-shadow: 0 6px 24px rgba(62,31,0,0.4); cursor: pointer;
            display: flex; align-items: center; gap: 7px;
            transition: transform .15s, box-shadow .15s;
        }
        .btn-selesai:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 32px rgba(62,31,0,0.5);
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="pg-page-content">
        <?php
        switch ($page) {
            case "order_menu":  include "page/pelanggan/order/order_menu.php";  break;
            case "detail_menu": include "page/pelanggan/order/detail_menu.php"; break;
            case "transaksi":   include "page/pelanggan/order/transaksi.php";   break;
            case "checkout":    include "page/pelanggan/order/checkout.php";    break;
            default:
                $page = "dashboard";
                include "page/pelanggan/order/dashboard.php";
                break;
        }
        ?>
        <div class="pg-copyright">Copyright © 2026 Dapur Nusantara. All rights reserved.</div>
    </div>

    <!-- Floating Selesai Order -->
    <?php if (!in_array($page, ['transaksi','checkout'])): ?>
    <button class="btn-selesai" id="btdelete">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/>
        </svg>
        Selesai Order
    </button>
    <?php endif; ?>

    <!-- ── BOTTOM NAVIGATION ── -->
    <nav class="bottom-nav">
        <!-- Beranda -->
        <a href="?page=dashboard" class="nav-item <?= ($page=='dashboard') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            Beranda
        </a>

        <!-- Menu -->
        <a href="?page=order_menu&kd=1" class="nav-item <?= in_array($page,['order_menu','detail_menu']) ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24">
                <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
            </svg>
            Menu
        </a>

        <!-- Keranjang -->
        <a href="?page=transaksi" class="nav-item <?= in_array($page,['transaksi','checkout']) ? 'active' : '' ?>">
            <span class="cart-wrap">
                <svg viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0023.46 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <?php if ($num3 > 0): ?>
                <span class="badge-cart"><?= $num3 ?></span>
                <?php endif; ?>
            </span>
            Keranjang
        </a>

        <!-- Keluar -->
        <a href="#" class="nav-item" id="btnLogout">
            <svg viewBox="0 0 24 24">
                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
            </svg>
            Keluar
        </a>
    </nav>
</div>

<script src="vendor/jquery-3.2.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="vendor/bootstrap-4.1/popper.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
<script src="vendor/slick/slick.min.js"></script>
<script src="vendor/wow/wow.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script src="vendor/dropify/dist/js/dropify.min.js"></script>
<script src="js/jquery.input-counter.min.js"></script>
<script src="js/main.js"></script>
<script src="js/sweetalert.min.js"></script>
<script>
$('#btdelete').click(function(e){
    e.preventDefault();
    swal({ title:"Selesai Order?", text:"Yakin ingin mengakhiri sesi order?", type:"warning",
        showCancelButton:true, confirmButtonText:"Ya, Selesai", cancelButtonText:"Batal",
        closeOnConfirm:false, closeOnCancel:true
    }, function(isConfirm){
        if(isConfirm){
            <?php if($data_kd): ?>
            window.location.href="?page=dashboard&delete&kd=<?= $data_kd ?>";
            <?php else: ?>
            swal("Info","Tidak ada pesanan aktif.","info");
            <?php endif; ?>
        }
    });
});

$('#btnLogout').click(function(e){
    e.preventDefault();
    swal({ title:"Keluar?", text:"Yakin ingin logout?", type:"warning",
        showCancelButton:true, confirmButtonText:"Ya, Keluar", cancelButtonText:"Batal",
        closeOnConfirm:false, closeOnCancel:true
    }, function(isConfirm){
        if(isConfirm){ window.location.href="?logout=true"; }
    });
});

$(document).ready(function(){
    if($('#example').length) $('#example').DataTable();
});
if($('.dropify').length) $('.dropify').dropify();
</script>
<?php include "config/alert.php"; ?>
</body>
</html>