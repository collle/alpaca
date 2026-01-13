<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "listrikdb";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal Terhubung ke Database: " . mysqli_connect_error());
}
?>