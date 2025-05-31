<?php
session_start();
$base_url = 'http://localhost/bbksda';
include __DIR__ . '/../koneksi.php'; // pastikan path koneksi.php benar

$email = $_POST['email'];
$password = $_POST['password'];

// Hash password menggunakan MD5 sesuai yang kamu pakai di database
$password_hashed = md5($password);

// Query data user berdasarkan email dan password hash
$query = "SELECT * FROM users WHERE email = '$email' AND password = '$password_hashed' LIMIT 1";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    // Set session data
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nama' => $user['nama'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    header("Location: $base_url/admin/dashboard.php");
    exit();
} else {
    header("Location: $base_url/login.php?error=Email atau password salah");
    exit();
}
