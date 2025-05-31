<?php
$base_url = 'http://localhost/bbksda';

// Include file header & koneksi database
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../koneksi.php';

// Cek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Inisialisasi error message
$error_message = '';

// Cek apakah ada error di URL
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 'slug_kosong':
            $error_message = 'Slug perizinan tidak valid.';
            break;
        case 'data_tidak_ditemukan':
            $slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : '';
            $error_message = "Data perizinan dengan slug '$slug' tidak ditemukan.";
            break;
        default:
            $error_message = 'Terjadi kesalahan.';
    }
}

// Ambil semua jenis izin yang aktif
$query = "SELECT * FROM jenis_izin WHERE is_aktif = 1 ORDER BY nama ASC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Terjadi kesalahan dalam mengambil data: " . mysqli_error($koneksi));
}

// Definisikan warna tema
$primary_color = '#0d6efd';
$success_color = '#008374';
$warning_color = '#ffc107';
?>

<main class="main index-page">

    <!-- Page Title -->
    <div class="page-title mt-5" data-aos="fade" style="background-color: <?= $success_color ?>; color: white; padding: 60px 0;">
        <div class="container">
            <nav class="breadcrumbs mb-3">
                <ol>
                    <li><a href="<?= $base_url ?>/">Beranda</a></li>
                    <li class="current">Perizinan</li>
                </ol>
            </nav>
            <h1>Jenis Perizinan</h1>
            <p class="lead">Berbagai jenis perizinan yang dapat Anda ajukan untuk kegiatan di kawasan konservasi</p>
        </div>
    </div>

    <!-- Perizinan Section -->
    <section id="perizinan" class="perizinan section" style="padding: 60px 0;">
        <div class="container">

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row gy-4">

                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <?php while ($izin = mysqli_fetch_assoc($result)) : ?>
                        <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="100">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                        <?= htmlspecialchars($izin['nama']) ?>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?= htmlspecialchars(substr($izin['deskripsi'], 0, 150)) ?>
                                        <?= strlen($izin['deskripsi']) > 150 ? '...' : '' ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <a href="<?= $base_url ?>/perizinan/detail.php?slug=<?= urlencode($izin['slug']) ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Lihat Detail
                                        </a>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($izin['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                <?php else : ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            Saat ini belum ada jenis perizinan yang tersedia.
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Info Tambahan -->
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Butuh Bantuan?
                            </h5>
                            <p class="card-text">
                                Jika Anda memerlukan bantuan dalam proses pengajuan perizinan,
                                silakan hubungi layanan customer service kami.
                            </p>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="<?= $base_url ?>/#contact" class="btn btn-outline-primary">
                                    <i class="bi bi-telephone me-1"></i>Hubungi Kami
                                </a>
                                <a href="<?= $base_url ?>/perizinan/cara-pengajuan.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-book me-1"></i>Panduan Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>
