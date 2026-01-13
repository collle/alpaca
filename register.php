<?php
include 'config.php';
if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password_asli = $_POST['password'];
    $nomor_kwh = $_POST['nomor_kwh'];
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $id_tarif = $_POST['id_tarif'];

    // --- PROSES ENKRIPSI ---
    $password_aman = password_hash($password_asli, PASSWORD_DEFAULT);

    // Insert ke database (password plain text sesuai dump sql)
    $query = "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) 
              VALUES ('$username', '$password_aman', '$nomor_kwh', '$nama', '$alamat', '$id_tarif')";
    
    if(mysqli_query($koneksi, $query)){
        echo "<script>alert('Registrasi Berhasil!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 500px;">
        <h3 class="text-center">Daftar Pelanggan</h3>
        <form method="POST">
            <div class="mb-2"><label>Nama Lengkap</label><input type="text" name="nama_pelanggan" class="form-control" required></div>
            <div class="mb-2"><label>Username</label><input type="text" name="username" class="form-control" required></div>
            <div class="mb-2"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-2"><label>Nomor KWh</label><input type="text" name="nomor_kwh" class="form-control" required></div>
            <div class="mb-2"><label>Alamat</label><textarea name="alamat" class="form-control" required></textarea></div>
            <div class="mb-3">
                <label>Daya Listrik</label>
                <select name="id_tarif" class="form-select">
                    <?php
                    $tarif = mysqli_query($koneksi, "SELECT * FROM tarif");
                    while($t = mysqli_fetch_assoc($tarif)){
                        echo "<option value='$t[id_tarif]'>$t[daya] VA - Rp $t[tarifperkwh]/kWh</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>
            <a href="login.php" class="d-block text-center mt-2">Sudah punya akun? Login</a>
        </form>
    </div>
</body>
</html>