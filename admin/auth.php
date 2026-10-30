<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['dashboard_user_id'])) {
    header('Location: login');
    exit;
}

$isAdmin = isset($_SESSION['admin_id']);
$isReadOnlyUser = !$isAdmin && isset($_SESSION['dashboard_user_id']);

// Data lama memakai tautan GET untuk menghapus atau menjalankan sinkronisasi.
// Tolak juga jalur tersebut agar akun user tidak bisa mengubah data lewat URL.
if ($isReadOnlyUser && (
    $_SERVER['REQUEST_METHOD'] !== 'GET' ||
    isset($_GET['delete']) || isset($_GET['action']) || isset($_GET['bulk_delete'])
)) {
    http_response_code(403);
    exit('Akses ditolak. Akun user hanya memiliki akses baca.');
}
