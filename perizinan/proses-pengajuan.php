<?php
// Tambahkan error reporting dan logging untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_url = 'http://localhost/bbksda';

// Log file untuk debugging
$log_file = __DIR__ . '/../debug.log';

function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

writeLog("=== NEW PENGAJUAN REQUEST ===");
writeLog("Method: " . $_SERVER['REQUEST_METHOD']);
writeLog("POST data: " . print_r($_POST, true));
writeLog("FILES data: " . print_r($_FILES, true));

try {
    // Cek method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Include koneksi database
    $koneksi_file = __DIR__ . '/../koneksi.php';
    if (!file_exists($koneksi_file)) {
        throw new Exception('File koneksi.php tidak ditemukan di: ' . $koneksi_file);
    }
    
    include $koneksi_file;
    writeLog("Koneksi file included");

    // Cek koneksi database
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal: ' . mysqli_connect_error());
    }
    writeLog("Database connected successfully");

    // Cek apakah tabel ada
    $check_table = "SHOW TABLES LIKE 'pengajuan_izin'";
    $table_result = mysqli_query($koneksi, $check_table);
    if (mysqli_num_rows($table_result) == 0) {
        throw new Exception('Tabel pengajuan_izin tidak ditemukan. Silakan buat tabel terlebih dahulu.');
    }
    writeLog("Table pengajuan_izin exists");

    // Ambil dan validasi data dari form
    $jenis_perizinan_id = isset($_POST['jenis_perizinan_id']) ? (int)$_POST['jenis_perizinan_id'] : 0;
    $nama_pemohon = isset($_POST['nama_pemohon']) ? trim($_POST['nama_pemohon']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $no_wa = isset($_POST['no_wa']) ? trim($_POST['no_wa']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $instansi = isset($_POST['instansi']) ? trim($_POST['instansi']) : '';
    $keperluan = isset($_POST['keperluan']) ? trim($_POST['keperluan']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';

    writeLog("Data extracted - ID: $jenis_perizinan_id, Nama: $nama_pemohon, Email: $email");

    // Validasi input wajib
    if (empty($jenis_perizinan_id)) {
        throw new Exception('ID jenis perizinan tidak valid');
    }
    if (empty($nama_pemohon)) {
        throw new Exception('Nama pemohon harus diisi');
    }
    if (empty($email)) {
        throw new Exception('Email harus diisi');
    }
    if (empty($no_wa)) {
        throw new Exception('Nomor WhatsApp harus diisi');
    }
    if (empty($alamat)) {
        throw new Exception('Alamat harus diisi');
    }
    if (empty($keperluan)) {
        throw new Exception('Keperluan/tujuan pengajuan harus diisi');
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid');
    }

    // Validasi dan format nomor WhatsApp
    $no_wa = preg_replace('/[^0-9]/', '', $no_wa);
    if (strlen($no_wa) < 10) {
        throw new Exception('Nomor WhatsApp minimal 10 digit');
    }
    
    // Pastikan dimulai dengan 0 untuk format Indonesia
    if (substr($no_wa, 0, 1) !== '0') {
        $no_wa = '0' . $no_wa;
    }
    
    writeLog("Validation passed");

    // Generate kode pengajuan unik
    $kode_pengajuan = generateKodePengajuan($koneksi);
    writeLog("Generated kode pengajuan: $kode_pengajuan");

    // Validasi dan handle upload file surat permohonan (WAJIB)
    if (!isset($_FILES['file_surat_permohonan']) || $_FILES['file_surat_permohonan']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('Surat permohonan harus diupload');
    }

    $file_surat_permohonan = handleFileUpload($_FILES['file_surat_permohonan']);
    if ($file_surat_permohonan === false) {
        throw new Exception('Gagal mengupload surat permohonan');
    }
    
    writeLog("File uploaded: $file_surat_permohonan");

    // Escape data untuk keamanan
    $nama_pemohon = mysqli_real_escape_string($koneksi, $nama_pemohon);
    $email = mysqli_real_escape_string($koneksi, $email);
    $no_wa = mysqli_real_escape_string($koneksi, $no_wa);
    $alamat = mysqli_real_escape_string($koneksi, $alamat);
    $instansi = mysqli_real_escape_string($koneksi, $instansi);
    $keperluan = mysqli_real_escape_string($koneksi, $keperluan);
    $file_surat_permohonan = mysqli_real_escape_string($koneksi, $file_surat_permohonan);
    $kode_pengajuan = mysqli_real_escape_string($koneksi, $kode_pengajuan);

    // Insert ke database dengan kode_pengajuan
    $query = "INSERT INTO pengajuan_izin 
              (jenis_perizinan_id, kode_pengajuan, nama_pemohon, email, no_wa, alamat, instansi, keperluan, file_surat_permohonan, status_pengajuan, tanggal_pengajuan, created_at) 
              VALUES 
              ($jenis_perizinan_id, '$kode_pengajuan', '$nama_pemohon', '$email', '$no_wa', '$alamat', '$instansi', '$keperluan', '$file_surat_permohonan', 'pending', NOW(), NOW())";

    writeLog("Query: $query");

    if (mysqli_query($koneksi, $query)) {
        $pengajuan_id = mysqli_insert_id($koneksi);
        writeLog("Data inserted successfully with ID: $pengajuan_id and kode: $kode_pengajuan");
        
        // Redirect ke halaman detail dengan parameter success dan kode pengajuan
        $redirect_url = $base_url . '/perizinan/detail.php?slug=' . urlencode($slug) . '&success=1&kode=' . urlencode($kode_pengajuan);
        header('Location: ' . $redirect_url);
        exit();
        
    } else {
        throw new Exception('Gagal menyimpan data: ' . mysqli_error($koneksi));
    }

} catch (Exception $e) {
    writeLog("ERROR: " . $e->getMessage());
    
    // Redirect kembali dengan error message
    $error_msg = urlencode($e->getMessage());
    $slug = isset($_POST['slug']) ? $_POST['slug'] : '';
    $redirect_url = $base_url . '/perizinan/detail.php?slug=' . urlencode($slug) . '&error=' . $error_msg;
    header('Location: ' . $redirect_url);
    exit();
}

if (isset($koneksi)) {
    mysqli_close($koneksi);
}

// Function untuk generate kode pengajuan unik
function generateKodePengajuan($koneksi) {
    $attempts = 0;
    do {
        $attempts++;
        if ($attempts > 10) {
            throw new Exception('Gagal generate kode pengajuan setelah 10 percobaan');
        }
        
        // Format: BBKSDA + YYYYMMDD + 4 digit random
        $kode = 'BBKSDA' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Cek apakah kode sudah ada
        $kode_escaped = mysqli_real_escape_string($koneksi, $kode);
        $check_query = "SELECT id FROM pengajuan_izin WHERE kode_pengajuan = '$kode_escaped'";
        $result = mysqli_query($koneksi, $check_query);
        
        if (!$result) {
            throw new Exception('Error checking kode pengajuan: ' . mysqli_error($koneksi));
        }
        
    } while (mysqli_num_rows($result) > 0);
    
    return $kode;
}

// Function untuk handle upload file
function handleFileUpload($file) {
    try {
        writeLog("Starting file upload process");
        
        $upload_dir = __DIR__ . '/../uploads/surat-permohonan/';
        
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                writeLog("Failed to create upload directory");
                return false;
            }
            writeLog("Upload directory created");
        }

        // Cek apakah direktori writable
        if (!is_writable($upload_dir)) {
            writeLog("Upload directory not writable");
            return false;
        }

        $allowed_types = ['pdf', 'doc', 'docx'];
        $allowed_mime_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        $file_type = $file['type'];

        writeLog("File info - Name: $file_name, Size: $file_size, Error: $file_error, Type: $file_type");

        if ($file_error !== UPLOAD_ERR_OK) {
            writeLog("File upload error: $file_error");
            return false;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (!in_array($file_ext, $allowed_types)) {
            writeLog("Invalid file extension: $file_ext");
            throw new Exception('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
        }

        // Validasi MIME type
        if (!in_array($file_type, $allowed_mime_types)) {
            writeLog("Invalid MIME type: $file_type");
            throw new Exception('Tipe file tidak valid.');
        }

        // Validasi ukuran file
        if ($file_size > $max_size) {
            writeLog("File too large: $file_size bytes");
            throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
        }

        // Generate nama file unik
        $new_filename = date('YmdHis') . '_' . uniqid() . '.' . $file_ext;
        $destination = $upload_dir . $new_filename;

        if (move_uploaded_file($file_tmp, $destination)) {
            writeLog("File uploaded successfully: $new_filename");
            return $new_filename;
        } else {
            writeLog("Failed to move uploaded file");
            return false;
        }

    } catch (Exception $e) {
        writeLog("File upload exception: " . $e->getMessage());
        throw $e;
    }
}
?>
