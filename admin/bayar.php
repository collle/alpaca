<?php
session_start();
include '../config.php';

// Proteksi halaman
if($_SESSION['role'] != 'admin'){ 
    header("Location: ../index.php"); 
    exit; 
}

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];
// AMBIL ID USER DARI SESSION (Pastikan saat login, id_user disimpan di session)
$id_user = $_SESSION['id_user']; 

// --- LOGIKA PROSES BAYAR ---
if(isset($_POST['proses_bayar'])){
    $id_tagihan = $_POST['id_tagihan'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_bayar = date('Y-m-d');
    
    // Ambil bulan sekarang untuk bulan_bayar
    $bulan_bayar = date('F'); 
    
    $biaya_admin = 2500; 
    $total_tagihan = $_POST['total_tagihan'];
    $total_bayar = $total_tagihan + $biaya_admin;

    // 1. Update status di tabel tagihan
    $u_tagihan = mysqli_query($koneksi, "UPDATE tagihan SET status='Lunas' WHERE id_tagihan='$id_tagihan'");

    // 2. Insert ke tabel pembayaran
    // Sesuaikan urutan kolom: id_pembayaran(auto), id_tagihan, id_pelanggan, tgl, bulan, biaya_admin, total, id_user
    $query_simpan = "INSERT INTO pembayaran (id_tagihan, id_pelanggan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user) 
                     VALUES ('$id_tagihan', '$id_pelanggan', '$tgl_bayar', '$bulan_bayar', '$biaya_admin', '$total_bayar', '$id_user')";
    
    $i_pembayaran = mysqli_query($koneksi, $query_simpan);
    
    if($i_pembayaran){
        echo "<script>alert('Pembayaran Berhasil!'); window.location='cetak_struk.php?id_tagihan=$id_tagihan';</script>";
    } else {
        // Jika error, tampilkan pesan error database-nya
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Proses Pembayaran - ALPACA</title>
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
                    <a href="bayar.php" class="list-group-item list-group-item-action active">Proses Pembayaran</a>
                    <?php endif; ?>
                    <?php if($id_level == 1): ?>
                        <a href="laporan.php" class="list-group-item list-group-item-action">Laporan Transaksi</a>
                        <a href="tunggakan.php" class="list-group-item list-group-item-action text-danger">Data Tunggakan</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold"><i class="bi bi-search me-2"></i>Cari Tagihan Pelanggan</h5>
                        <form method="GET" class="d-flex gap-2 mt-3">
                            <input type="text" name="keyword" class="form-control" placeholder="Masukkan No. KWh atau Nama Pelanggan..." value="<?= @$_GET['keyword'] ?>" required>
                            <button type="submit" class="btn btn-primary px-4">Cari</button>
                        </form>
                    </div>
                </div>

                <?php if(isset($_GET['keyword'])): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Hasil Pencarian Tagihan:</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pelanggan</th>
                                        <th>Bulan/Tahun</th>
                                        <th>Meter (Lalu - Sekarang)</th>
                                        <th>Total Tagihan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $k = $_GET['keyword'];
                                    // Join 4 tabel: Tagihan, Penggunaan, Pelanggan, Tarif
                                    $q = mysqli_query($koneksi, "SELECT t.*, p.nama_pelanggan, p.nomor_kwh, u.meter_awal, u.meter_akhir, tr.tarifperkwh 
                                                                FROM tagihan t
                                                                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                                                JOIN penggunaan u ON t.id_penggunaan = u.id_penggunaan
                                                                JOIN tarif tr ON p.id_tarif = tr.id_tarif
                                                                WHERE (p.nama_pelanggan LIKE '%$k%' OR p.nomor_kwh LIKE '%$k%') 
                                                                AND t.status = 'Belum Bayar'");

                                    if(mysqli_num_rows($q) > 0){
                                        while($r = mysqli_fetch_assoc($q)){
                                            $jumlah_bayar = $r['jumlah_meter'] * $r['tarifperkwh'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= $r['nama_pelanggan'] ?></strong><br>
                                                    <small class="text-muted"><?= $r['nomor_kwh'] ?></small>
                                                </td>
                                                <td><?= $r['bulan'] ?> / <?= $r['tahun'] ?></td>
                                                <td><?= $r['meter_awal'] ?> - <?= $r['meter_akhir'] ?> (<?= $r['jumlah_meter'] ?> kWh)</td>
                                                <td class="text-danger fw-bold">Rp <?= number_format($jumlah_bayar, 0, ',', '.') ?></td>
                                                <td>
                                                    <form method="POST">
                                                        <input type="hidden" name="id_tagihan" value="<?= $r['id_tagihan'] ?>">
                                                        <input type="hidden" name="id_pelanggan" value="<?= $r['id_pelanggan'] ?>">
                                                        <input type="hidden" name="total_tagihan" value="<?= $jumlah_bayar ?>">
                                                        <button type="submit" name="proses_bayar" class="btn btn-success btn-sm" onclick="return confirm('Proses pembayaran ini?')">
                                                            <i class="bi bi-cash-stack me-1"></i> Bayar Sekarang
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Tidak ada tagihan 'Belum Bayar' untuk data tersebut.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>