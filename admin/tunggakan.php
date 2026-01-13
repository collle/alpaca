<?php
session_start();
include '../config.php';

// Proteksi Halaman: Hanya Super Admin (Level 1) yang bisa masuk
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1){ 
    echo "<script>alert('Akses Ditolak! Halaman ini khusus Super Admin.'); window.location='index.php';</script>";
    exit; 
}

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Tunggakan - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">⚡ ALPACA ADMIN</a>
            <span class="navbar-text text-white">
                <i class="bi bi-person-circle"></i> <?= $nama_admin ?> 
                <span class="badge bg-primary ms-2"><?= ($id_level == 1) ? 'Super Admin' : 'Mitra' ?></span>
            </span>
        </div>
    </nav>

<div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <div class="list-group-item bg-light fw-bold text-muted small text-uppercase">Menu Utama</div>
                    <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                    
                    <?php if($id_level == 3): ?>
                    <a href="input_penggunaan.php" class="list-group-item list-group-item-action text-primary">
                        <i class="bi bi-pencil-square"></i> Catat Meteran</a>
                    <?php endif; ?>
                    <?php if($id_level == 1): ?>
                    <a href="pelanggan.php" class="list-group-item list-group-item-action">Kelola Pelanggan</a>
                    <a href="tarif.php" class="list-group-item list-group-item-action">Kelola Tarif</a>
                    <?php endif; ?>
                    <?php if($_SESSION['id_level'] != 3): ?>
                    <a href="bayar.php" class="list-group-item list-group-item-action">Proses Pembayaran</a>
                    <?php endif; ?>
                    <?php if($id_level == 1): ?>
                        <a href="laporan.php" class="list-group-item list-group-item-action">Laporan Transaksi</a>
                        <a href="tunggakan.php" class="list-group-item list-group-item-action active">Data Tunggakan</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-octagon me-2"></i>Daftar Pelanggan Belum Bayar</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Pilih Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">-- Semua Bulan --</option>
                                    <?php
                                    $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    foreach ($months as $m) {
                                        $selected = (@$_GET['bulan'] == $m) ? 'selected' : '';
                                        echo "<option value='$m' $selected>$m</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2024" value="<?= @$_GET['tahun'] ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-dark w-100">Filter Data</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-danger text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Pelanggan</th>
                                        <th>No. KWh</th>
                                        <th>Periode</th>
                                        <th>Jumlah Tagihan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $where = "WHERE t.status = 'Belum Bayar'";
                                    
                                    if(!empty($_GET['bulan'])) {
                                        $bulan = $_GET['bulan'];
                                        $where .= " AND t.bulan = '$bulan'";
                                    }
                                    if(!empty($_GET['tahun'])) {
                                        $tahun = $_GET['tahun'];
                                        $where .= " AND t.tahun = '$tahun'";
                                    }

                                    $query = mysqli_query($koneksi, "SELECT t.*, p.nama_pelanggan, p.nomor_kwh, tr.tarifperkwh 
                                                                    FROM tagihan t
                                                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                                                    JOIN tarif tr ON p.id_tarif = tr.id_tarif
                                                                    $where
                                                                    ORDER BY t.tahun DESC, t.bulan DESC");

                                    $total_piutang = 0;
                                    if(mysqli_num_rows($query) > 0){
                                        while($r = mysqli_fetch_assoc($query)){
                                            $tagihan = $r['jumlah_meter'] * $r['tarifperkwh'];
                                            $total_piutang += $tagihan;
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><strong><?= $r['nama_pelanggan'] ?></strong></td>
                                                <td class="text-center"><?= $r['nomor_kwh'] ?></td>
                                                <td class="text-center"><?= $r['bulan'] ?> / <?= $r['tahun'] ?></td>
                                                <td class="text-end">Rp <?= number_format($tagihan, 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark">Belum Bayar</span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Tidak ada data tunggakan pada periode ini.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <?php if($total_piutang > 0): ?>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">TOTAL PIUTANG (YANG BELUM MASUK):</th>
                                        <th class="text-end text-danger">Rp <?= number_format($total_piutang, 0, ',', '.') ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>