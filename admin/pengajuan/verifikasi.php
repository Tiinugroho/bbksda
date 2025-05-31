<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/headerTable.php';
include __DIR__ . '/../layout/sidebar.php';
include __DIR__ . '/../../koneksi.php';

$kode_pengajuan = $_GET['kode'] ?? '';
$error_message = '';
$success_message = '';

if (empty($kode_pengajuan)) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php');
    exit();
}

// Ambil data pengajuan
$query = "SELECT p.*, j.nama as jenis_izin_nama 
          FROM pengajuan_izin p 
          JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
          WHERE p.kode_pengajuan = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 's', $kode_pengajuan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pengajuan = mysqli_fetch_assoc($result);

if (!$pengajuan) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php');
    exit();
}

// Proses update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_baru = $_POST['status'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    
    if (empty($status_baru)) {
        $error_message = 'Status harus dipilih!';
    } else {
        $allowed_status = ['pending', 'diterima', 'ditolak'];
        if (!in_array($status_baru, $allowed_status)) {
            $error_message = 'Status tidak valid!';
        } else {
            // Update status
            $update_query = "UPDATE pengajuan_izin SET status_pengajuan = ?, catatan = ?, updated_at = NOW() WHERE kode_pengajuan = ?";
            $update_stmt = mysqli_prepare($koneksi, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'sss', $status_baru, $catatan, $kode_pengajuan);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $success_message = 'Status berhasil diupdate!';
                // Refresh data pengajuan
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $pengajuan = mysqli_fetch_assoc($result);
            } else {
                $error_message = 'Gagal mengupdate status: ' . mysqli_error($koneksi);
            }
        }
    }
}

// Function untuk mendapatkan badge status
function getStatusBadge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="label label-warning">Pending</span>';
        case 'diterima':
            return '<span class="label label-success">Diterima</span>';
        case 'ditolak':
            return '<span class="label label-danger">Ditolak</span>';
        default:
            return '<span class="label label-default">Unknown</span>';
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Verifikasi Pengajuan
            <small><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= $base_url ?>/admin/dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= $base_url ?>/admin/pengajuan/index.php">Pengajuan</a></li>
            <li class="active">Verifikasi</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Info Pengajuan -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Informasi Pengajuan</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td width="200"><strong>Kode Pengajuan</strong></td>
                                <td><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Perizinan</strong></td>
                                <td><?= htmlspecialchars($pengajuan['jenis_izin_nama']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Pemohon</strong></td>
                                <td><?= htmlspecialchars($pengajuan['nama_pemohon']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td><?= htmlspecialchars($pengajuan['email']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. WhatsApp</strong></td>
                                <td>
                                    <a href="https://wa.me/<?= $pengajuan['no_wa'] ?>" target="_blank">
                                        <?= htmlspecialchars($pengajuan['no_wa']) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td><?= nl2br(htmlspecialchars($pengajuan['alamat'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keperluan</strong></td>
                                <td><?= nl2br(htmlspecialchars($pengajuan['keperluan'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status Saat Ini</strong></td>
                                <td><?= getStatusBadge($pengajuan['status_pengajuan']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengajuan</strong></td>
                                <td><?= date('d F Y H:i', strtotime($pengajuan['tanggal_pengajuan'])) ?> WIB</td>
                            </tr>
                            <?php if (!empty($pengajuan['file_surat_permohonan'])): ?>
                            <tr>
                                <td><strong>Surat Permohonan</strong></td>
                                <td>
                                    <a href="<?= $base_url ?>/uploads/surat-permohonan/<?= htmlspecialchars($pengajuan['file_surat_permohonan']) ?>" 
                                       target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-download"></i> Download Berkas
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Verifikasi -->
            <div class="col-md-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Form Verifikasi</h3>
                    </div>
                    
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-ban"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> <?= htmlspecialchars($success_message) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Status Verifikasi:</label>
                                <select name="status" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="pending" <?= $pengajuan['status_pengajuan'] == 'pending' ? 'selected' : '' ?>>
                                        Pending (Belum Diverifikasi)
                                    </option>
                                    <option value="diterima" <?= $pengajuan['status_pengajuan'] == 'diterima' ? 'selected' : '' ?>>
                                        Diterima (Disetujui)
                                    </option>
                                    <option value="ditolak" <?= $pengajuan['status_pengajuan'] == 'ditolak' ? 'selected' : '' ?>>
                                        Ditolak
                                    </option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Catatan untuk Pemohon:</label>
                                <textarea name="catatan" class="form-control" rows="4" 
                                          placeholder="Tambahkan catatan atau alasan verifikasi..."><?= htmlspecialchars($pengajuan['catatan'] ?? '') ?></textarea>
                                <small class="text-muted">Catatan ini akan dilihat oleh pemohon saat tracking status.</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Panduan Verifikasi:</strong>
                                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                    <li><strong>Diterima:</strong> Jika semua persyaratan terpenuhi</li>
                                    <li><strong>Ditolak:</strong> Jika ada persyaratan yang tidak terpenuhi</li>
                                    <li><strong>Pending:</strong> Jika perlu verifikasi lebih lanjut</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fa fa-save"></i> Update Status Verifikasi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Aksi Lainnya -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Aksi Lainnya</h3>
                    </div>
                    <div class="box-body">
                        <a href="<?= $base_url ?>/admin/pengajuan/detail.php?kode=<?= urlencode($pengajuan['kode_pengajuan']) ?>" 
                           class="btn btn-info btn-block">
                            <i class="fa fa-eye"></i> Lihat Detail Lengkap
                        </a>
                        
                        <a href="<?= $base_url ?>/admin/pengajuan/print.php?kode=<?= urlencode($pengajuan['kode_pengajuan']) ?>" 
                           class="btn btn-default btn-block" target="_blank">
                            <i class="fa fa-print"></i> Print Pengajuan
                        </a>
                        
                        <a href="https://wa.me/<?= $pengajuan['no_wa'] ?>?text=Halo%20<?= urlencode($pengajuan['nama_pemohon']) ?>,%20terkait%20pengajuan%20perizinan%20dengan%20kode%20<?= $pengajuan['kode_pengajuan'] ?>" 
                           class="btn btn-success btn-block" target="_blank">
                            <i class="fa fa-whatsapp"></i> Hubungi via WhatsApp
                        </a>
                        
                        <hr>
                        
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php" class="btn btn-primary btn-block">
                            <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layout/footerTable.php'; ?>
