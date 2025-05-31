<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
include __DIR__ . '/../../koneksi.php';

// Ambil data berdasarkan ID
$id = $_GET['id'];
$query = "SELECT * FROM jenis_izin WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Jenis Izin</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Form Edit Jenis Izin</h3>
            </div>

            <form action="<?= $base_url ?>/admin/jenis/update.php" method="POST">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label for="nama_izin">Nama Izin</label>
                        <input type="text" name="nama_izin" class="form-control" id="nama_izin"
                            value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tata_cara">Tata Cara</label>
                        <textarea name="tata_cara" class="form-control" id="tata_cara" rows="3" required><?= htmlspecialchars($data['tata_cara']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="syarat">Syarat</label>
                        <textarea name="syarat" class="form-control" id="syarat" rows="3" required><?= htmlspecialchars($data['syarat']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="is_aktif">Status Aktif</label>
                        <select name="is_aktif" class="form-control" id="is_aktif" required>
                            <option value="1" <?= $data['is_aktif'] == 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $data['is_aktif'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?= $base_url ?>/admin/jenis/index.php" class="btn btn-default">Kembali</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
