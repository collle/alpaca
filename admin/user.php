<?php
session_start();
include '../config.php';

// Proteksi: Hanya Super Admin yang boleh kelola user
if($_SESSION['role'] != 'admin' || $_SESSION['id_level'] != 1){ 
    header("Location: index.php"); 
    exit; 
}

$nama_admin = $_SESSION['nama_admin'];
$id_level = $_SESSION['id_level'];

// --- LOGIKA TAMBAH USER ---
if(isset($_POST['simpan'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_petugas = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $level_baru = $_POST['id_level'];
    $password_raw = $_POST['password'];
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    mysqli_query($koneksi, "INSERT INTO user (username, password, nama_admin, id_level) VALUES ('$username', '$password_hash', '$nama_petugas', '$level_baru')");
    echo "<script>alert('User Berhasil Ditambahkan'); window.location='user.php';</script>";
}

// --- LOGIKA UPDATE USER ---
if(isset($_POST['update'])){
    $id_user = $_POST['id_user'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_petugas = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    $level_edit = $_POST['id_level'];
    $password_raw = $_POST['password'];

    if(!empty($password_raw)){
        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
        $query = "UPDATE user SET username='$username', password='$password_hash', nama_admin='$nama_petugas', id_level='$level_edit' WHERE id_user='$id_user'";
    } else {
        $query = "UPDATE user SET username='$username', nama_admin='$nama_petugas', id_level='$level_edit' WHERE id_user='$id_user'";
    }

    mysqli_query($koneksi, $query);
    echo "<script>alert('Data Berhasil Diperbarui'); window.location='user.php';</script>";
}

// --- LOGIKA HAPUS USER ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user='$id'");
    header("Location: user.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola User - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">⚡ ALPACA ADMIN</a>
            <span class="navbar-text text-white">
                <i class="bi bi-person-circle"></i> <?= $nama_admin ?> 
                <span class="badge bg-primary ms-2">Super Admin</span>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <div class="list-group-item bg-light fw-bold text-muted small text-uppercase">Menu Utama</div>
                    <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="pelanggan.php" class="list-group-item list-group-item-action">Kelola Pelanggan</a>
                    <a href="tarif.php" class="list-group-item list-group-item-action">Kelola Tarif</a>
                    <a href="bayar.php" class="list-group-item list-group-item-action">Proses Pembayaran</a>
                    <a href="laporan.php" class="list-group-item list-group-item-action">Laporan Transaksi</a>
                    <a href="tunggakan.php" class="list-group-item list-group-item-action text-danger">Data Tunggakan</a>
                    <a href="user.php" class="list-group-item list-group-item-action active">Kelola User</a>
                    <a href="../logout.php" class="list-group-item list-group-item-action" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2"></i>Daftar Petugas</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Petugas
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Petugas</th>
                                        <th>Username</th>
                                        <th>Level</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $q = mysqli_query($koneksi, "SELECT user.*, level.nama_level FROM user JOIN level ON user.id_level = level.id_level");
                                    while($d = mysqli_fetch_assoc($q)):
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $d['nama_admin'] ?></td>
                                        <td><?= $d['username'] ?></td>
                                        <td class="text-center"><span class="badge bg-info text-dark"><?= $d['nama_level'] ?></span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditUser"
                                                data-id="<?= $d['id_user'] ?>"
                                                data-nama="<?= $d['nama_admin'] ?>"
                                                data-user="<?= $d['username'] ?>"
                                                data-level="<?= $d['id_level'] ?>">
                                                <i class="bi bi-pencil text-white"></i>
                                            </button>
                                            <a href="?hapus=<?= $d['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Petugas Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label fw-bold small text-muted">Nama Lengkap</label><input type="text" name="nama_admin" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small text-muted">Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small text-muted">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small text-muted">Level Akses</label>
                            <select name="id_level" class="form-select">
                                <option value="1">Super Admin</option><option value="2">Mitra</option><option value="3">Pencatat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="simpan" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Petugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_user" id="edit-id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Lengkap</label>
                            <input type="text" name="nama_admin" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Username</label>
                            <input type="text" name="username" id="edit-username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Ganti Password (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Level Akses</label>
                            <select name="id_level" id="edit-level" class="form-select">
                                <option value="1">Super Admin</option>
                                <option value="2">Mitra</option>
                                <option value="3">Pencatat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-warning">Update Petugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const btnEdits = document.querySelectorAll('.btn-edit');
        btnEdits.forEach(btn => {
            btn.addEventListener('click', function() {
                // Mengambil data dari atribut tombol yang diklik
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const user = this.getAttribute('data-user');
                const level = this.getAttribute('data-level');

                // Memasukkan data ke dalam input modal edit
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nama').value = nama;
                document.getElementById('edit-username').value = user;
                document.getElementById('edit-level').value = level;
            });
        });
    </script>
</body>
</html>