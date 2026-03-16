<?php
$sp          = new Resto();
$kd          = $_GET['kd'] ?? null;
$dataDetail  = $sp->edit("tb_detail_order", "order_kd", $kd);
$jumlah_menu = $sp->selectSumWhere("tb_detail_order", "total", "order_kd='$kd'");
$total       = $sp->selectSumWhere("tb_detail_order", "sub_total", "order_kd='$kd'");

// Ambil nama menu via JOIN manual kalau tidak ada di tb_detail_order
global $con;
$dataDetail2 = [];
if ($kd) {
    $sqlD = "SELECT d.*, m.name_menu, m.harga
             FROM tb_detail_order d
             LEFT JOIN tb_menu m ON d.menu_kd = m.kd_menu
             WHERE d.order_kd = '$kd'";
    $exeD = mysqli_query($con, $sqlD);
    if ($exeD) while ($row = mysqli_fetch_assoc($exeD)) $dataDetail2[] = $row;
}

// Ambil tanggal dari tb_order
$tgl = '-';
if ($kd) {
    $sqlTgl = "SELECT tanggal FROM tb_order WHERE kd_order='$kd'";
    $exeTgl = mysqli_query($con, $sqlTgl);
    $dtoTgl = mysqli_fetch_assoc($exeTgl);
    $tgl    = $dtoTgl['tanggal'] ?? '-';
}

$useData = count($dataDetail2) > 0 ? $dataDetail2 : $dataDetail;
?>
<style>
    .struk-wrap { background: #fff; padding: 24px; border-radius: 12px; }
    @media print {
        .ds, .hd, .header-desktop2, .bottom-nav, .menu-sidebar2, .page-container2 > header { display: none !important; }
        .card { box-shadow: none !important; border: none !important; }
        body { background: #fff !important; }
    }
</style>

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="card struk-wrap">
                        <div class="card-header">
                            <h4>Struk Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Kode Transaksi:</strong> <?= htmlspecialchars($kd ?? '-') ?>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <strong>Tanggal Cetak:</strong> <?= date("Y-m-d") ?>
                                </div>
                            </div>
                            <br>

                            <?php if (count($useData) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Kode Detail</th>
                                            <th>Nama Menu</th>
                                            <th>Harga Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($useData as $dd): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($dd['kd_detail'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($dd['name_menu'] ?? $dd['menu_kd'] ?? '-') ?></td>
                                            <td>Rp <?= number_format($dd['harga'] ?? 0, 0, ',', '.') ?></td>
                                            <td><?= $dd['total'] ?? 0 ?></td>
                                            <td>Rp <?= number_format($dd['sub_total'] ?? 0, 0, ',', '.') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td><strong>Jumlah Pembelian</strong></td>
                                            <td><strong><?= $jumlah_menu['sum'] ?? 0 ?></strong></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="2"><strong>Total Bayar</strong></td>
                                            <td><strong>Rp <?= number_format($total['sum'] ?? 0, 0, ',', '.') ?>,-</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">Data transaksi tidak ditemukan.</div>
                            <?php endif; ?>

                            <br>
                            <p><strong>Tanggal Beli:</strong> <?= htmlspecialchars($tgl) ?></p>
                            <br>
                            <a href="#" class="btn btn-info ds" onclick="window.print()">
                                <i class="fa fa-print"></i> Cetak Struk
                            </a>
                            <a href="?" class="btn btn-danger ds">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>