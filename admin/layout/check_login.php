<?php
session_start();

$base_url = 'http://localhost/bbksda';

// Cegah cache halaman di browser
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "/admin/login.php");
    exit();
}

// Cek apakah session masih valid (maksimal 8 jam)
$max_session_time = 8 * 60 * 60; // 8 jam dalam detik
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $max_session_time) {
    // Session expired
    session_destroy();
    header("Location: " . $base_url . "/admin/login.php?expired=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Function untuk cek role
function checkRole($required_role) {
    if (!isset($_SESSION['user']['role'])) {
        return false;
    }
    
    $user_role = $_SESSION['user']['role'];
    
    // Hierarki role: super-admin > admin > operator
    $role_hierarchy = [
        'super-admin' => 3,
        'admin' => 2,
        'operator' => 1
    ];
    
    $user_level = $role_hierarchy[$user_role] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;
    
    return $user_level >= $required_level;
}

// Function untuk redirect jika tidak punya akses
function requireRole($required_role) {
    global $base_url;
    
    if (!checkRole($required_role)) {
        header("Location: " . $base_url . "/403.php");
        exit();
    }
}

// Function untuk mendapatkan info user yang login
function getLoggedUser() {
    return $_SESSION['user'] ?? [
        'id' => 0,
        'username' => '',
        'nama' => '',
        'email' => '',
        'role' => 'operator'
    ];
}

// Function untuk cek apakah user adalah super admin
function isSuperAdmin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super-admin';
}

// Function untuk cek apakah user adalah admin atau super admin
function isAdmin() {
    return checkRole('admin');
}
?>
