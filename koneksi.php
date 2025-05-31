<?php
// Konfigurasi database
$host     = "localhost";
$user     = "root";
$password = ""; // Ganti jika ada password
$database = "db_bbksda"; // Ganti dengan nama database Anda

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    // echo "Koneksi berhasil";
}
?>
