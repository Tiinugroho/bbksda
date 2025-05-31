<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/header.php';
// Definisikan warna tema
$primary_color = '#0d6efd';
$success_color = '#008374';
$warning_color = '#ffc107';
?>
?>

<main class="main">
    <!-- Page Title -->
    <div class="page-title mt-5" data-aos="fade" style="background-color: <?= $success_color ?>; color: white; padding: 60px 0;">
        <div class="container">
            <nav class="breadcrumbs mb-3">
                <ol>
                    <li><a href="<?= $base_url ?>">Beranda</a></li>
                    <li><a href="<?= $base_url ?>/perizinan/index.php">Perizinan</a></li>
                    <li class="current">Cara Pengajuan</li>
                </ol>
            </nav>
            <h1>Cara Pengajuan Perizinan</h1>
            <p class="lead">Panduan lengkap untuk mengajukan perizinan di BBKSDA Riau</p>
        </div>
    </div>

    <!-- Cara Pengajuan Section -->
    <section class="section" style="padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-body p-5">
                            <h3 class="mb-4">Langkah-langkah Pengajuan</h3>
                            
                            <div class="step-item mb-4">
                                <div class="d-flex">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        1
                                    </div>
                                    <div>
                                        <h5>Pilih Jenis Perizinan</h5>
                                        <p>Tentukan jenis perizinan yang sesuai dengan kebutuhan Anda dari menu dropdown atau halaman perizinan.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item mb-4">
                                <div class="d-flex">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        2
                                    </div>
                                    <div>
                                        <h5>Pelajari Persyaratan</h5>
                                        <p>Baca dengan teliti persyaratan dan tata cara pengajuan pada halaman detail perizinan.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item mb-4">
                                <div class="d-flex">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        3
                                    </div>
                                    <div>
                                        <h5>Siapkan Dokumen</h5>
                                        <p>Lengkapi semua dokumen yang diperlukan sesuai dengan daftar persyaratan.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item mb-4">
                                <div class="d-flex">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        4
                                    </div>
                                    <div>
                                        <h5>Ajukan Permohonan</h5>
                                        <p>Klik tombol "Ajukan Perizinan" dan isi formulir dengan lengkap dan benar.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item mb-4">
                                <div class="d-flex">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        5
                                    </div>
                                    <div>
                                        <h5>Tunggu Proses Verifikasi</h5>
                                        <p>Tim kami akan memverifikasi dokumen dan memberikan update status melalui email atau SMS.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Catatan:</strong> Proses verifikasi membutuhkan waktu 3-7 hari kerja tergantung jenis perizinan.
                            </div>

                            <div class="text-center mt-4">
                                <a href="<?= $base_url ?>/perizinan/index.php" class="btn" style="background-color: <?= $success_color ?>; color: white;">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Perizinan
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
