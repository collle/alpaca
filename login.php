<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password_input = $_POST['password'];

    // Cek Admin
    $cek_admin = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek_admin) === 1) {
        $data = mysqli_fetch_assoc($cek_admin);
            if (password_verify($password_input, $data['password'])) {
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['nama_admin'] = $data['nama_admin']; 
                $_SESSION['id_level'] = $data['id_level'];     
                $_SESSION['role'] = 'admin';
                header("Location: admin/index.php");
            exit;
            }
    }

    // Cek Pelanggan
    $cek_pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE username='$username'");
    if (mysqli_num_rows($cek_pelanggan) === 1) {
        $data = mysqli_fetch_assoc($cek_pelanggan);
            if (password_verify($password_input, $data['password'])) {
                $_SESSION['id_pelanggan'] = $data['id_pelanggan'];
                $_SESSION['nama_pelanggan'] = $data['nama_pelanggan'];
                $_SESSION['role'] = 'pelanggan';
                header("Location: pelanggan/index.php");
            exit;
            }
    }

    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - ALPACA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Login ⚡ ALPACA</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
            <div class="text-center mt-3">
                <a href="register.php">Belum punya akun? Daftar</a> <br>
                <a href="index.php">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
</body>
</html>