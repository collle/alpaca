<?php
session_start();
if($_SESSION['role'] != 'pelanggan'){ header("Location: ../login.php"); exit; }
include '../config.php';
$nama = $_SESSION['nama_pelanggan'];
$id_pelanggan = $_SESSION['id_pelanggan'];

// --- LOGIKA HITUNG TOTAL TAGIHAN ---
$query_info = mysqli_query($koneksi, "SELECT tagihan.jumlah_meter, tarif.tarifperkwh 
                                      FROM tagihan 
                                      JOIN pelanggan ON tagihan.id_pelanggan = pelanggan.id_pelanggan
                                      JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
                                      WHERE tagihan.id_pelanggan='$id_pelanggan' AND tagihan.status='Belum Bayar'");

$total_tunggakan = 0;
$biaya_admin = 2500; // Asumsi biaya admin per tagihan

while($t = mysqli_fetch_assoc($query_info)){
    $subtotal = ($t['jumlah_meter'] * $t['tarifperkwh']) + $biaya_admin;
    $total_tunggakan += $subtotal;
}
// ------------------------------------
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand">Dashboard Pelanggan - Halo, <?= $nama ?></a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3 h-100">
                    <div class="card-header">Info Tagihan</div>
                    <div class="card-body">
                        <h5 class="card-title">Tagihan Belum Bayar</h5>
                        
                        <h2 class="fw-bold">
                            Rp <?= number_format($total_tunggakan, 0, ',', '.') ?>
                        </h2>
                        
                        <p class="card-text">Total nominal yang harus Anda lunasi saat ini.</p>
                        <a href="tagihan.php" class="btn btn-light text-danger w-100">Lihat Rincian</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3 h-100">
                    <div class="card-header">Lapor Penggunaan</div>
                    <div class="card-body">
                        <h5 class="card-title">Catat Meteran</h5>
                        <p class="card-text">Input meteran listrik bulanan Anda secara mandiri.</p>
                        <a href="lapor.php" class="btn btn-light text-primary w-100">Input Data</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success mb-3 h-100">
                    <div class="card-header">Riwayat Pembayaran</div>
                    <div class="card-body">
                        <h5 class="card-title">Arsip Pembayaran</h5>
                        <p class="card-text">Lihat histori pembayaran yang telah lunas.</p>
                        <a href="riwayat.php" class="btn btn-light text-success w-100">Lihat Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>