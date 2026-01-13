<?php
session_start();
include '../config.php';

// Proteksi: Hanya Super Admin
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1){ 
    header("Location: index.php"); 
    exit; 
}

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];

// --- LOGIKA FILTER & CARI ---
$cari  = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// --- LOGIKA PAGINATION ---
$limit = 10; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman > 1) ? ($halaman * $limit) - $limit : 0;

// Query untuk menghitung total data (untuk pagination)
$query_total = "SELECT COUNT(*) AS total FROM penggunaan 
                JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan 
                WHERE 1=1";
if($cari != '')  { $query_total .= " AND pelanggan.nama_pelanggan LIKE '%$cari%'"; }
if($bulan != '') { $query_total .= " AND penggunaan.bulan = '$bulan'"; }
if($tahun != '') { $query_total .= " AND penggunaan.tahun = '$tahun'"; }

$result_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_rows = $data_total['total'];
$total_pages = ceil($total_rows / $limit);

// --- LOGIKA UPDATE PENGGUNAAN ---
if(isset($_POST['update'])){
    $id_penggunaan = $_POST['id_penggunaan'];
    $meter_awal = $_POST['meter_awal'];
    $meter_akhir = $_POST['meter_akhir'];

    $query = mysqli_query($koneksi, "UPDATE penggunaan SET 
                                     meter_awal = '$meter_awal', 
                                     meter_akhir = '$meter_akhir' 
                                     WHERE id_penggunaan = '$id_penggunaan'");
    if($query){
        echo "<script>alert('Data penggunaan berhasil diperbaiki!'); window.location='penggunaan.php?halaman=$halaman&cari=$cari&bulan=$bulan&tahun=$tahun';</script>";
    }
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM penggunaan WHERE id_penggunaan='$id'");
    header("Location: penggunaan.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Penggunaan - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">⚡ ALPACA ADMIN</a>
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
                        <a href="tunggakan.php" class="list-group-item list-group-item-action text-danger">Data Tunggakan</a>
                        <a href="penggunaan.php" class="list-group-item list-group-item-action active">Koreksi Meteran</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Koreksi Meteran</h5>
                        <form method="GET" action="" class="row g-2">
                            <div class="col-md-4">
                                <label class="small fw-bold">Cari Pelanggan</label>
                                <input type="text" name="cari" class="form-control" placeholder="Nama pelanggan..." value="<?= $cari ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    <?php
                                    $list_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    foreach($list_bulan as $b):
                                        $selected = ($bulan == $b) ? 'selected' : '';
                                        echo "<option value='$b' $selected>$b</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" placeholder="2026" value="<?= $tahun ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                                <a href="penggunaan.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Pelanggan</th>
                                        <th>Bulan/Tahun</th>
                                        <th>Meter Awal</th>
                                        <th>Meter Akhir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query dengan LIMIT dan OFFSET
                                    $sql = "SELECT penggunaan.*, pelanggan.nama_pelanggan 
                                            FROM penggunaan 
                                            JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan 
                                            WHERE 1=1";
                                    
                                    if($cari != '')  { $sql .= " AND pelanggan.nama_pelanggan LIKE '%$cari%'"; }
                                    if($bulan != '') { $sql .= " AND penggunaan.bulan = '$bulan'"; }
                                    if($tahun != '') { $sql .= " AND penggunaan.tahun = '$tahun'"; }

                                    $sql .= " ORDER BY id_penggunaan DESC LIMIT $offset, $limit";
                                    $q = mysqli_query($koneksi, $sql);
                                    $no = $offset + 1;

                                    if(mysqli_num_rows($q) > 0):
                                        while($d = mysqli_fetch_assoc($q)):
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $d['nama_pelanggan'] ?></td>
                                        <td class="text-center"><?= $d['bulan'] ?> <?= $d['tahun'] ?></td>
                                        <td class="text-center"><?= $d['meter_awal'] ?></td>
                                        <td class="text-center"><?= $d['meter_akhir'] ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-sm btn-edit-meter" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditMeter"
                                                data-id="<?= $d['id_penggunaan'] ?>"
                                                data-nama="<?= $d['nama_pelanggan'] ?>"
                                                data-awal="<?= $d['meter_awal'] ?>"
                                                data-akhir="<?= $d['meter_akhir'] ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="?hapus=<?= $d['id_penggunaan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?halaman=<?= $halaman - 1 ?>&cari=<?= $cari ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>">Previous</a>
                                </li>

                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($halaman == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $i ?>&cari=<?= $cari ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= ($halaman >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?halaman=<?= $halaman + 1 ?>&cari=<?= $cari ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditMeter" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold text-dark">Koreksi Angka Meteran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_penggunaan" id="edit-id-meter">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Pelanggan</label>
                            <input type="text" id="edit-nama-pelanggan" class="form-control bg-light" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Meter Awal (kWh)</label>
                                <input type="number" name="meter_awal" id="edit-meter-awal" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Meter Akhir (kWh)</label>
                                <input type="number" name="meter_akhir" id="edit-meter-akhir" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btnEditMeter = document.querySelectorAll('.btn-edit-meter');
        btnEditMeter.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit-id-meter').value = this.getAttribute('data-id');
                document.getElementById('edit-nama-pelanggan').value = this.getAttribute('data-nama');
                document.getElementById('edit-meter-awal').value = this.getAttribute('data-awal');
                document.getElementById('edit-meter-akhir').value = this.getAttribute('data-akhir');
            });
        });
    </script>
</body>
</html>