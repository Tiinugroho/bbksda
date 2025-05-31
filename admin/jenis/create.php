<?php
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Tambah Jenis Izin</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Form Tambah Jenis Izin</h3>
            </div>

            <form action="<?= $base_url ?>/admin/jenis/store.php" method="POST">
                <div class="box-body">
                    <div class="form-group">
                        <label for="nama_izin">Nama Izin</label>
                        <input type="text" name="nama_izin" class="form-control" id="nama_izin"
                            placeholder="Contoh: Izin Penangkaran" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3"
                            placeholder="Deskripsi singkat mengenai izin ini..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tata_cara">Tata Cara</label>
                        <textarea name="tata_cara" class="form-control" id="tata_cara" rows="3"
                            placeholder="Langkah-langkah atau prosedur izin..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="syarat">Syarat</label>
                        <textarea name="syarat" class="form-control" id="syarat" rows="3" placeholder="Persyaratan untuk izin ini..."
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="is_aktif">Status Aktif</label>
                        <select name="is_aktif" class="form-control" id="is_aktif" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= $base_url ?>/admin/jenis/index.php" class="btn btn-default">Kembali</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
