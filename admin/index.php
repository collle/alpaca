<?php
session_start();
// Cek apakah sudah login sebagai admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
    header("Location: ../login.php"); 
    exit; 
}

include '../config.php';

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level']; // 1 untuk Super Admin, 2 untuk Mitra, 3 untuk Pencatat
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
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
            <div class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <div class="list-group-item bg-light fw-bold text-muted small text-uppercase">Menu Utama</div>
                    <a href="index.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    
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
                        <a href="tunggakan.php" class="list-group-item list-group-item-action text-danger">Data Tunggakan</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="p-5 bg-white rounded shadow-sm border">
                    <h2 class="fw-bold">Selamat Datang, <?= $nama_admin ?></h2>
                    <p class="lead text-muted">Silahkan gunakan menu dibawah ini untuk mengakses cepat.</p>
                    <hr class="my-4">
                    
                    <div class="row g-3">
                        <?php if($_SESSION['id_level'] != 3): ?>
                        <div class="col-md-6">
                            <div class="card bg-success text-white h-100 border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">Pembayaran</h5>
                                    <p>Gunakan ini untuk melayani pelanggan yang ingin membayar tagihan.</p>
                                    <a href="bayar.php" class="btn btn-light btn-sm fw-bold">Proses pembayaran</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($id_level == 1 || $id_level == 3): ?>
                        <div class="col-md-6">
                            <div class="card bg-primary text-white h-100 border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">Input Penggunaan</h5>
                                    <p>Pelanggan lupa lapor? Klik tombol di bawah untuk membantu mencatat meteran.</p>
                                    <a href="input_penggunaan.php" class="btn btn-light btn-sm fw-bold text-primary">Input Meteran</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($id_level == 1): ?>
                        <div class="col-md-6">
                            <div class="card bg-secondary text-white h-100 border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">Koreksi Meteran</h5>
                                    <p>Klik tombol dibawah ini untuk mengoreksi meteran pelanggan jika terjadi kesalahan input.</p>
                                    <a href="penggunaan.php" class="btn btn-light btn-sm fw-bold">Koreksi</a>
                                </div>
                            </div>
                        </div>                     
                        
                        <div class="col-md-6">
                            <div class="card bg-light text-emphasis h-100 border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">Kelola User</h5>
                                    <p>Klik tombol dibawah ini untuk mengelola user.</p>
                                    <a href="user.php" class="btn btn-light btn-sm fw-bold">Kelola</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>