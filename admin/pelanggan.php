<?php
session_start();
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1){ 
    header("Location: index.php"); 
    exit; 
}
include '../config.php';

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level']; // 1 untuk Super Admin, 2 untuk Mitra

// Inisialisasi variabel keyword agar tidak undefined error
$keyword = "";
if (isset($_POST['cari'])) {
    $keyword = $_POST['keyword'];
}

// --- LOGIKA SIMPAN (TAMBAH DATA) ---
if(isset($_POST['simpan'])){
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $nomor_kwh = $_POST['nomor_kwh'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $id_tarif = $_POST['id_tarif'];

    $q = mysqli_query($koneksi, "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) VALUES ('$username', '$password', '$nomor_kwh', '$nama_pelanggan', '$alamat', '$id_tarif')");
    if($q) header("Location: pelanggan.php?pesan=tambah_sukses");
}

// --- LOGIKA UPDATE (EDIT DATA) ---
if(isset($_POST['update'])){
    $id = $_POST['id_pelanggan'];
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $id_tarif = $_POST['id_tarif'];

    $q = mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', alamat='$alamat', id_tarif='$id_tarif' WHERE id_pelanggan='$id'");
    if($q) header("Location: pelanggan.php?pesan=edit_sukses");
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id_pelanggan='$id'");
    mysqli_query($koneksi, "DELETE FROM tagihan WHERE id_pelanggan='$id'");
    mysqli_query($koneksi, "DELETE FROM penggunaan WHERE id_pelanggan='$id'");
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
    header("Location: pelanggan.php?pesan=hapus_sukses");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Pelanggan - ALPACA</title>
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
                    <a href="pelanggan.php" class="list-group-item list-group-item-action active">Kelola Pelanggan</a>
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
                <div class="p-4 bg-white rounded shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold">Data Pelanggan</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-person-plus"></i> Tambah Pelanggan
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="POST" class="d-flex gap-2">
                                <input type="text" name="keyword" class="form-control" placeholder="Cari Nama atau No. KWh..." value="<?= htmlspecialchars($keyword) ?>">
                                <button type="submit" name="cari" class="btn btn-outline-primary">Cari</button>
                                <?php if($keyword != ""): ?>
                                    <a href="pelanggan.php" class="btn btn-outline-secondary">Reset</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No. KWh</th>
                                <th>Nama</th>
                                <th>Daya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Gabungkan query pencarian dan query utama
                            $query = "SELECT p.*, t.daya 
                                      FROM pelanggan p 
                                      JOIN tarif t ON p.id_tarif = t.id_tarif 
                                      WHERE p.nama_pelanggan LIKE '%$keyword%' 
                                      OR p.nomor_kwh LIKE '%$keyword%' 
                                      ORDER BY p.id_pelanggan DESC";
                                      
                            $sql = mysqli_query($koneksi, $query);
                            
                            if (mysqli_num_rows($sql) > 0) {
                                while($r = mysqli_fetch_assoc($sql)): ?>
                                <tr class="text-center">
                                    <td><code><?= $r['nomor_kwh'] ?></code></td>
                                    <td class="text-start"><?= $r['nama_pelanggan'] ?></td>
                                    <td><?= $r['daya'] ?> VA</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-id="<?= $r['id_pelanggan'] ?>"
                                                data-nama="<?= $r['nama_pelanggan'] ?>"
                                                data-alamat="<?= $r['alamat'] ?>"
                                                data-tarif="<?= $r['id_tarif'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <a href="pelanggan.php?hapus=<?= $r['id_pelanggan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pelanggan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; 
                            } else {
                                echo "<tr><td colspan='4' class='text-center text-muted p-4'>Data pelanggan tidak ditemukan.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">No. KWh</label>
                            <input type="number" name="nomor_kwh" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Daya / Tarif</label>
                            <select name="id_tarif" class="form-select" required>
                                <option value="">-- Pilih Daya --</option>
                                <?php
                                $tarif = mysqli_query($koneksi, "SELECT * FROM tarif");
                                while($t = mysqli_fetch_assoc($tarif)) echo "<option value='$t[id_tarif]'>$t[daya] VA - Rp $t[tarifperkwh]</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_pelanggan" id="edit_id">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Daya / Tarif</label>
                            <select name="id_tarif" id="edit_tarif" class="form-select" required>
                                <?php
                                $tarif2 = mysqli_query($koneksi, "SELECT * FROM tarif");
                                while($t2 = mysqli_fetch_assoc($tarif2)) echo "<option value='$t2[id_tarif]'>$t2[daya] VA</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
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
        var modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_nama').value = button.getAttribute('data-nama');
            document.getElementById('edit_alamat').value = button.getAttribute('data-alamat');
            document.getElementById('edit_tarif').value = button.getAttribute('data-tarif');
        });
    </script>
</body>
</html>