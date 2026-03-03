<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php';
 $db = new Database();

// Hitung Statistik (Menggunakan nama tabel yang benar: dokter, posts, staff)
 $db->query("SELECT COUNT(*) as total FROM dokter"); 
 $docCount = $db->single()['total'];

 $db->query("SELECT COUNT(*) as total FROM posts"); 
 $postCount = $db->single()['total'];

 $db->query("SELECT COUNT(*) as total FROM staff"); 
 $staffCount = $db->single()['total'];

require_once 'nav_admin.php';
?>

    <h2 class="fw-bold mb-4 text-dark">Dashboard</h2>
    
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white o-hidden h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-light">Total Dokter</h6><h2 class="fw-bold"><?php echo $docCount; ?></h2></div>
                        <i class="fas fa-user-md fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white o-hidden h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-light">Total Staf</h6><h2 class="fw-bold"><?php echo $staffCount; ?></h2></div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark o-hidden h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-light">Berita</h6><h2 class="fw-bold"><?php echo $postCount; ?></h2></div>
                        <i class="fas fa-newspaper fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white o-hidden h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-light">User</h6><h2 class="fw-bold">Admin</h2></div>
                        <i class="fas fa-user-shield fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card p-4">
                <h5>Selamat Datang di Panel Admin RSIA Restu Ibu</h5>
                <p class="text-muted">Gunakan menu di sebelah kiri untuk mengelola data website.</p>
            </div>
        </div>
    </div>

<?php require_once 'footer_admin.php'; ?>