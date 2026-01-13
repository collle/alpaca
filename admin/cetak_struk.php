<?php
session_start();
include '../config.php';

if(!isset($_GET['id_tagihan'])){
    header("Location: bayar.php");
    exit;
}

$id_tagihan = $_GET['id_tagihan'];

// Query diperbaiki: Mengganti tabel 'admin' menjadi 'user' sesuai database
$query_text = "SELECT pmb.*, t.*, p.nama_pelanggan, p.nomor_kwh, tr.daya, tr.tarifperkwh, u.meter_awal, u.meter_akhir, usr.nama_admin
               FROM pembayaran pmb
               JOIN tagihan t ON pmb.id_tagihan = t.id_tagihan
               JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
               JOIN penggunaan u ON t.id_penggunaan = u.id_penggunaan
               JOIN tarif tr ON p.id_tarif = tr.id_tarif
               JOIN user usr ON pmb.id_user = usr.id_user
               WHERE t.id_tagihan = '$id_tagihan'";

$query = mysqli_query($koneksi, $query_text);

// Cek jika query gagal untuk melihat pesan error yang lebih detail
if (!$query) {
    die("Query Error: " . mysqli_error($koneksi));
}

$d = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Struk Pembayaran - <?= $d['nomor_kwh'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; }
        .struk { width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; background: #fff; }
        .text-center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; }
        .footer { margin-top: 20px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="struk">
        <div class="text-center">
            <h3 style="margin:0">⚡ ALPACA</h3>
            <h5 style="margin:0">Aplikasi Listrik Pasca Bayar</h5>
            <p style="margin:0">Struk Pembayaran Tagihan Listrik</p>
        </div>
        
        <div class="line"></div>
        
        <table>
            <tr><td>ID PELANGGAN</td><td>: <?= $d['id_pelanggan'] ?></td></tr>
            <tr><td>NAMA</td><td>: <?= strtoupper($d['nama_pelanggan']) ?></td></tr>
            <tr><td>NO KWH</td><td>: <?= $d['nomor_kwh'] ?></td></tr>
            <tr><td>GOL. TARIF</td><td>: <?= $d['daya'] ?> VA</td></tr>
            <tr><td>BLN/THN</td><td>: <?= $d['bulan'] ?> / <?= $d['tahun'] ?></td></tr>
        </table>
        
        <div class="line"></div>
        
        <table>
            <tr><td>STAND AWAL-AKHIR</td><td>: <?= $d['meter_awal'] ?> - <?= $d['meter_akhir'] ?></td></tr>
            <tr><td>PEMAKAIAN</td><td>: <?= $d['jumlah_meter'] ?> kWh</td></tr>
            <tr><td>BIAYA LISTRIK</td><td>: Rp <?= number_format($d['total_bayar'] - $d['biaya_admin'], 0, ',', '.') ?></td></tr>
            <tr><td>BIAYA ADMIN</td><td>: Rp <?= number_format($d['biaya_admin'], 0, ',', '.') ?></td></tr>
            <tr><td style="font-weight:bold">TOTAL BAYAR</td><td style="font-weight:bold">: Rp <?= number_format($d['total_bayar'], 0, ',', '.') ?></td></tr>
        </table>
        
        <div class="line"></div>
        
        <div class="text-center footer">
            <p>TGL BAYAR: <?= $d['tanggal_pembayaran'] ?> | ADMIN: <?= $d['nama_admin'] ?></p>
            <p><strong>TERIMA KASIH</strong><br>Simpan struk ini sebagai bukti pembayaran sah.</p>
            <button class="no-print" onclick="window.location='bayar.php'" style="margin-top:10px; cursor:pointer; padding: 5px 15px;">Kembali</button>
        </div>
    </div>
</body>
</html>