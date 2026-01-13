<?php
session_start();
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1){ 
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit; 
}
include '../config.php';

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];

// --- LOGIKA SIMPAN (TAMBAH) ---
if(isset($_POST['simpan'])){
    $daya = $_POST['daya'];
    $tarif = $_POST['tarifperkwh'];
    
    $query = mysqli_query($koneksi, "INSERT INTO tarif (daya, tarifperkwh) VALUES ('$daya', '$tarif')");
    if($query) header("Location: tarif.php?pesan=tambah_sukses");
}

// --- LOGIKA UPDATE (EDIT) ---
if(isset($_POST['update'])){
    $id = $_POST['id_tarif'];
    $daya = $_POST['daya'];
    $tarif = $_POST['tarifperkwh'];

    $query = mysqli_query($koneksi, "UPDATE tarif SET daya='$daya', tarifperkwh='$tarif' WHERE id_tarif='$id'");
    if($query) header("Location: tarif.php?pesan=edit_sukses");
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $cek = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_tarif='$id'");
    
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Gagal! Tarif ini sedang digunakan oleh pelanggan.'); window.location='tarif.php';</script>";
    } else {
        mysqli_query($koneksi, "DELETE FROM tarif WHERE id_tarif='$id'");
        header("Location: tarif.php?pesan=hapus_sukses");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Tarif - ALPACA</title>
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
                    <a href="tarif.php" class="list-group-item list-group-item-action active">Kelola Tarif</a>
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
                <div class="p-4 bg-white rounded shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Data Tarif Listrik</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Tarif
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Daya (VA)</th>
                                    <th>Tarif / kWh</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $sql = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
                                if(mysqli_num_rows($sql) > 0){
                                    while($d = mysqli_fetch_assoc($sql)){ ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="fw-bold text-primary"><?= $d['daya'] ?> VA</td>
                                        <td>Rp <?= number_format($d['tarifperkwh'], 0, ',', '.') ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEdit"
                                                    data-id="<?= $d['id_tarif'] ?>"
                                                    data-daya="<?= $d['daya'] ?>"
                                                    data-tarif="<?= $d['tarifperkwh'] ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="tarif.php?hapus=<?= $d['id_tarif'] ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Hapus tarif ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } 
                                } else {
                                    echo "<tr><td colspan='4' class='text-muted'>Belum ada data tarif.</td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tarif Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Daya (VA)</label>
                            <input type="number" name="daya" class="form-control" placeholder="Contoh: 900" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tarif per kWh (Rp)</label>
                            <input type="number" name="tarifperkwh" class="form-control" placeholder="Contoh: 1467" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit Data Tarif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_tarif" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Daya (VA)</label>
                            <input type="number" name="daya" id="edit_daya" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tarif per kWh (Rp)</label>
                            <input type="number" name="tarifperkwh" id="edit_tarif" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-warning">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika untuk mengirim data ke Modal Edit
        var modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Tombol yang diklik
            // Ambil data dari atribut data-*
            var id = button.getAttribute('data-id');
            var daya = button.getAttribute('data-daya');
            var tarif = button.getAttribute('data-tarif');

            // Masukkan ke dalam input form modal
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_daya').value = daya;
            document.getElementById('edit_tarif').value = tarif;
        });
    </script>
</body>
</html>