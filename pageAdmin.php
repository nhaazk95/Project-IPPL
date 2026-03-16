<?php
    include "config/controller.php";
    $function = new Resto();
    session_start();
    $auth = $function->AuthUser($_SESSION['username']);
    $response = $function->sessionCheck();
    if ($response == "false") { header("Location:loginMulti.php"); }
    if ($_SESSION['level'] != "Admin") { header("Location:loginMulti.php"); }
    if (isset($_GET['logout'])) { $function->logout(); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Dapur Nusantara</title>
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="vendor/dropify/dist/css/dropify.css">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --brown-dark:  #3E1F00;
            --brown-mid:   #7B3F00;
            --brown-warm:  #A0522D;
            --brown-light: #C4956A;
            --gold:        #C8973A;
            --cream:       #FDF6EC;
        }

        /* ── LOGO TEKS ── */
        .logo-text-admin {
            font-family: 'Lora', serif !important;
            font-size: 20px !important;
            font-weight: 700 !important;
            color: var(--gold) !important;
            text-decoration: none !important;
            padding: 18px 20px !important;
            display: block !important;
            letter-spacing: .3px;
            white-space: nowrap;
        }
        .logo-text-admin:hover { color: #fff !important; text-decoration: none !important; }

        /* ── SIDEBAR ── */
        .menu-sidebar2 { background: var(--brown-dark) !important; border-right: 1px solid rgba(200,151,58,0.2) !important; }
        .menu-sidebar2 .logo { background: var(--brown-dark) !important; border-bottom: 2px solid rgba(200,151,58,0.25) !important; padding: 0 !important; }
        .menu-sidebar2 .account2 .name { color: var(--gold) !important; font-family: 'Lora', serif !important; }
        .menu-sidebar2 .account2 span  { color: rgba(255,255,255,0.55) !important; }
        .menu-sidebar2__content { background: var(--brown-dark) !important; }

        .navbar-sidebar2 .navbar__list > li > a { color: rgba(255,255,255,0.65) !important; font-family: 'Nunito', sans-serif !important; font-weight: 700 !important; }
        .navbar-sidebar2 .navbar__list > li > a:hover { color: var(--gold) !important; background: rgba(200,151,58,0.12) !important; }
        .navbar-sidebar2 .navbar__list > li > a i { color: rgba(255,255,255,0.5) !important; }
        .navbar-sidebar2 .navbar__list > li > a:hover i { color: var(--gold) !important; }
        .navbar-sidebar2 .navbar__list > li.active > a { color: var(--gold) !important; }
        .navbar-mobile-sub__list { background: rgba(0,0,0,0.2) !important; }
        .navbar-mobile-sub__list a { color: rgba(255,255,255,0.6) !important; }
        .navbar-mobile-sub__list a:hover { color: var(--gold) !important; }

        /* ── HEADER ── */
        .header-desktop2 { background: var(--brown-mid) !important; border-bottom: 1px solid rgba(200,151,58,0.3) !important; }
        .header-desktop2 i { color: rgba(255,255,255,0.85) !important; }
        .header-desktop2 i:hover { color: var(--gold) !important; }

        /* ── DASHBOARD CARDS ── */
        .overview-item--c1 { background: linear-gradient(135deg, #A0522D, #7B3F00) !important; }
        .overview-item--c2 { background: linear-gradient(135deg, #6B8E23, #3B5323) !important; }
        .overview-item--c3 { background: linear-gradient(135deg, #C8973A, #A0522D) !important; }

        /* ── BREADCRUMB ── */
        .au-breadcrumb { background: var(--cream) !important; }
        .au-breadcrumb__list li a { color: var(--brown-warm) !important; }
        .au-breadcrumb__list li.active a { color: var(--gold) !important; }

        /* ── BUTTONS ── */
        .btn-primary { background: var(--brown-warm) !important; border-color: var(--brown-warm) !important; }
        .btn-primary:hover { background: var(--brown-dark) !important; border-color: var(--brown-dark) !important; }
        .btn-info    { background: var(--brown-mid) !important; border-color: var(--brown-mid) !important; }
        .btn-success { background: #5a7a2e !important; border-color: #5a7a2e !important; }

        /* ── TABLE ── */
        .table thead th { background: var(--brown-dark) !important; color: var(--gold) !important; border-color: rgba(200,151,58,0.2) !important; }

        /* ── PAGE BG ── */
        body { background: var(--cream) !important; }
        .page-container2 { background: var(--cream) !important; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- ── SIDEBAR KIRI ── -->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="#" class="logo-text-admin">🍛 Dapur Nusantara</a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <img src="images/avatar.png" />
                    </div>
                    <h4 class="name"><?= $auth['name'] ?></h4>
                    <span><?= $auth['level'] ?></span>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="?page"><i class="zmdi zmdi-view-dashboard zmdi-hc-lg"></i>Dashboard</a></li>
                        <li><a href="?page=indexLevel"><i class="zmdi zmdi-account zmdi-hc-lg"></i>Level</a></li>
                        <li><a href="?page=indexKategori"><i class="zmdi zmdi-widgets zmdi-hc-lg"></i>Kategori</a></li>
                        <li><a href="?page=indexMenu"><i class="zmdi zmdi-local-dining zmdi-hc-lg"></i>Menu</a></li>
                        <li><a href="?page=indexMeja"><i class="zmdi zmdi-chart zmdi-hc-lg"></i>Meja</a></li>
                        <li><a href="?page=indexTransaksi"><i class="zmdi zmdi-card zmdi-hc-lg"></i>Transaksi</a></li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#"><i class="fas fa-archive"></i>Laporan</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li><a href="?page=indexLaporan">Kelola Transaksi</a></li>
                                <li><a href="?page=order_periode">Orderan per Periode</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-container2">
            <!-- ── HEADER ── -->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <!-- Logo mobile (header) -->
                            <div class="logo d-block d-lg-none">
                                <a href="#" class="logo-text-admin" style="padding:10px 16px !important;font-size:15px !important;">
                                    🍛 Dapur Nusantara
                                </a>
                            </div>
                            <div class="header-button2">
                                <div class="header-button-item js-item-menu">
                                    <i class="zmdi zmdi-search"></i>
                                    <div class="search-dropdown js-dropdown">
                                        <form action="">
                                            <input class="au-input au-input--full au-input--h65" type="text" placeholder="Search..."/>
                                            <span class="search-dropdown__icon"><i class="zmdi zmdi-search"></i></span>
                                        </form>
                                    </div>
                                </div>
                                <div class="header-button-item mr-0 js-sidebar-btn">
                                    <i class="zmdi zmdi-menu"></i>
                                </div>
                                <div class="setting-menu js-right-sidebar d-none d-lg-block">
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <a href="?page=profile"><i class="zmdi zmdi-account"></i>Account</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#"><i class="zmdi zmdi-settings"></i>Setting</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#" id="forLogout"><i class="zmdi zmdi-power"></i>Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ── SIDEBAR MOBILE ── -->
            <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
                <div class="logo">
                    <a href="#" class="logo-text-admin">🍛 Dapur Nusantara</a>
                </div>
                <div class="menu-sidebar2__content js-scrollbar2">
                    <div class="account2">
                        <div class="image img-cir img-120">
                            <img src="images/avatar.png" alt="Avatar" />
                        </div>
                        <h4 class="name"><?= $auth['name'] ?></h4>
                    </div>
                    <nav class="navbar-sidebar2">
                        <ul class="list-unstyled navbar__list">
                            <li><a href="?page"><i class="zmdi zmdi-view-dashboard zmdi-hc-lg"></i>Dashboard</a></li>
                            <li><a href="?page=indexLevel"><i class="zmdi zmdi-account zmdi-hc-lg"></i>Level</a></li>
                            <li><a href="?page=indexKategori"><i class="zmdi zmdi-widgets zmdi-hc-lg"></i>Kategori</a></li>
                            <li><a href="?page=indexMenu"><i class="zmdi zmdi-local-dining zmdi-hc-lg"></i>Menu</a></li>
                            <li><a href="?page=indexMeja"><i class="zmdi zmdi-chart zmdi-hc-lg"></i>Meja</a></li>
                            <li><a href="index_admin.php" target="_blank"><i class="zmdi zmdi-shopping-cart zmdi-hc-lg"></i>Order</a></li>
                            <li><a href="?page=indexTransaksi"><i class="zmdi zmdi-card zmdi-hc-lg"></i>Transaksi</a></li>
                            <li><a href="?page=indexLaporan"><i class="zmdi zmdi-book zmdi-hc-lg"></i>Laporan</a></li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <?php
            @$page = $_GET['page'];
            switch ($page) {
                case 'indexLevel':      include "page/admin/level/index.php";               break;
                case 'indexKategori':   include "page/admin/kategori/index.php";            break;
                case 'createKategori':  include "page/admin/kategori/create.php";           break;
                case 'editKategori':    include "page/admin/kategori/edit.php";             break;
                case 'indexMenu':       include "page/admin/menu/index.php";                break;
                case 'createMenu':      include "page/admin/menu/create.php";               break;
                case 'editMenu':        include "page/admin/menu/edit.php";                 break;
                case 'indexMeja':       include "page/admin/meja/index.php";                break;
                case 'indexLaporan':    include "page/admin/laporan/laporan_transaksi.php"; break;
                case 'order_periode':   include "page/admin/laporan/order_periode.php";     break;
                case 'indexTransaksi':  include "page/admin/transaksi/index.php";           break;
                case 'struk_transaksi': include "page/admin/transaksi/struk_transaksi.php";
                default:
                    $page = "dashboard";
                    include "page/admin/dashboard/index.php";
                    break;
            }
            ?>
        </div>
    </div>

    <script src="vendor/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/vector-map/jquery.vmap.js"></script>
    <script src="vendor/vector-map/jquery.vmap.min.js"></script>
    <script src="vendor/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="vendor/vector-map/jquery.vmap.world.js"></script>
    <script src="vendor/dropify/dist/js/dropify.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#forLogout').click(function(e){
            e.preventDefault();
            swal({
                title: "Logout",
                text: "Yakin Logout?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Logout",
                cancelButtonText: "Batal",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) { window.location.href = "?logout"; }
            });
        });
        if ($('#example').length) $('#example').DataTable();
    });
    if ($('.dropify').length) $('.dropify').dropify();
    </script>
    <?php include "config/alert.php"; ?>
</body>
</html>