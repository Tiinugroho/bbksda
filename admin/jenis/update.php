<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama_izin = $_POST['nama_izin'];
    $slug = strtolower(str_replace(' ', '-', $nama_izin));
    $deskripsi = $_POST['deskripsi'];
    $tata_cara = $_POST['tata_cara'];
    $syarat = $_POST['syarat'];
    $is_aktif = $_POST['is_aktif'];

    $query = "UPDATE jenis_izin 
              SET slug = '$slug', nama = '$nama_izin', deskripsi = '$deskripsi', 
                  tata_cara = '$tata_cara', syarat = '$syarat', is_aktif = '$is_aktif', updated_at = NOW()
              WHERE id = $id";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header('Location: ' . $base_url . '/admin/jenis/index.php?success=Data berhasil diupdate');
        exit();
    } else {
        echo 'Error: ' . mysqli_error($koneksi);
    }
} else {
    header('Location: ' . $base_url . '/admin/jenis/index.php?error=Invalid request method');
    exit();
}
