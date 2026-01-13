<?php
session_start();
include '../config.php';
$id_pelanggan = $_SESSION['id_pelanggan'];
$nama = $_SESSION['nama_pelanggan'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pembayaran</title>
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
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Arsip Pembayaran Lunas</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Bayar</th>
                            <th>Bulan Tagihan</th>
                            <th>Biaya Admin</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Loket Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Join tabel pembayaran, tagihan, dan user (admin yang memproses)
                        $query = "SELECT bayar.*, tgh.bulan, tgh.tahun, usr.nama_admin 
                                  FROM pembayaran bayar
                                  JOIN tagihan tgh ON bayar.id_tagihan = tgh.id_tagihan
                                  JOIN user usr ON bayar.id_user = usr.id_user
                                  WHERE bayar.id_pelanggan = '$id_pelanggan'
                                  ORDER BY bayar.id_pembayaran DESC";
                        
                        $sql = mysqli_query($koneksi, $query);
                        $no = 1;

                        if(mysqli_num_rows($sql) > 0){
                            while($d = mysqli_fetch_assoc($sql)){
                                echo "<tr>
                                    <td>$no</td>
                                    <td>".date('d-m-Y', strtotime($d['tanggal_pembayaran']))."</td>
                                    <td>$d[bulan] $d[tahun]</td>
                                    <td>Rp ".number_format($d['biaya_admin'],0,',','.')."</td>
                                    <td class='fw-bold text-success'>Rp ".number_format($d['total_bayar'],0,',','.')."</td>
                                    <td><span class='badge bg-success'>LUNAS</span></td>
                                    <td>$d[nama_admin]</td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Belum ada riwayat pembayaran.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
                <div class="mt-3">
                    <button onclick="window.print()" class="btn btn-secondary btn-sm">Cetak Riwayat</button>
                    <a href="index.php" class="btn btn-primary btn-sm">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>