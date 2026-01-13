<?php
session_start();
include '../config.php';

// Hanya Admin yang bisa akses
if($_SESSION['role'] != 'admin'){ 
    header("Location: ../index.php"); 
    exit; 
}

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];

// Logika Filter Tanggal
$tgl_mulai = @$_GET['tgl_mulai'] ?: date('Y-m-01'); 
$tgl_selesai = @$_GET['tgl_selesai'] ?: date('Y-m-d'); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Transaksi - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* CSS Khusus Mode Cetak */
        @media print {
            /* Sembunyikan elemen yang tidak diperlukan */
            .no-print, .sidebar-nav, .navbar, .btn, form, .breadcrumb { 
                display: none !important; 
            }
            
            /* Atur agar konten utama memenuhi lebar kertas */
            .col-md-9 { 
                width: 100% !important; 
                flex: 0 0 100% !important; 
                max-width: 100% !important; 
            }
            
            .container { 
                max-width: 100% !important; 
                width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }

            .card { 
                border: none !important; 
                box-shadow: none !important; 
            }

            body { 
                background-color: white !important; 
            }

            /* Tambahkan judul laporan saat dicetak jika perlu */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
            }
        }

        /* Sembunyikan judul cetak di tampilan layar biasa */
        .print-header { display: none; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">⚡ ALPACA ADMIN</a>
            <span class="navbar-text text-white">
                <i class="bi bi-person-circle"></i> <?= $nama_admin ?> 
                <span class="badge bg-primary ms-2">
                    <?php if($id_level == 1): ?>Super Admin<?php elseif($id_level == 2): ?>Mitra<?php else: ?>Pencatat<?php endif; ?></span>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4 no-print">
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
                        <a href="laporan.php" class="list-group-item list-group-item-action active">Laporan Transaksi</a>
                        <a href="tunggakan.php" class="list-group-item list-group-item-action text-danger">Data Tunggakan</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>


            <div class="col-md-9">
                <div class="print-header">
                    <h2 class="fw-bold">LAPORAN PEMBAYARAN LISTRIK ALPACA</h2>
                    <p>Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
                    <hr>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0">Riwayat Transaksi</h4>
                            <button onclick="window.print()" class="btn btn-success no-print">
                                <i class="bi bi-printer me-2"></i>Cetak Laporan
                            </button>
                        </div>

                        <form method="GET" class="row g-3 mb-4 no-print">
                            <div class="col-md-4">
                                <label class="small fw-bold">Dari Tanggal</label>
                                <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Sampai Tanggal</label>
                                <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter Laporan</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tgl Bayar</th>
                                        <th>Pelanggan</th>
                                        <th>Bulan Tagihan</th>
                                        <th>Biaya Admin</th>
                                        <th>Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $total_semua = 0;
                                    $total_admin = 0;

                                    $sql = "SELECT pmb.*, p.nama_pelanggan, t.bulan, t.tahun 
                                            FROM pembayaran pmb
                                            JOIN pelanggan p ON pmb.id_pelanggan = p.id_pelanggan
                                            JOIN tagihan t ON pmb.id_tagihan = t.id_tagihan
                                            WHERE pmb.tanggal_pembayaran BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                            ORDER BY pmb.tanggal_pembayaran DESC";
                                    
                                    $query = mysqli_query($koneksi, $sql);
                                    
                                    if(mysqli_num_rows($query) > 0) {
                                        while($d = mysqli_fetch_assoc($query)) {
                                            $total_semua += $d['total_bayar'];
                                            $total_admin += $d['biaya_admin'];
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td class="text-center"><?= date('d/m/Y', strtotime($d['tanggal_pembayaran'])) ?></td>
                                                <td><?= $d['nama_pelanggan'] ?></td>
                                                <td class="text-center"><?= $d['bulan'] ?> <?= $d['tahun'] ?></td>
                                                <td class="text-end">Rp <?= number_format($d['biaya_admin'], 0, ',', '.') ?></td>
                                                <td class="text-end fw-bold">Rp <?= number_format($d['total_bayar'], 0, ',', '.') ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-muted py-4'>Tidak ada transaksi pada periode ini.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="4" class="text-end">TOTAL PENDAPATAN ADMIN:</td>
                                        <td class="text-end text-primary">Rp <?= number_format($total_admin, 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                                        <td></td>
                                        <td class="text-end text-success">Rp <?= number_format($total_semua, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mt-4 d-none d-print-block text-end" style="margin-right: 50px;">
                            <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
                            <br><br><br>
                            <p class="fw-bold">( <?= $nama_admin ?> )</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>