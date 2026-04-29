<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Dashboard";
require_once '../config/database.php';
 $db = new Database();

// --- STATS PENGUNJUNG ---
// Hari Ini
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE visit_date = CURDATE()");
 $visitors_today = $db->single()['total'];

// Bulan Ini
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())");
 $visitors_month = $db->single()['total'];

// Rata-rata Harian
 $days_passed = date('d'); 
 $avg_visits = $days_passed > 0 ? round($visitors_month / $days_passed) : 0;

// --- STATS DEVICE (PIE CHART DATA) ---
// Total Device Bulan Ini
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE MONTH(visit_date) = MONTH(CURDATE())");
 $total_dev = $db->single()['total'];

// Hitung Persentase Mobile
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE device_type = 'Mobile' AND MONTH(visit_date) = MONTH(CURDATE())");
 $mobile_count = $db->single()['total'];

// Hitung Persentase Desktop
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE device_type = 'Desktop' AND MONTH(visit_date) = MONTH(CURDATE())");
 $desktop_count = $db->single()['total'];

// Hitung Persentase Tablet
 $db->query("SELECT COUNT(*) as total FROM visitors WHERE device_type = 'Tablet' AND MONTH(visit_date) = MONTH(CURDATE())");
 $tablet_count = $db->single()['total'];

// Hitung Persentase (Hindari pembagian nol)
 $mobile_pct = $total_dev > 0 ? round(($mobile_count / $total_dev) * 100) : 0;
 $desktop_pct = $total_dev > 0 ? round(($desktop_count / $total_dev) * 100) : 0;
 $tablet_pct = $total_dev > 0 ? round(($tablet_count / $total_dev) * 100) : 0;

// --- STATS DATA LAIN ---
 $db->query("SELECT COUNT(*) as total FROM dokter"); $docCount = $db->single()['total'];
 $db->query("SELECT COUNT(*) as total FROM posts"); $postCount = $db->single()['total'];
 $db->query("SELECT COUNT(*) as total FROM staff"); $staffCount = $db->single()['total'];

require_once 'nav_admin.php';
?>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark mb-0">Dashboard</h2>
            <p class="text-muted">Selamat datang kembali, <?php echo $_SESSION['admin_user']; ?>!</p>
        </div>
    </div>

    <!-- Row Stats Pengunjung -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm py-3 bg-opacity-10" style="background-color: #e8f5e9; border-left: 4px solid #51b749 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pengunjung Hari Ini</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo number_format($visitors_today); ?></h2>
                            <small class="text-success">Unique Visitors</small>
                        </div>
                        <div class="p-3 rounded-circle bg-success text-white"><i class="fas fa-eye"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm py-3" style="background-color: #e3f2fd; border-left: 4px solid #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Bulan Ini</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo number_format($visitors_month); ?></h2>
                            <small class="text-primary">Klik Bulan Berjalan</small>
                        </div>
                        <div class="p-3 rounded-circle bg-primary text-white"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm py-3" style="background-color: #fff3e0; border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Rata-rata Harian</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo number_format($avg_visits); ?></h2>
                            <small class="text-warning">Per Hari (Bulan Ini)</small>
                        </div>
                        <div class="p-3 rounded-circle bg-warning text-white"><i class="fas fa-calculator"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm py-3" style="background-color: #ffebee; border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Dokter</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo $docCount; ?></h2>
                            <small class="text-danger">Dokter Aktif</small>
                        </div>
                        <div class="p-3 rounded-circle bg-danger text-white"><i class="fas fa-user-md"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row Device & Stats -->
    <div class="row g-4">
        
        <!-- Device Usage (Pie Chart) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-mobile-alt me-2 text-primary"></i>Device Usage (Bulan Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div id="chart-container" style="position: relative; height: 200px;">
                            <!-- Placeholder untuk Chart -->
                             <i class="fas fa-chart-pie fa-5x text-muted opacity-25"></i>
                        </div>
                        <h4 class="mt-3">Total: <?php echo $total_dev; ?> Kunjungan</h4>
                    </div>
                    
                    <!-- Progress Bar Stats -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1"><span>Mobile</span><span><?php echo $mobile_pct; ?>%</span></div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $mobile_pct; ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1"><span>Desktop</span><span><?php echo $desktop_pct; ?>%</span></div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo $desktop_pct; ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1"><span>Tablet</span><span><?php echo $tablet_pct; ?>%</span></div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: <?php echo $tablet_pct; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Lain -->
        <div class="col-lg-7">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent"><h6 class="mb-0">Statistik Website</h6></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between"><span>Total Berita</span><span class="badge bg-primary"><?php echo $postCount; ?></span></li>
                                <li class="list-group-item d-flex justify-content-between"><span>Total Staf</span><span class="badge bg-secondary"><?php echo $staffCount; ?></span></li>
                                <li class="list-group-item d-flex justify-content-between"><span>Sistem Keamanan</span><span class="badge bg-success">CSRF Active</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent"><h6 class="mb-0">Catatan</h6></div>
                        <div class="card-body text-muted small">
                            <p class="mb-2"><strong>Pengunjung:</strong> Dihitung berdasarkan IP unik per hari.</p>
                            <p class="mb-0"><strong>Device:</strong> Terdeteksi otomatis berdasarkan browser pengunjung.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'footer_admin.php'; ?>