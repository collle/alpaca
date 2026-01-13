<?php
session_start();
include '../config.php';
$id_pelanggan = $_SESSION['id_pelanggan'];
$nama = $_SESSION['nama_pelanggan'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Tagihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand">Dashboard Pelanggan - Halo, <?= $nama ?></a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Tagihan Anda</h3>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Jumlah Meter</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query JOIN untuk mendapatkan data tagihan, tarif, penggunaan, dan pembayaran (jika ada)
                    $query = "SELECT t.*, tr.tarifperkwh, tr.daya, p.nama_pelanggan, p.nomor_kwh,
                              u.meter_awal, u.meter_akhir,
                              bayar.tanggal_pembayaran, usr.nama_admin, bayar.biaya_admin, bayar.total_bayar as bayar_asli
                              FROM tagihan t 
                              JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                              JOIN tarif tr ON p.id_tarif = tr.id_tarif
                              JOIN penggunaan u ON t.id_penggunaan = u.id_penggunaan
                              LEFT JOIN pembayaran bayar ON t.id_tagihan = bayar.id_tagihan
                              LEFT JOIN user usr ON bayar.id_user = usr.id_user
                              WHERE t.id_pelanggan='$id_pelanggan'
                              ORDER BY t.tahun DESC, t.bulan DESC";
                    
                    $sql = mysqli_query($koneksi, $query);
                    $admin_fee = 2500;

                    while($r = mysqli_fetch_assoc($sql)){
                        $biaya_listrik = $r['jumlah_meter'] * $r['tarifperkwh'];
                        $estimasi_total = $biaya_listrik + $admin_fee;
                        $status_color = ($r['status'] == 'Lunas') ? 'success' : 'warning';
                        
                        // Menyiapkan data untuk dikirim ke Modal via Javascript
                        $json_data = htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= $r['bulan'] ?> <?= $r['tahun'] ?></td>
                            <td><?= $r['jumlah_meter'] ?> kWh</td>
                            <td class="fw-bold">Rp <?= number_format(($r['status'] == 'Lunas' ? $r['bayar_asli'] : $estimasi_total), 0, ',', '.') ?></td>
                            <td><span class="badge bg-<?= $status_color ?>"><?= $r['status'] ?></span></td>
                            <td>
                                <button type="button" 
                                        class="btn btn-info btn-sm text-white btn-detail" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDetail" 
                                        data-item='<?= $json_data ?>'>
                                    Rincian
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-info mt-3">
        <strong>Info:</strong> Silakan lakukan pembayaran melalui Mitra/Loket Pembayaran Resmi dengan menyebutkan ID Pelanggan, Nomor KWh atau Nama Anda.
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Rincian Tagihan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless">
                    <tr><td>Status Pembayaran</td><td class="fw-bold text-uppercase" id="det-status"></td></tr>
                    <hr>
                    <tr><td>ID Pelanggan</td><td id="det-no"></td></tr>
                    <tr><td>Nama</td><td id="det-nama"></td></tr>
                    <tr><td>Periode</td><td id="det-periode"></td></tr>
                    <tr><td>Golongan Tarif</td><td id="det-tarif"></td></tr>
                    <tr><td>Stand awal - akhir</td><td id="det-meter"></td></tr>
                    <tr><td>Pemakaian</td><td id="det-pakai"></td></tr>
                    <tr><td>Tanggal Bayar</td><td id="det-tgl"></td></tr>
                    <tr><td>Loket Pembayaran</td><td id="det-loket"></td></tr>
                    <hr>
                    <tr><td>Biaya Listrik</td><td id="det-listrik"></td></tr>
                    <tr><td>Biaya Admin</td><td id="det-admin"></td></tr>
                    <tr class="table-primary fw-bold"><td>TOTAL</td><td id="det-total"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Cetak</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-detail').forEach(button => {
    button.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-item'));
        
        // Format Rupiah
        const formatRupiah = (num) => "Rp " + parseInt(num).toLocaleString('id-ID');

        const biayaListrik = data.jumlah_meter * data.tarifperkwh;
        const biayaAdmin = data.biaya_admin ? data.biaya_admin : 2500;
        const total = data.bayar_asli ? data.bayar_asli : (biayaListrik + 2500);

        // Isi Data ke Modal
        document.getElementById('modalTitle').innerText = "Rincian Tagihan " + data.bulan + " " + data.tahun;
        document.getElementById('det-status').innerText = ": " + data.status;
        document.getElementById('det-status').className = "fw-bold text-uppercase " + (data.status === 'Lunas' ? 'text-success' : 'text-danger');
        document.getElementById('det-no').innerText = ": " + data.nomor_kwh;
        document.getElementById('det-nama').innerText = ": " + data.nama_pelanggan;
        document.getElementById('det-periode').innerText = ": " + data.bulan + " " + data.tahun;
        document.getElementById('det-tarif').innerText = ": " + data.daya + " VA (Rp " + data.tarifperkwh + "/kWh)";
        document.getElementById('det-meter').innerText = ": " + data.meter_awal + " - " + data.meter_akhir;
        document.getElementById('det-pakai').innerText = ": " + data.jumlah_meter + " kWh";
        document.getElementById('det-tgl').innerText = ": " + (data.tanggal_pembayaran ? data.tanggal_pembayaran : "-");
        document.getElementById('det-loket').innerText = ": " + (data.nama_admin ? data.nama_admin : "-");
        document.getElementById('det-listrik').innerText = ": " + formatRupiah(biayaListrik);
        document.getElementById('det-admin').innerText = ": " + formatRupiah(biayaAdmin);
        document.getElementById('det-total').innerText = ": " + formatRupiah(total);
    });
});
</script>
</body>
</html>