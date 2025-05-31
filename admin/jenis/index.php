<?php
$base_url = 'http://localhost/bbksda'; // ganti sesuai folder kamu
include __DIR__ . '/../layout/headerTable.php';
include __DIR__ . '/../layout/sidebar.php';
include __DIR__ . '/../../koneksi.php'; // pastikan file koneksi.php betul
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Daftar Jenis Izin</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="text-right" style="margin-bottom: 10px;">
                    <a href="<?= $base_url ?>/admin/jenis/create.php" class="btn btn-primary mb-3">Tambah</a>
                </div>
                <table id="example2" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Izin</th>
                            <th>Deskripsi</th>
                            <th>Tata Cara</th>
                            <th>Syarat</th>
                            <th>Tampilan Display</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <?php
                    
                    $query = 'SELECT * FROM jenis_izin';
                    $result = mysqli_query($koneksi, $query);
                    ?>

                    <tbody>
                        <?php 
                        $no = 1; // Inisialisasi nomor
                        while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++ ?></td> <!-- Tampilkan nomor dan increment -->
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td><?= htmlspecialchars($row['tata_cara']) ?></td>
                            <td><?= htmlspecialchars($row['syarat']) ?></td>
                            <td>
                                <?php if ($row['is_aktif'] == 1): ?>
                                <span class="label label-success">Aktif</span>
                                <?php else: ?>
                                <span class="label label-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="<?= $base_url ?>/admin/jenis/edit.php?id=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <br><br>
                                <a href="<?= $base_url ?>/admin/jenis/delete.php?id=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layout/footerTable.php'; ?>
