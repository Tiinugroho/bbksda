<?php
$base_url = 'http://localhost/bbksda';
// include __DIR__ . '/check_login.php';

// Mendapatkan path file halaman aktif
$current_page = $_SERVER['REQUEST_URI'];
?>


<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $base_url ?>/public/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= htmlspecialchars($current_user['nama'] ?? $current_user['name']) ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i
                            class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN MENU</li>
            <li class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/admin/dashboard.php">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li
                class="treeview <?= strpos($current_page, '/admin/jenis/index.php') !== false || strpos($current_page, '/admin/pengajuan/index.php') !== false ? 'active' : '' ?>">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Perizinan</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?= strpos($current_page, '/admin/jenis/index.php') !== false ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/jenis/index.php">
                            <i class="fa fa-circle-o"></i> Jenis
                        </a>
                    </li>
                    <li class="<?= strpos($current_page, '/admin/pengajuan/index.php') !== false ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/pengajuan/index.php">
                            <i class="fa fa-circle-o"></i> Pengajuan
                        </a>
                    </li>
                </ul>
            </li>

            <li class="header">SETTING</li>
            <li><a href="http://localhost/bbksda/admin/logout.php"><i class="fa fa-circle-o text-red"></i>
                    <span>Logout</span></a></li>
            <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>User</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
