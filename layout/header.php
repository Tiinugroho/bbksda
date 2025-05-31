<?php
$base_url = 'http://localhost/bbksda';

// Pastikan koneksi database ada
if (!file_exists(__DIR__ . '/../koneksi.php')) {
    die('File koneksi.php tidak ditemukan!');
}

include __DIR__ . '/../koneksi.php';

// Cek koneksi database
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Ambil data jenis izin dari database untuk dropdown
$query_izin = 'SELECT slug, nama FROM jenis_izin WHERE is_aktif = 1 ORDER BY nama ASC';
$result_izin = mysqli_query($koneksi, $query_izin);

// Cek apakah query berhasil
if (!$result_izin) {
    echo 'Error query: ' . mysqli_error($koneksi);
    $result_izin = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>BBKSDA Riau - Balai Besar Konservasi Sumber Daya Alam</title>
    <meta name="description" content="BBKSDA Riau - Menjaga kelestarian alam dan keanekaragaman hayati">
    <meta name="keywords" content="BBKSDA, Riau, Konservasi, Perizinan, Lingkungan">

    <!-- Favicons -->
    <link href="<?= $base_url ?>/public/template/assets/img/favicon.png" rel="icon">
    <link href="<?= $base_url ?>/public/template/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?= $base_url ?>/public/template/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/public/template/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $base_url ?>/public/template/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="<?= $base_url ?>/public/template/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/public/template/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="<?= $base_url ?>/public/template/assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

    <header id="header" class="header fixed-top">

        <div class="topbar d-flex align-items-center">
            <div class="container d-flex justify-content-center justify-content-md-between">
                <div class="contact-info d-flex align-items-center">
                    <i class="bi bi-envelope d-flex align-items-center">
                        <a href="mailto:bbksdariau@menlhk.go.id">bbksdariau@menlhk.go.id</a>
                    </i>
                    <i class="bi bi-phone d-flex align-items-center ms-4">
                        <span>(0761) 563065</span>
                    </i>
                </div>
                <div class="social-links d-none d-md-flex align-items-center">
                    <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div><!-- End Top Bar -->

        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="<?= $base_url ?>" class="logo d-flex align-items-center">
                    <h1 class="sitename">BBKSDA Riau</h1>
                    <span>.</span>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="<?= $base_url ?>/index.php" class="active">Beranda</a></li>
                        <li class="dropdown">
                            <a href="#"><span>Perizinan</span>
                                <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>
                            <ul>
                                <?php if ($result_izin && mysqli_num_rows($result_izin) > 0): ?>
                                <?php while ($izin = mysqli_fetch_assoc($result_izin)): ?>
                                <li>
                                    <a
                                        href="<?= $base_url ?>/perizinan/detail.php?slug=<?= urlencode($izin['slug']) ?>">
                                        <?= htmlspecialchars($izin['nama']) ?>
                                    </a>
                                </li>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <li><a href="#">Tidak ada jenis izin tersedia</a></li>
                                <?php endif; ?>

                                <!-- Tambahan menu untuk halaman umum perizinan -->
                                <li>
                                    <hr>
                                </li>
                                <li><a href="<?= $base_url ?>/perizinan/index.php">Semua Jenis Perizinan</a></li>
                                <li><a href="<?= $base_url ?>/perizinan/cara-pengajuan.php">Cara Pengajuan</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= $base_url ?>/tracking.php">Cari Surat</a></li>
                        <li><a href="<?= $base_url ?>/#about">Tentang</a></li>
                        <li><a href="<?= $base_url ?>/#contact">Kontak</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>
            </div>
        </div>
    </header>
