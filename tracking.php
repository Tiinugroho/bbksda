<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/layout/header.php';
include __DIR__ . '/koneksi.php';

$tracking_results = [];
$tracking_detail = null;
$error_message = '';
$search_type = '';
$search_value = '';
$detail_kode = '';

// Cek apakah ada request untuk detail berdasarkan kode pengajuan
if (isset($_GET['detail_kode']) && !empty($_GET['detail_kode'])) {
    $detail_kode = trim($_GET['detail_kode']);

    // Query untuk mengambil detail berdasarkan kode pengajuan
    $detail_query = "SELECT p.*, j.nama as jenis_izin_nama, j.deskripsi as jenis_izin_deskripsi,
                            j.tata_cara, j.syarat
                     FROM pengajuan_izin p 
                     JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
                     WHERE p.kode_pengajuan = ?";

    $stmt = mysqli_prepare($koneksi, $detail_query);
    mysqli_stmt_bind_param($stmt, 's', $detail_kode);
    mysqli_stmt_execute($stmt);
    $detail_result = mysqli_stmt_get_result($stmt);

    if ($detail_result && mysqli_num_rows($detail_result) > 0) {
        $tracking_detail = mysqli_fetch_assoc($detail_result);
    } else {
        $error_message = 'Data pengajuan dengan kode "' . htmlspecialchars($detail_kode) . '" tidak ditemukan.';
    }
}

// Proses pencarian tracking
elseif (isset($_POST['search_value']) && !empty(trim($_POST['search_value']))) {
    $search_value = trim($_POST['search_value']);
    $search_type = $_POST['search_type'] ?? 'kode';

    $search_escaped = mysqli_real_escape_string($koneksi, $search_value);

    // Build query berdasarkan tipe pencarian
    switch ($search_type) {
        // case 'nama':
        //     $query = "SELECT p.*, j.nama as jenis_izin_nama 
        //               FROM pengajuan_izin p 
        //               JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
        //               WHERE p.nama_pemohon LIKE '%$search_escaped%'
        //               ORDER BY p.tanggal_pengajuan DESC";
        //     break;

        case 'whatsapp':
            // Bersihkan nomor WhatsApp dari karakter non-digit
            $clean_number = preg_replace('/[^0-9]/', '', $search_value);
            $query = "SELECT p.*, j.nama as jenis_izin_nama 
                      FROM pengajuan_izin p 
                      JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
                      WHERE p.no_wa LIKE '%$clean_number%'
                      ORDER BY p.tanggal_pengajuan DESC";
            break;

        case 'email':
            $query = "SELECT p.*, j.nama as jenis_izin_nama 
                      FROM pengajuan_izin p 
                      JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
                      WHERE p.email LIKE '%$search_escaped%'
                      ORDER BY p.tanggal_pengajuan DESC";
            break;

        default:
            // kode pengajuan
            $query = "SELECT p.*, j.nama as jenis_izin_nama 
                      FROM pengajuan_izin p 
                      JOIN jenis_izin j ON p.jenis_perizinan_id = j.id 
                      WHERE p.kode_pengajuan LIKE '%$search_escaped%'
                      ORDER BY p.tanggal_pengajuan DESC";
            break;
    }

    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Semua pencarian menghasilkan multiple results
        while ($row = mysqli_fetch_assoc($result)) {
            $tracking_results[] = $row;
        }
    } else {
        $search_labels = [
            'kode' => 'kode pengajuan',
            // 'nama' => 'nama pemohon',
            'whatsapp' => 'nomor WhatsApp',
            'email' => 'email',
        ];
        $error_message = 'Data tidak ditemukan dengan ' . $search_labels[$search_type] . ' yang Anda masukkan.';
    }
}

// Function untuk mendapatkan badge status
function getStatusBadge($status)
{
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'diterima':
            return '<span class="badge bg-success">Diterima</span>';
        case 'ditolak':
            return '<span class="badge bg-danger">Ditolak</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

// Function untuk safely get array value
function safeGet($array, $key, $default = '')
{
    return isset($array[$key]) && $array[$key] !== null ? $array[$key] : $default;
}

// Definisikan warna tema
$primary_color = '#0d6efd';
$success_color = '#008374';
$warning_color = '#ffc107';
?>

<main class="main">
    <!-- Page Title -->
    <div class="page-title mt-5" data-aos="fade" style="background-color: #008374; color: white; padding: 60px 0;">
        <div class="container">
            <nav class="breadcrumbs mb-3">
                <ol>
                    <li><a href="<?= $base_url ?>">Beranda</a></li>
                    <li><a href="<?= $base_url ?>/perizinan/index.php">Perizinan</a></li>
                    <li class="current">Tracking Status</li>
                </ol>
            </nav>
            <h1>Tracking Status Pengajuan</h1>
            <p class="lead">Pantau status pengajuan perizinan Anda dengan kode pengajuan atau data lainnya</p>
        </div>
    </div>

    <section class="section" style="padding: 60px 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Tampilkan form pencarian hanya jika bukan detail view -->
                    <?php if (!$tracking_detail): ?>
                    <!-- Form Tracking -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color: <?= $success_color ?>;">
                            <h5 class="mb-0 text-white"><i class="bi bi-search me-2"></i>Cari Status Pengajuan</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="search_type" class="form-label">Cari Berdasarkan</label>
                                        <select class="form-select" id="search_type" name="search_type"
                                            onchange="updatePlaceholder()">
                                            <option value="kode" <?= $search_type == 'kode' ? 'selected' : '' ?>>Kode
                                                Pengajuan</option>
                                            <!-- <option value="nama" <?= $search_type == 'nama' ? 'selected' : '' ?>>Nama
                                                Pemohon</option> -->
                                            <option value="whatsapp"
                                                <?= $search_type == 'whatsapp' ? 'selected' : '' ?>>No. WhatsApp
                                            </option>
                                            <option value="email" <?= $search_type == 'email' ? 'selected' : '' ?>>
                                                Email</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="search_value" class="form-label">Kata Kunci Pencarian</label>
                                        <input type="text" class="form-control" id="search_value" name="search_value"
                                            placeholder="Masukkan kata kunci pencarian"
                                            value="<?= htmlspecialchars($search_value) ?>" required>
                                        <div class="form-text" id="search_help">
                                            Masukkan kata kunci sesuai dengan tipe pencarian yang dipilih
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn w-100"
                                            style="background-color: <?= $success_color ?>; color: white;">
                                            <i class="bi bi-search me-2"></i>Cari
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $error_message ?>
                        <?php if (!$tracking_detail): ?>
                        <hr>
                        <small>
                            <strong>Tips Pencarian:</strong><br>
                            • Untuk kode pengajuan: masukkan kode lengkap atau sebagian kode (contoh:
                            BBKSDA20241201)<br>
                            • Untuk nama: masukkan nama lengkap atau sebagian nama<br>
                            • Untuk WhatsApp: masukkan nomor dengan atau tanpa kode negara<br>
                            • Untuk email: masukkan alamat email lengkap atau sebagian
                        </small>
                        <?php endif; ?>

                        <?php if ($tracking_detail): ?>
                        <div class="mt-3">
                            <a href="<?= $base_url ?>/perizinan/tracking.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Pencarian
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Hasil Tracking - Multiple Results -->
                    <?php if (!empty($tracking_results)): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color: <?= $success_color ?>;">
                            <h5 class="mb-0 text-white">
                                <i class="bi bi-list-check me-2"></i>
                                Ditemukan <?= count($tracking_results) ?> Pengajuan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Pengajuan</th>
                                            <th>Jenis Perizinan</th>
                                            <th>Nama Pemohon</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($tracking_results as $result): 
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <strong
                                                    class="text-primary"><?= htmlspecialchars($result['kode_pengajuan']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($result['jenis_izin_nama']) ?></td>
                                            <td><?= htmlspecialchars($result['nama_pemohon']) ?></td>
                                            <td><?= getStatusBadge($result['status_pengajuan']) ?></td>
                                            <td>
                                                <small><?= date('d/m/Y H:i', strtotime($result['tanggal_pengajuan'])) ?></small>
                                            </td>
                                            <td>
                                                <a href="<?= $base_url ?>/tracking.php?detail_kode=<?= urlencode($result['kode_pengajuan']) ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Ditemukan <?= count($tracking_results) ?> pengajuan.</strong>
                                Klik tombol "Detail" untuk melihat informasi lengkap masing-masing pengajuan.
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Detail View -->
                    <?php if ($tracking_detail): ?>
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background-color: <?= $success_color ?>;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="bi bi-file-earmark-check me-2"></i>Detail Pengajuan
                                </h5>
                                <a href="<?= $base_url ?>/tracking.php" class="btn btn-sm btn-outline-light">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Utama -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Kode Pengajuan:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-info fs-6"><?= htmlspecialchars(safeGet($tracking_detail, 'kode_pengajuan')) ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Perizinan:</strong></td>
                                            <td><?= htmlspecialchars(safeGet($tracking_detail, 'jenis_izin_nama')) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Pemohon:</strong></td>
                                            <td><?= htmlspecialchars(safeGet($tracking_detail, 'nama_pemohon')) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars(safeGet($tracking_detail, 'email')) ?>"
                                                    class="text-decoration-none">
                                                    <i class="bi bi-envelope text-primary"></i>
                                                    <?= htmlspecialchars(safeGet($tracking_detail, 'email')) ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. WhatsApp:</strong></td>
                                            <td>
                                                <a href="https://wa.me/<?= safeGet($tracking_detail, 'no_wa') ?>"
                                                    target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-whatsapp text-success"></i>
                                                    <?= htmlspecialchars(safeGet($tracking_detail, 'no_wa')) ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php if (!empty(safeGet($tracking_detail, 'instansi'))): ?>
                                        <tr>
                                            <td><strong>Instansi:</strong></td>
                                            <td><?= htmlspecialchars(safeGet($tracking_detail, 'instansi')) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Status:</strong></td>
                                            <td><?= getStatusBadge(safeGet($tracking_detail, 'status_pengajuan', 'pending')) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Pengajuan:</strong></td>
                                            <td>
                                                <?php
                                                $tanggal_pengajuan = safeGet($tracking_detail, 'tanggal_pengajuan');
                                                if (!empty($tanggal_pengajuan)) {
                                                    echo date('d F Y H:i', strtotime($tanggal_pengajuan)) . ' WIB';
                                                } else {
                                                    echo 'Tidak tersedia';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php 
                                        $updated_at = safeGet($tracking_detail, 'updated_at');
                                        if (!empty($updated_at) && $updated_at !== safeGet($tracking_detail, 'created_at')): 
                                        ?>
                                        <tr>
                                            <td><strong>Terakhir Update:</strong></td>
                                            <td><?= date('d F Y H:i', strtotime($updated_at)) ?> WIB</td>
                                        </tr>
                                        <?php endif; ?>

                                        <?php 
                                        $catatan = safeGet($tracking_detail, 'catatan');
                                        if (!empty($catatan)): 
                                        ?>
                                        <tr>
                                            <td><strong>Catatan:</strong></td>
                                            <td>
                                                <div class="alert alert-info p-2 mb-0">
                                                    <small><?= nl2br(htmlspecialchars($catatan)) ?></small>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <?php 
                            $alamat = safeGet($tracking_detail, 'alamat');
                            if (!empty($alamat)): 
                            ?>
                            <div class="mt-3">
                                <strong>Alamat Lengkap:</strong>
                                <div class="ms-3 mt-2">
                                    <div class="alert alert-light p-3 mb-0">
                                        <?= nl2br(htmlspecialchars($alamat)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Keperluan -->
                            <?php 
                            $keperluan = safeGet($tracking_detail, 'keperluan');
                            if (!empty($keperluan)): 
                            ?>
                            <div class="mt-3">
                                <strong>Keperluan/Tujuan Pengajuan:</strong>
                                <div class="ms-3 mt-2">
                                    <div class="alert alert-light p-3 mb-0">
                                        <?= nl2br(htmlspecialchars($keperluan)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Surat Permohonan -->
                            <?php 
                            $file_surat = safeGet($tracking_detail, 'file_surat_permohonan');
                            if (!empty($file_surat)): 
                            ?>
                            <div class="mt-3">
                                <strong>Surat Permohonan:</strong>
                                <div class="mt-2">
                                    <a href="<?= $base_url ?>/uploads/surat-permohonan/<?= htmlspecialchars($file_surat) ?>"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download me-1"></i><?= htmlspecialchars($file_surat) ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <h6><i class="bi bi-graph-up me-2"></i>Progress Pengajuan:</h6>
                                <?php
                                $status = safeGet($tracking_detail, 'status_pengajuan', 'pending');
                                $progress = 0;
                                $progress_class = 'bg-warning';
                                $status_text = '';
                                
                                switch ($status) {
                                    case 'pending':
                                        $progress = 33;
                                        $progress_class = 'bg-warning';
                                        $status_text = 'Pengajuan sedang menunggu verifikasi';
                                        break;
                                    case 'diterima':
                                        $progress = 100;
                                        $progress_class = 'bg-success';
                                        $status_text = 'Pengajuan telah diterima dan disetujui';
                                        break;
                                    case 'ditolak':
                                        $progress = 100;
                                        $progress_class = 'bg-danger';
                                        $status_text = 'Pengajuan ditolak';
                                        break;
                                }
                                ?>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar <?= $progress_class ?>" role="progressbar"
                                        style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <strong><?= $progress ?>%</strong>
                                    </div>
                                </div>

                                <p class="text-muted mb-3">
                                    <i class="bi bi-info-circle me-1"></i><?= $status_text ?>
                                </p>

                                <!-- Timeline -->
                                <div class="timeline">
                                    <div class="timeline-item completed">
                                        <i class="bi bi-check-circle"></i>
                                        <span>Pengajuan Diterima</span>
                                    </div>
                                    <div
                                        class="timeline-item <?= in_array($status, ['diterima', 'ditolak']) ? 'completed' : '' ?>">
                                        <i class="bi bi-gear"></i>
                                        <span>Sedang Diverifikasi</span>
                                    </div>
                                    <div
                                        class="timeline-item <?= $status == 'diterima' ? 'completed' : ($status == 'ditolak' ? 'rejected' : '') ?>">
                                        <i class="bi bi-<?= $status == 'ditolak' ? 'x-circle' : 'check-circle' ?>"></i>
                                        <span><?= $status == 'ditolak' ? 'Ditolak' : 'Selesai' ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 text-center">
                                <button type="button" class="btn"
                                    style="background-color: <?= $success_color ?>; color: white;"
                                    onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i>Cetak Detail
                                </button>
                                <a href="<?= $base_url ?>/perizinan/index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Perizinan
                                </a>
                                <a href="<?= $base_url ?>/tracking.php" class="btn btn-outline-primary">
                                    <i class="bi bi-search me-2"></i>Pencarian Baru
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Info Tambahan - hanya tampil jika bukan detail view -->
                    <?php if (!$tracking_detail): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6><i class="bi bi-info-circle me-2"></i>Cara Menggunakan Pencarian:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                <li><strong>Kode Pengajuan:</strong> Masukkan kode lengkap atau sebagian
                                                    (contoh: BBKSDA20241201)</li>
                                                <li><strong>Nama Pemohon:</strong> Masukkan nama lengkap atau sebagian
                                                    nama</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                <li><strong>No. WhatsApp:</strong> Masukkan nomor dengan/tanpa kode
                                                    negara</li>
                                                <li><strong>Email:</strong> Masukkan alamat email lengkap atau sebagian
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-telephone me-1"></i> Butuh bantuan? Hubungi (0761) 563065 atau
                                        <i class="bi bi-envelope me-1"></i> bbksdariau@menlhk.go.id
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
</main>

<style>
    .timeline {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .timeline-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        background: white;
        padding: 0 10px;
    }

    .timeline-item i {
        font-size: 30px;
        color: #dee2e6;
        margin-bottom: 5px;
    }

    .timeline-item.completed i {
        color: #198754;
    }

    .timeline-item.rejected i {
        color: #dc3545;
    }

    .timeline-item span {
        font-size: 12px;
        text-align: center;
        color: #6c757d;
    }

    .timeline-item.completed span,
    .timeline-item.rejected span {
        color: #495057;
        font-weight: 600;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 131, 116, 0.1);
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
        font-size: 14px;
        line-height: 25px;
    }

    @media print {

        .btn,
        .breadcrumbs,
        .page-title,
        .card-header {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .alert {
            border: 1px solid #ddd !important;
            background: #f8f9fa !important;
        }
    }
</style>

<script>
    function updatePlaceholder() {
        const searchType = document.getElementById('search_type').value;
        const searchInput = document.getElementById('search_value');
        const helpText = document.getElementById('search_help');

        const placeholders = {
            'kode': 'Masukkan kode pengajuan (contoh: BBKSDA20241201)',
            'nama': 'Masukkan nama pemohon (contoh: John Doe)',
            'whatsapp': 'Masukkan nomor WhatsApp (contoh: 08123456789)',
            'email': 'Masukkan alamat email (contoh: john@email.com)'
        };

        const helpTexts = {
            'kode': 'Masukkan kode pengajuan lengkap atau sebagian kode',
            'nama': 'Masukkan nama lengkap atau sebagian nama pemohon',
            'whatsapp': 'Masukkan nomor WhatsApp dengan atau tanpa kode negara',
            'email': 'Masukkan alamat email yang terdaftar saat pengajuan'
        };

        searchInput.placeholder = placeholders[searchType];
        helpText.textContent = helpTexts[searchType];
        searchInput.value = '';
    }

    // Initialize placeholder on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('search_type')) {
            updatePlaceholder();
        }
    });
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
