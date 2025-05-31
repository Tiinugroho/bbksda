<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/headerTable.php';
include __DIR__ . '/../layout/sidebar.php';
include __DIR__ . '/../../koneksi.php';

$kode_pengajuan = $_GET['kode'] ?? '';

if (empty($kode_pengajuan)) {
    header('Location: ' . $base_url . '/admin/pengajuan/index.php');
    exit();
}

// Ambil data pengajuan
$query = "SELECT p.*, j.nama as jenis_izin_nama, j.deskripsi as jenis_izin_deskripsi,
                 j.tata_cara, j.syarat
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
            Detail Pengajuan
            <small><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= $base_url ?>/admin/dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= $base_url ?>/admin/pengajuan/index.php">Pengajuan</a></li>
            <li class="active">Detail</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Informasi Pemohon -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Informasi Pengajuan</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td width="200"><strong>Kode Pengajuan</strong></td>
                                <td>
                                    <span class="label label-info"><?= htmlspecialchars($pengajuan['kode_pengajuan']) ?></span>
                                </td>
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
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($pengajuan['email']) ?>">
                                        <?= htmlspecialchars($pengajuan['email']) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>No. WhatsApp</strong></td>
                                <td>
                                    <a href="https://wa.me/<?= $pengajuan['no_wa'] ?>" target="_blank" class="btn btn-xs btn-success">
                                        <i class="fa fa-whatsapp"></i> <?= htmlspecialchars($pengajuan['no_wa']) ?>
                                    </a>
                                </td>
                            </tr>
                            <?php if (!empty($pengajuan['instansi'])): ?>
                            <tr>
                                <td><strong>Instansi</strong></td>
                                <td><?= htmlspecialchars($pengajuan['instansi']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td><?= nl2br(htmlspecialchars($pengajuan['alamat'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keperluan</strong></td>
                                <td><?= nl2br(htmlspecialchars($pengajuan['keperluan'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Berkas Pendukung -->
                <?php if (!empty($pengajuan['file_surat_permohonan'])): ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Berkas Pendukung</h3>
                    </div>
                    <div class="box-body">
                        <div class="attachment-block clearfix">
                            <img class="attachment-img" src="<?= $base_url ?>/admin/dist/img/file-icon.png" alt="Attachment" style="width: 60px;">
                            <div class="attachment-pushed" style="margin-left: 70px;">
                                <h4 class="attachment-heading">
                                    <a href="<?= $base_url ?>/uploads/surat-permohonan/<?= htmlspecialchars($pengajuan['file_surat_permohonan']) ?>" target="_blank">
                                        <?= htmlspecialchars($pengajuan['file_surat_permohonan']) ?>
                                    </a>
                                </h4>
                                <div class="attachment-text">
                                    Surat permohonan yang diupload oleh pemohon.
                                    <a href="<?= $base_url ?>/uploads/surat-permohonan/<?= htmlspecialchars($pengajuan['file_surat_permohonan']) ?>" target="_blank">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status dan Aksi -->
            <div class="col-md-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Status Pengajuan</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Status Saat Ini</strong></td>
                                <td><?= getStatusBadge($pengajuan['status_pengajuan']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengajuan</strong></td>
                                <td><?= date('d F Y H:i', strtotime($pengajuan['tanggal_pengajuan'])) ?> WIB</td>
                            </tr>
                            <?php if (!empty($pengajuan['updated_at'])): ?>
                            <tr>
                                <td><strong>Terakhir Update</strong></td>
                                <td><?= date('d F Y H:i', strtotime($pengajuan['updated_at'])) ?> WIB</td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($pengajuan['catatan'])): ?>
                            <tr>
                                <td><strong>Catatan</strong></td>
                                <td><?= nl2br(htmlspecialchars($pengajuan['catatan'])) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>

                        <?php if ($pengajuan['status_pengajuan'] == 'pending'): ?>
                        <div class="text-center" style="margin-top: 15px;">
                            <a href="<?= $base_url ?>/admin/pengajuan/verifikasi.php?kode=<?= urlencode($pengajuan['kode_pengajuan']) ?>" 
                               class="btn btn-warning btn-block">
                                <i class="fa fa-edit"></i> Verifikasi Pengajuan
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Progress Pengajuan</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $progress = 0;
                        $progress_class = 'progress-bar-warning';
                        switch ($pengajuan['status_pengajuan']) {
                            case 'pending': 
                                $progress = 33; 
                                $progress_class = 'progress-bar-warning';
                                break;
                            case 'diterima': 
                                $progress = 100; 
                                $progress_class = 'progress-bar-success';
                                break;
                            case 'ditolak': 
                                $progress = 100; 
                                $progress_class = 'progress-bar-danger';
                                break;
                        }
                        ?>
                        <div class="progress">
                            <div class="progress-bar <?= $progress_class ?>" style="width: <?= $progress ?>%">
                                <?= $progress ?>%
                            </div>
                        </div>
                        
                        <div class="progress-steps" style="margin-top: 15px;">
                            <div class="step completed" style="margin-bottom: 10px; color: #00a65a;">
                                <i class="fa fa-check"></i> Pengajuan Diterima
                            </div>
                            <div class="step <?= in_array($pengajuan['status_pengajuan'], ['diterima', 'ditolak']) ? 'completed' : '' ?>" 
                                 style="margin-bottom: 10px; color: <?= in_array($pengajuan['status_pengajuan'], ['diterima', 'ditolak']) ? '#00a65a' : '#999' ?>;">
                                <i class="fa fa-gear"></i> Sedang Diverifikasi
                            </div>
                            <div class="step <?= $pengajuan['status_pengajuan'] == 'diterima' ? 'completed' : ($pengajuan['status_pengajuan'] == 'ditolak' ? 'rejected' : '') ?>" 
                                 style="margin-bottom: 10px; color: <?= $pengajuan['status_pengajuan'] == 'diterima' ? '#00a65a' : ($pengajuan['status_pengajuan'] == 'ditolak' ? '#dd4b39' : '#999') ?>;">
                                <i class="fa fa-<?= $pengajuan['status_pengajuan'] == 'ditolak' ? 'times' : 'check' ?>"></i> 
                                <?= $pengajuan['status_pengajuan'] == 'ditolak' ? 'Ditolak' : 'Selesai' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aksi Lainnya -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Aksi Lainnya</h3>
                    </div>
                    <div class="box-body">
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
