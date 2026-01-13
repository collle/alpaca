<?php
session_start();
include '../config.php';
$id_pelanggan = $_SESSION['id_pelanggan'];
$nama = $_SESSION['nama_pelanggan'];

// --- LOGIKA OTOMATIS METER AWAL ---
// Mengambil meter_akhir terakhir dari database berdasarkan id_pelanggan
$query_terakhir = mysqli_query($koneksi, "SELECT meter_akhir FROM penggunaan WHERE id_pelanggan='$id_pelanggan' ORDER BY id_penggunaan DESC LIMIT 1");
$data_terakhir = mysqli_fetch_assoc($query_terakhir);

// Jika belum pernah lapor sama sekali, set meter awal jadi 0
$meter_awal_otomatis = ($data_terakhir) ? $data_terakhir['meter_akhir'] : 0;
// ----------------------------------

// Tambah Data
if(isset($_POST['simpan'])){
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $awal = $_POST['meter_awal'];
    $akhir = $_POST['meter_akhir'];
    
   // Validasi sederhana agar meter akhir tidak lebih kecil dari meter awal
    if($akhir < $awal) {
        echo "<script>alert('Gagal! Meter akhir tidak boleh lebih kecil dari meter awal.');</script>";
    } else {
        $q = mysqli_query($koneksi, "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) VALUES ('$id_pelanggan', '$bulan', '$tahun', '$awal', '$akhir')");
        if($q) {
            echo "<script>alert('Berhasil Lapor!'); window.location='lapor.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lapor Penggunaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand">Dashboard Pelanggan - Halo, <?= $nama ?></a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
</nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Lapor Penggunaan Listrik</h3>
        <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Form Input Meteran</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" value="<?= date('Y') ?>" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select">
                            <?php 
                            $bulan_array = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            foreach($bulan_array as $bln) {
                                echo "<option value='$bln'>$bln</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Meter Awal (Otomatis)</label>
                        <input type="number" name="meter_awal" value="<?= $meter_awal_otomatis ?>" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Meter Akhir</label>
                        <input type="number" name="meter_akhir" placeholder="Masukkan angka meteran" class="form-control" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Meter Awal</th>
                        <th>Meter Akhir</th>
                        <th>Total Pemakaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($koneksi, "SELECT * FROM penggunaan WHERE id_pelanggan='$id_pelanggan' ORDER BY id_penggunaan DESC");
                    if(mysqli_num_rows($data) > 0) {
                        while($d = mysqli_fetch_assoc($data)){
                            $pemakaian = $d['meter_akhir'] - $d['meter_awal'];
                            echo "<tr>
                                <td>$d[bulan] $d[tahun]</td>
                                <td>$d[meter_awal]</td>
                                <td>$d[meter_akhir]</td>
                                <td>$pemakaian kWh</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Belum ada data penggunaan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>