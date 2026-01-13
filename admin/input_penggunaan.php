<?php
session_start();
include '../config.php';

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level']; // 1 untuk Super Admin, 2 untuk Mitra, 3 untuk Pencatat

// Proteksi: Hanya Super Admin
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1 && $_SESSION['id_level'] != 3){ 
    header("Location: index.php"); 
    exit; 
}

if(isset($_POST['simpan'])){
    $id_pelanggan = $_POST['id_pelanggan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $meter_awal = $_POST['meter_awal'];
    $meter_akhir = $_POST['meter_akhir'];

    // VALIDASI: Apakah pelanggan ini sudah dilaporkan bulan ini di tabel PENGGUNAAN?
    $cek_penggunaan = mysqli_query($koneksi, "SELECT * FROM penggunaan WHERE id_pelanggan='$id_pelanggan' AND bulan='$bulan' AND tahun='$tahun'");
    
    if(mysqli_num_rows($cek_penggunaan) > 0) {
        echo "<script>alert('Error: Data penggunaan bulan ini sudah ada!'); window.location='input_penggunaan.php';</script>";
        exit;
    }

    // PROSES INSERT 1: Tabel Penggunaan
    $insert_p = mysqli_query($koneksi, "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                                        VALUES ('$id_pelanggan', '$bulan', '$tahun', '$meter_awal', '$meter_akhir')");
    
    if($insert_p) {
        $id_penggunaan = mysqli_insert_id($koneksi);
        $jumlah_meter = $meter_akhir - $meter_awal;

        // VALIDASI: Apakah ID penggunaan ini sudah ada di tabel TAGIHAN? (Double Check)
        $cek_tagihan = mysqli_query($koneksi, "SELECT * FROM tagihan WHERE id_penggunaan='$id_penggunaan'");
        
        if(mysqli_num_rows($cek_tagihan) == 0) {
            // PROSES INSERT 2: Tabel Tagihan
            mysqli_query($koneksi, "INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) 
                                    VALUES ('$id_penggunaan', '$id_pelanggan', '$bulan', '$tahun', '$jumlah_meter', 'Belum Bayar')");
        }

        // REDIRECT INSTAN untuk memutus siklus POST
        header("Location: input_penggunaan.php?status=sukses");
        exit;
    }
}

// Tampilkan alert sukses jika setelah redirect
if(isset($_GET['status']) && $_GET['status'] == 'sukses'){
    echo "<script>alert('Data Berhasil Disimpan!');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Penggunaan - Admin</title>
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
                    <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                    
                    <?php if($id_level == 1 || $id_level == 3): ?>
                    <a href="input_penggunaan.php" class="list-group-item list-group-item-action active">
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
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Catat Meteran Pelanggan</h5>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pelanggan</label>
                            <select name="id_pelanggan" id="id_pelanggan" class="form-select form-select-lg" required onchange="getMeterAwal(this.value)">
                                <option value="">-- Cari Nama / No. KWh --</option>
                                <?php
                                $p = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                                while($row = mysqli_fetch_assoc($p)){
                                    echo "<option value='$row[id_pelanggan]'>$row[nomor_kwh] - $row[nama_pelanggan]</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bulan Periode</label>
                                <select name="bulan" class="form-select">
                                    <?php
                                    $bln = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                    foreach($bln as $b) {
                                        $selected = ($b == date('F')) ? 'selected' : ''; // Otomatis pilih bulan sekarang
                                        echo "<option value='$b' $selected>$b</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mt-3 mb-4 p-3 bg-light rounded border">
                            <div class="col-md-6">
                                <label class="form-label text-primary fw-bold">Meter Awal (Bulan Lalu)</label>
                                <input type="number" name="meter_awal" id="meter_awal" class="form-control fw-bold" readonly placeholder="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-success fw-bold">Meter Akhir (Bulan Ini)</label>
                                <input type="number" name="meter_akhir" class="form-control fw-bold" placeholder="Contoh: 1250" required autofocus>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="simpan" class="btn btn-primary btn-lg">Simpan Data Penggunaan</button>
                            <a href="index.php" class="btn btn-outline-secondary">Batal & Kembali</a>
                        </div>
                    </form>
                </div>
            </div> 

<script>
function getMeterAwal(id) {
    if(id == "") {
        document.getElementById('meter_awal').value = "";
        return;
    }
    fetch('get_meter.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('meter_awal').value = data;
        });
}
</script>
</body>
</html>