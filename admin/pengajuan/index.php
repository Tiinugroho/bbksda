<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/headerTable.php';
include __DIR__ . '/../layout/sidebar.php';
include __DIR__ . '/../../koneksi.php';

// Cek koneksi database
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Filter status jika ada
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query dengan filter
$where_conditions = [];
$params = [];

if (!empty($status_filter)) {
    $where_conditions[] = "p.status_pengajuan = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(p.kode_pengajuan LIKE ? OR p.nama_pemohon LIKE ? OR p.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Query untuk mengambil data pengajuan
$query = "SELECT p.*, j.nama as jenis_izin_nama 
          FROM pengajuan_izin p 
          JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
          $where_clause
          ORDER BY p.tanggal_pengajuan DESC";

if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $query);
    if ($stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        die('Error preparing statement: ' . mysqli_error($koneksi));
    }
} else {
    $result = mysqli_query($koneksi, $query);
    if (!$result) {
        die('Error executing query: ' . mysqli_error($koneksi));
    }
}

// Statistik untuk cards
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status_pengajuan = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status_pengajuan = 'diterima' THEN 1 ELSE 0 END) as diterima,
    SUM(CASE WHEN status_pengajuan = 'ditolak' THEN 1 ELSE 0 END) as ditolak
    FROM pengajuan_izin";
$stats_result = mysqli_query($koneksi, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

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
            Verifikasi Pengajuan Perizinan <br>
            <small>Kelola dan verifikasi semua pengajuan perizinan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= $base_url ?>/admin/dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Pengajuan Perizinan</li>
        </ol>
    </section>

    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pengajuan</span>
                        <span class="info-box-number"><?= $stats['total'] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending</span>
                        <span class="info-box-number"><?= $stats['pending'] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Diterima</span>
                        <span class="info-box-number"><?= $stats['diterima'] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ditolak</span>
                        <span class="info-box-number"><?= $stats['ditolak'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Pengajuan Perizinan</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="box-body">
                <!-- Filter dan Search -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-4">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label>Filter Status:</label>
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="diterima" <?= $status_filter == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                    <option value="ditolak" <?= $status_filter == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                            </div>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <form method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari kode, nama, atau email..." 
                                       value="<?= htmlspecialchars($search) ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        </form>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php" class="btn btn-default">
                            <i class="fa fa-list"></i> Semua
                        </a>
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php?status=pending" class="btn btn-warning">
                            <i class="fa fa-clock-o"></i> Pending (<?= $stats['pending'] ?>)
                        </a>
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php?status=diterima" class="btn btn-success">
                            <i class="fa fa-check"></i> Diterima (<?= $stats['diterima'] ?>)
                        </a>
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php?status=ditolak" class="btn btn-danger">
                            <i class="fa fa-times"></i> Ditolak (<?= $stats['ditolak'] ?>)
                        </a>
                    </div>
                </div>

                <!-- Tabel Data -->
                <table id="example2" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pengajuan</th>
                            <th>Jenis Perizinan</th>
                            <th>Nama Pemohon</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($row['kode_pengajuan']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($row['jenis_izin_nama']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pemohon']) ?></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($row['email']) ?>">
                                    <?= htmlspecialchars($row['email']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= $row['no_wa'] ?>" target="_blank" class="text-success">
                                    <i class="fa fa-whatsapp"></i> <?= htmlspecialchars($row['no_wa']) ?>
                                </a>
                            </td>
                            <td><?= getStatusBadge($row['status_pengajuan']) ?></td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($row['tanggal_pengajuan'])) ?></small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= $base_url ?>/admin/pengajuan/detail.php?kode=<?= urlencode($row['kode_pengajuan']) ?>" 
                                       class="btn btn-xs btn-info" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($row['status_pengajuan'] == 'pending'): ?>
                                    <a href="<?= $base_url ?>/admin/pengajuan/verifikasi.php?kode=<?= urlencode($row['kode_pengajuan']) ?>" 
                                       class="btn btn-xs btn-warning" title="Verifikasi">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($row['file_surat_permohonan'])): ?>
                                    <a href="<?= $base_url ?>/uploads/surat-permohonan/<?= htmlspecialchars($row['file_surat_permohonan']) ?>" 
                                       target="_blank" class="btn btn-xs btn-default" title="Download Surat">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= $base_url ?>/admin/pengajuan/print.php?kode=<?= urlencode($row['kode_pengajuan']) ?>" 
                                       target="_blank" class="btn btn-xs btn-default" title="Print">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Modal untuk Quick Verify -->
<div class="modal fade" id="quickVerifyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Quick Verify</h4>
            </div>
            <form id="quickVerifyForm" method="POST" action="<?= $base_url ?>/admin/pengajuan/quick-verify.php">
                <div class="modal-body">
                    <input type="hidden" id="verify_kode" name="kode_pengajuan">
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" class="form-control" required>
                            <option value="">Pilih Status</option>
                            <option value="diterima">Terima</option>
                            <option value="ditolak">Tolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan:</label>
                        <textarea name="catatan" class="form-control" rows="3" 
                                  placeholder="Tambahkan catatan untuk pemohon..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function quickVerify(kode) {
    document.getElementById('verify_kode').value = kode;
    $('#quickVerifyModal').modal('show');
}

// Auto refresh setiap 30 detik untuk update status terbaru
setInterval(function() {
    // Hanya refresh jika tidak ada modal yang terbuka
    if (!$('.modal').hasClass('in')) {
        location.reload();
    }
}, 30000);
</script>

<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.btn-group .btn {
    margin-right: 2px;
}

.text-primary {
    color: #337ab7 !important;
}

.text-success {
    color: #3c763d !important;
}
</style>

<?php include __DIR__ . '/../layout/footerTable.php'; ?>
