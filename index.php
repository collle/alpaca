<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALPACA - Aplikasi Listrik Pascabayar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">⚡ ALPACA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Hubungi Kami</a></li>
                    <li class="nav-item"><a class="btn btn-light text-primary ms-2" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5 text-center">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Selamat Datang di ALPACA</h1>
                <p class="col-md-8 fs-4 mx-auto">Aplikasi Pembayaran Listrik Pascabayar yang memudahkan Anda dalam mengecek tagihan, melaporkan penggunaan, dan melihat riwayat pembayaran.</p>
                <a href="register.php" class="btn btn-primary btn-lg" type="button">Daftar Sekarang</a>
            </div>
        </div>
    </div>

    <div class="container mt-5" id="tentang">
        <div class="p-5 mb-4 rounded-3">
            <h3>Tentang Kami</h3>
            <p>ALPACA adalah solusi digital untuk manajemen listrik pascabayar yang transparan dan efisien.</p>
        </div>
    </div>

    <div class="container mt-5 text-center" id="kontak">
        <div class="p-5 mb-4 bg-primary text-white rounded-3">
            <h3>Hubungi Kami</h3>
            <p>Email: cs@alpaca.com <br> Telp: 021-12345678</p>
        </div>
    </div>
</body>
</html>