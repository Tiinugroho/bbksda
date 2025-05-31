<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/check_login.php'; // Pastikan user sudah login

$current_user = getLoggedUser();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BBKSDA Admin - Dashboard</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= $base_url ?>/public/adminlte/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?= $base_url ?>/public/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $base_url ?>/public/adminlte/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="<?= $base_url ?>/public/adminlte/dist/css/skins/_all-skins.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= $base_url ?>/public/adminlte/plugins/datatables/dataTables.bootstrap.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="<?= $base_url ?>/admin/dashboard.php" class="logo">
            <span class="logo-mini"><b>B</b>KS</span>
            <span class="logo-lg"><b>BBKSDA</b> Admin</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $base_url ?>/public/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= htmlspecialchars($current_user['nama'] ?? $current_user['name']) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= $base_url ?>/public/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                <p>
                                    <?= htmlspecialchars($current_user['nama'] ?? $current_user['name']) ?>
                                    <small><?= ucfirst(str_replace('-', ' ', $current_user['role'])) ?> - <?= htmlspecialchars($current_user['email'] ?? '') ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= $base_url ?>/admin/profile.php" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= $base_url ?>/logout.php" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $base_url ?>/public/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= htmlspecialchars($current_user['nama'] ?? $current_user['username']) ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            
            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">MENU UTAMA</li>
                
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>/admin/dashboard.php">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="treeview <?= in_array(basename($_SERVER['PHP_SELF']), ['pengajuan.php', 'pengajuan-detail.php']) ? 'active' : '' ?>">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Pengajuan Perizinan</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="<?= $base_url ?>/admin/pengajuan.php"><i class="fa fa-circle-o"></i> Semua Pengajuan</a></li>
                        <li><a href="<?= $base_url ?>/admin/pengajuan.php?status=diajukan"><i class="fa fa-circle-o"></i> Pengajuan Baru</a></li>
                        <li><a href="<?= $base_url ?>/admin/pengajuan.php?status=diproses"><i class="fa fa-circle-o"></i> Sedang Diproses</a></li>
                        <li><a href="<?= $base_url ?>/admin/pengajuan.php?status=disetujui"><i class="fa fa-circle-o"></i> Disetujui</a></li>
                        <li><a href="<?= $base_url ?>/admin/pengajuan.php?status=ditolak"><i class="fa fa-circle-o"></i> Ditolak</a></li>
                    </ul>
                </li>
                
                <?php if (isAdmin()): ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-gear"></i>
                        <span>Pengaturan</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="<?= $base_url ?>/admin/jenis-izin.php"><i class="fa fa-circle-o"></i> Jenis Perizinan</a></li>
                        <?php if (isSuperAdmin()): ?>
                        <li><a href="<?= $base_url ?>/admin/users.php"><i class="fa fa-circle-o"></i> Kelola User</a></li>
                        <li><a href="<?= $base_url ?>/admin/settings.php"><i class="fa fa-circle-o"></i> Pengaturan Sistem</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <li>
                    <a href="<?= $base_url ?>">
                        <i class="fa fa-globe"></i> <span>Lihat Website</span>
                    </a>
                </li>
            </ul>
        </section>
    </aside>
