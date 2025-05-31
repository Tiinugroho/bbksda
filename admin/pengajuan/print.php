<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../../koneksi.php';

$kode_pengajuan = $_GET['kode'] ?? '';

if (empty($kode_pengajuan)) {
    die('Kode pengajuan tidak valid');
}

// Ambil data pengajuan
$query = "SELECT p.*, j.nama as jenis_izin_nama, j.deskripsi as jenis_izin_deskripsi
          FROM pengajuan_izin p 
          JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
          WHERE p.kode_pengajuan = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 's', $kode_pengajuan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pengajuan = mysqli_fetch_assoc($result);

if (!$pengajuan) {
    die('Data pengajuan tidak ditemukan');
}

// Function untuk mendapatkan status text
function getStatusText($status) {
    switch ($status) {
        case 'pending': return 'Pending';
        case 'diterima': return 'Diterima';
        case 'ditolak': return 'Ditolak';
        default: return 'Unknown';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Pengajuan - <?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .info-table td:first-child {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 200px;
        }
        .status-pending { color: #f39c12; }
        .status-diterima { color: #27ae60; }
        .status-ditolak { color: #e74c3c; }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;">
            Print Dokumen
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>BALAI BESAR KONSERVASI SUMBER DAYA ALAM RIAU</h1>
        <h2>DETAIL PENGAJUAN PERIZINAN</h2>
        <p>Kode Pengajuan: <strong><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td>Kode Pengajuan</td>
            <td><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></td>
        </tr>
        <tr>
            <td>Jenis Perizinan</td>
            <td><?= htmlspecialchars($pengajuan['jenis_izin_nama']) ?></td>
        </tr>
        <tr>
            <td>Nama Pemohon</td>
            <td><?= htmlspecialchars($pengajuan['nama_pemohon']) ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?= htmlspecialchars($pengajuan['email']) ?></td>
        </tr>
        <tr>
            <td>No. WhatsApp</td>
            <td><?= htmlspecialchars($pengajuan['no_wa']) ?></td>
        </tr>
        <?php if (!empty($pengajuan['instansi'])): ?>
        <tr>
            <td>Instansi</td>
            <td><?= htmlspecialchars($pengajuan['instansi']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td>Alamat</td>
            <td><?= nl2br(htmlspecialchars($pengajuan['alamat'])) ?></td>
        </tr>
        <tr>
            <td>Keperluan</td>
            <td><?= nl2br(htmlspecialchars($pengajuan['keperluan'])) ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="status-<?= $pengajuan['status_pengajuan'] ?>">
                <strong><?= getStatusText($pengajuan['status_pengajuan']) ?></strong>
            </td>
        </tr>
        <tr>
            <td>Tanggal Pengajuan</td>
            <td><?= date('d F Y H:i', strtotime($pengajuan['tanggal_pengajuan'])) ?> WIB</td>
        </tr>
        <?php if (!empty($pengajuan['updated_at'])): ?>
        <tr>
            <td>Terakhir Update</td>
            <td><?= date('d F Y H:i', strtotime($pengajuan['updated_at'])) ?> WIB</td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($pengajuan['catatan'])): ?>
        <tr>
            <td>Catatan</td>
            <td><?= nl2br(htmlspecialchars($pengajuan['catatan'])) ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($pengajuan['file_surat_permohonan'])): ?>
        <tr>
            <td>File Surat Permohonan</td>
            <td><?= htmlspecialchars($pengajuan['file_surat_permohonan']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d F Y H:i:s') ?> WIB</p>
        <p>BBKSDA Riau - Sistem Informasi Perizinan</p>
    </div>
</body>
</html>
