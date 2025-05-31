<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../../koneksi.php';

// Cek apakah request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php');
    exit();
}

$kode_pengajuan = $_POST['kode_pengajuan'] ?? '';
$status = $_POST['status'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');

// Validasi input
if (empty($kode_pengajuan) || empty($status)) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php?error=invalid_input');
    exit();
}

$allowed_status = ['diterima', 'ditolak'];
if (!in_array($status, $allowed_status)) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php?error=invalid_status');
    exit();
}

// Update status
$update_query = "UPDATE pengajuan_izin SET status_pengajuan = ?, catatan = ?, updated_at = NOW() WHERE kode_pengajuan = ?";
$stmt = mysqli_prepare($koneksi, $update_query);
mysqli_stmt_bind_param($stmt, 'sss', $status, $catatan, $kode_pengajuan);

if (mysqli_stmt_execute($stmt)) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php?success=status_updated');
} else {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php?error=update_failed');
}
exit();
?>
