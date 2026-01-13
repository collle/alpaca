<?php
include '../config.php';
$id = $_GET['id'];

// Cari data terakhir dari pelanggan tersebut
$query = mysqli_query($koneksi, "SELECT meter_akhir FROM penggunaan WHERE id_pelanggan='$id' ORDER BY id_penggunaan DESC LIMIT 1");
$data = mysqli_fetch_assoc($query);

if($data) {
    echo $data['meter_akhir'];
} else {
    echo "0"; // Jika pelanggan baru
}
?>