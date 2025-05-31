<?php
$base_url = 'http://localhost/bbksda';

// koneksi ke database
include __DIR__ . '/../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_izin = $_POST['nama_izin'];
    $slug = strtolower(str_replace(' ', '-', $nama_izin));
    $deskripsi = $_POST['deskripsi'];
    $tata_cara = $_POST['tata_cara'];
    $syarat = $_POST['syarat'];
    $is_aktif = $_POST['is_aktif'];

    $query = "INSERT INTO jenis_izin (slug, nama, deskripsi, tata_cara, syarat, is_aktif, created_at)
              VALUES ('$slug', '$nama_izin', '$deskripsi', '$tata_cara', '$syarat', '$is_aktif', NOW())";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header('Location: ' . $base_url . '/admin/jenis/index.php?success=Data berhasil ditambahkan');
        exit();
    } else {
        echo 'Error: ' . mysqli_error($koneksi);
    }
} else {
    header('Location: ' . $base_url . '/admin/jenis/create.php?error=Invalid request method');
    exit();
}
?>
