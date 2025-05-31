<?php
// Debug mode - aktifkan untuk melihat masalah
$debug = false; // Ubah ke true untuk debugging

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$base_url = 'http://localhost/bbksda';

// Cek apakah file koneksi ada
$koneksi_file = __DIR__ . '/../koneksi.php';
if (!file_exists($koneksi_file)) {
    die('Error: File koneksi.php tidak ditemukan di: ' . $koneksi_file);
}

include $koneksi_file;

// Cek koneksi database
if (!$koneksi) {
    die('Error: Koneksi database gagal - ' . mysqli_connect_error());
}

// Ambil slug dari URL dan decode
$slug_raw = $_GET['slug'] ?? '';
$slug = urldecode(trim($slug_raw));

if (empty($slug)) {
    header('Location: ' . $base_url . '/perizinan/index.php?error=slug_kosong');
    exit();
}

// Escape slug untuk keamanan
$slug_escaped = mysqli_real_escape_string($koneksi, $slug);

// Query dengan debug
$query = "SELECT * FROM jenis_izin WHERE slug = '$slug_escaped' AND is_aktif = 1";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die('Error query: ' . mysqli_error($koneksi));
}

// Ambil data izin
$izin = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan, redirect
if (!$izin) {
    header('Location: ' . $base_url . '/perizinan/index.php?error=data_tidak_ditemukan&slug=' . urlencode($slug));
    exit();
}

// PENTING: Simpan data izin dalam variabel terpisah untuk menghindari overwrite
$izin_id = $izin['id'];
$deskripsi_data = isset($izin['deskripsi']) ? $izin['deskripsi'] : '';
$tata_cara_data = isset($izin['tata_cara']) ? $izin['tata_cara'] : '';
$syarat_data = isset($izin['syarat']) ? $izin['syarat'] : '';
$nama_data = isset($izin['nama']) ? $izin['nama'] : 'Detail Perizinan';
$slug_data = isset($izin['slug']) ? $izin['slug'] : $slug;
$created_at_data = isset($izin['created_at']) ? $izin['created_at'] : '';
$updated_at_data = isset($izin['updated_at']) ? $izin['updated_at'] : '';

// Include header setelah semua validasi
include __DIR__ . '/../layout/header.php';

// Definisikan warna tema
$primary_color = '#0d6efd';
$success_color = '#008374';
$warning_color = '#ffc107';
?>

<main class="main">
    <!-- Page Title -->
    <div class="page-title mt-5" data-aos="fade" style="background-color: <?= $success_color ?>; color: white; padding: 60px 0;">
        <div class="container">
            <nav class="breadcrumbs mb-3">
                <ol>
                    <li><a href="<?= $base_url ?>">Beranda</a></li>
                    <li><a href="<?= $base_url ?>/perizinan/index.php">Perizinan</a></li>
                    <li class="current"><?= htmlspecialchars($nama_data) ?></li>
                </ol>
            </nav>
            <h1><?= htmlspecialchars($nama_data) ?></h1>
            <p class="lead">Detail informasi dan persyaratan perizinan</p>
        </div>
    </div>

    <section class="section" style="padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">

                    <!-- Deskripsi -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header" style="background-color: <?= $primary_color ?>; color: white;">
                            <h5 class="mb-0 text-white"><i class="bi bi-info-circle me-2"></i>Deskripsi</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($deskripsi_data)): ?>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($deskripsi_data)) ?></p>
                            <?php else: ?>
                                <p class="mb-0 text-muted">Deskripsi belum tersedia untuk jenis perizinan ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tata Cara -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header" style="background-color: <?= $success_color ?>; color: #212529;">
                            <h5 class="mb-0 text-white"><i class="bi bi-list-ol me-2"></i>Tata Cara Pengajuan</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tata_cara_data)): ?>
                                <div class="tata-cara-content">
                                    <?= nl2br(htmlspecialchars($tata_cara_data)) ?>
                                </div>
                            <?php else: ?>
                                <p class="mb-0 text-muted">Tata cara pengajuan belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Syarat -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header" style="background-color: <?= $warning_color ?>; color: #212529;">
                            <h5 class="mb-0"><i class="bi bi-check-square me-2"></i>Persyaratan</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($syarat_data)): ?>
                                <div class="syarat-content">
                                    <?= nl2br(htmlspecialchars($syarat_data)) ?>
                                </div>
                            <?php else: ?>
                                <p class="mb-0 text-muted">Persyaratan belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-sidebar">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-success" 
                                        style="background-color: <?= $success_color ?>; color: #fff;"
                                        data-bs-toggle="modal" data-bs-target="#modalPengajuan">
                                    <i class="bi bi-plus-circle me-2"></i>Ajukan Perizinan
                                </button>
                                
                                <a href="<?= $base_url ?>/tracking.php" class="btn btn-outline-primary">
                                    <i class="bi bi-search me-2"></i>Tracking Status
                                </a>
                                
                                <a href="<?= $base_url ?>/#contact" class="btn btn-outline-secondary">
                                    <i class="bi bi-question-circle me-2"></i>Tanya Jawab
                                </a>
                            </div>

                            <hr>

                            <h6>Informasi Kontak</h6>
                            <div class="contact-info p-3 bg-light rounded">
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><small>(0761) 563065</small></p>
                                <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i><small>bbksdariau@menlhk.go.id</small></p>
                                <p class="mb-0"><i class="bi bi-clock me-2 text-primary"></i><small>Senin-Jumat: 08:00-16:00 WIB</small></p>
                            </div>

                            <hr>

                            <h6>Informasi Perizinan</h6>
                            <div class="info-detail">
                                <?php if (!empty($created_at_data)): ?>
                                <p class="mb-2">
                                    <strong>Dibuat:</strong><br><small><?= date('d F Y', strtotime($created_at_data)) ?></small>
                                </p>
                                <?php endif; ?>
                                <?php if (!empty($updated_at_data)): ?>
                                <p class="mb-0">
                                    <strong>Diperbarui:</strong><br><small><?= date('d F Y', strtotime($updated_at_data)) ?></small>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Modal Pengajuan Perizinan -->
    <div class="modal fade" id="modalPengajuan" tabindex="-1" aria-labelledby="modalPengajuanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: <?= $success_color ?>; color: white;">
                    <h5 class="modal-title" id="modalPengajuanLabel">
                        <i class="bi bi-file-earmark-plus me-2"></i>Ajukan Perizinan: <?= htmlspecialchars($nama_data) ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- Form dengan method POST langsung ke proses-pengajuan.php -->
                <form method="POST" action="<?= $base_url ?>/perizinan/proses-pengajuan.php" enctype="multipart/form-data" id="formPengajuan">
                    <div class="modal-body">
                        <input type="hidden" name="jenis_perizinan_id" value="<?= $izin_id ?>">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($slug_data) ?>">
                        
                        <!-- Alert untuk pesan -->
                        <div id="alertMessage" class="alert d-none" role="alert"></div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pemohon" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pemohon" name="nama_pemohon" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_wa" class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="no_wa" name="no_wa" 
                                       placeholder="08xxxxxxxxxx" required>
                                <div class="form-text">Contoh: 08123456789</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instansi" class="form-label">Instansi/Organisasi</label>
                                <input type="text" class="form-control" id="instansi" name="instansi" 
                                       placeholder="Nama instansi atau organisasi">
                                <div class="form-text">Opsional - jika mewakili instansi</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required 
                                      placeholder="Masukkan alamat lengkap Anda"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan/Tujuan Pengajuan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keperluan" name="keperluan" rows="4" required 
                                      placeholder="Jelaskan secara detail keperluan atau tujuan pengajuan perizinan ini"></textarea>
                            <div class="form-text">Jelaskan dengan detail untuk mempercepat proses verifikasi</div>
                        </div>

                        <div class="mb-3">
                            <label for="file_surat_permohonan" class="form-label">Surat Permohonan <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file_surat_permohonan" name="file_surat_permohonan" 
                                   accept=".pdf,.doc,.docx" required>
                            <div class="form-text">
                                <strong>Wajib:</strong> Upload surat permohonan resmi dalam format PDF, DOC, atau DOCX (Max: 5MB)
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Catatan Penting:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pastikan semua data yang diisi sudah benar</li>
                                <li>Surat permohonan harus ditandatangani dan bermaterai</li>
                                <li>Proses verifikasi membutuhkan waktu 3-7 hari kerja</li>
                                <li>Anda akan mendapat kode pengajuan untuk tracking status</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success" id="btnSubmit">
                            <i class="bi bi-send me-2"></i>Ajukan Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    <div class="modal fade" id="modalSuccess" tabindex="-1" aria-labelledby="modalSuccessLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalSuccessLabel">
                        <i class="bi bi-check-circle me-2"></i>Pengajuan Berhasil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h5>Permohonan Anda Telah Diterima!</h5>
                    <p class="mb-3">Kode Pengajuan Anda:</p>
                    <div class="alert alert-success">
                        <h4 id="kodePengajuan" class="mb-0"></h4>
                    </div>
                    <p class="text-muted">Simpan kode ini untuk memantau status permohonan Anda di halaman tracking.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" onclick="copyKode()">
                        <i class="bi bi-clipboard me-2"></i>Salin Kode
                    </button>
                    <a href="<?= $base_url ?>/tracking.php" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Tracking Status
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .sticky-sidebar {
        position: sticky;
        top: 150px;
    }

    @media (max-width: 991.98px) {
        .sticky-sidebar {
            position: relative;
            top: 0;
        }
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .form-control:focus {
        border-color: <?= $success_color ?>;
        box-shadow: 0 0 0 0.2rem rgba(0, 131, 116, 0.25);
    }
</style>

<script>
// Minimal JavaScript - hanya untuk validasi dan UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPengajuan');
    const btnSubmit = document.getElementById('btnSubmit');
    
    // Format nomor WhatsApp
    document.getElementById('no_wa').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });

    // Validasi file upload
    document.getElementById('file_surat_permohonan').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                e.target.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                e.target.value = '';
                return;
            }
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Memproses...';
    });
});

function copyKode() {
    const kodePengajuan = document.getElementById('kodePengajuan').textContent;
    navigator.clipboard.writeText(kodePengajuan).then(() => {
        alert('Kode pengajuan berhasil disalin!');
    }).catch(() => {
        // Fallback untuk browser lama
        const textArea = document.createElement('textarea');
        textArea.value = kodePengajuan;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Kode pengajuan berhasil disalin!');
    });
}

// Tampilkan modal success jika ada parameter success
<?php if (isset($_GET['success']) && isset($_GET['kode'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('kodePengajuan').textContent = '<?= htmlspecialchars($_GET['kode']) ?>';
    const modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
    modalSuccess.show();
});
<?php endif; ?>
</script>

<?php
$footer_file = __DIR__ . '/../layout/footer.php';
if (file_exists($footer_file)) {
    include $footer_file;
} else {
    echo "<!-- Footer file not found: $footer_file -->";
    echo '</body></html>';
}
?>
