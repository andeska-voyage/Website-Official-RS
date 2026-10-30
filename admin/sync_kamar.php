<?php
require_once __DIR__ . '/auth.php';

 $pageTitle = "Sync Kamar SIMRS";
require_once '../config/database.php'; 
require_once '../config/csrf.php';
 $db = new Database();

 $sync_msg = '';
 $sync_err = '';

// ======================================================
// LOGIKA SINKRONISASI (Jika Tombol Ditekan)
// ======================================================
if (isset($_GET['action']) && $_GET['action'] == 'sync') {
    // Validasi CSRF
    if (!isset($_GET['csrf_token']) || !validateCSRFToken($_GET['csrf_token'])) {
        die("<script>alert('Akses ditolak! Sesi tidak valid.'); history.back();</script>");
    }

    $simrs_host = 'localhost';
    $simrs_db   = 'sik';
    $simrs_user = 'sik';
    $simrs_pass = '';

    try {
        // 1. Koneksikan ke SIMRS
        $pdo_simrs = new PDO("mysql:host=$simrs_host;dbname=$simrs_db", $simrs_user, $simrs_pass);
        $pdo_simrs->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Ambil data dari SIMRS
        $sql_simrs = "SELECT k.kd_kamar, k.kd_bangsal, b.nm_bangsal, k.kelas, k.status, k.trf_kamar 
                      FROM kamar k 
                      LEFT JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal 
                      WHERE k.statusdata = '1'";
        $stmt = $pdo_simrs->query($sql_simrs);
        $data_kamar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Kosongkan tabel lokal website
        $db->query("TRUNCATE TABLE kamar_sync");
        $db->execute();

        // 4. Masukkan data baru ke tabel website
        $db->query("INSERT INTO kamar_sync (kd_kamar, kd_bangsal, nama_bangsal, kelas, status, trf_kamar) 
                    VALUES (:kd_kamar, :kd_bangsal, :nm_bangsal, :kelas, :status, :trf_kamar)");

        $insert_count = 0;
        foreach ($data_kamar as $row) {
            $db->bind(':kd_kamar', $row['kd_kamar']);
            $db->bind(':kd_bangsal', $row['kd_bangsal']);
            $db->bind(':nm_bangsal', $row['nm_bangsal']);
            $db->bind(':kelas', $row['kelas']);
            $db->bind(':status', $row['status']);
            $db->bind(':trf_kamar', $row['trf_kamar']);
            $db->execute();
            $insert_count++;
        }

        $sync_msg = "Sinkronisasi berhasil! $insert_count data kamar telah diperbarui dari SIMRS Khanza.";
        
    } catch (PDOException $e) {
        $sync_err = "Gagal terhubung ke SIMRS: " . $e->getMessage();
    }
}

// ======================================================
// AMBIL DATA LOKAL UNTUK DITAMPILKAN
// ======================================================
 $db->query("SELECT COUNT(*) as total FROM kamar_sync");
 $total_kamar = $db->single()['total'];

 $db->query("SELECT COUNT(*) as kosong FROM kamar_sync WHERE status = 'KOSONG'");
 $kamar_kosong = $db->single()['kosong'];

 $db->query("SELECT COUNT(*) as isi FROM kamar_sync WHERE status = 'ISI'");
 $kamar_isi = $db->single()['isi'];

 $db->query("SELECT * FROM kamar_sync ORDER BY kelas, nama_bangsal, kd_kamar LIMIT 10");
 $rows = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Sinkronisasi Kamar SIMRS</h2>
            <p class="text-muted">Bridging data ketersediaan tempat tidur dari database SIMRS Khanza.</p>
        </div>
    </div>

    <!-- Notifikasi -->
    <?php if($sync_msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $sync_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($sync_err): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $sync_err; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <!-- Kartu Status -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3"><i class="fas fa-bed fa-lg"></i></div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $total_kamar; ?></h3>
                        <small class="text-muted text-uppercase fw-semibold">Total Tempat Tidur</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3"><i class="fas fa-check-circle fa-lg"></i></div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $kamar_kosong; ?></h3>
                        <small class="text-muted text-uppercase fw-semibold">Kamar Kosong</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3"><i class="fas fa-times-circle fa-lg"></i></div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $kamar_isi; ?></h3>
                        <small class="text-muted text-uppercase fw-semibold">Kamar Terisi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4 p-md-5 text-center">
            <i class="fas fa-cloud-arrow-down text-primary mb-3" style="font-size: 3rem;"></i>
            <h4 class="fw-bold text-dark mb-2">Manual Sync</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Sistem secara otomatis melakukan sinkronisasi setiap 3 jam. Jika Anda baru saja mengubah data di SIMRS dan ingin langsung memperbarui website, klik tombol di bawah ini.</p>
            <a href="?action=sync&csrf_token=<?php echo generateCSRFToken(); ?>" class="btn btn-primary btn-lg px-5 py-3" style="border-radius: 12px;" onclick="return confirm('Mulai sinkronisasi data kamar sekarang?')">
                <i class="fas fa-sync-alt me-2"></i> Mulai Sinkronisasi Sekarang
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f1f5f9;">
            <h5 class="mb-0 text-dark fw-bold">Pratinjau Data Kamar (10 Data Terakhir)</h5>
            <a href="../kamar" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Halaman Web <i class="fas fa-external-link-alt ms-1"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;">
                        <tr>
                            <th class="ps-4 py-3">Kode Kamar</th>
                            <th class="py-3">Ruangan / Bangsal</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Tarif / Malam</th>
                            <th class="pe-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($rows)): ?>
                            <?php foreach($rows as $r): 
                                $status_color = 'bg-secondary';
                                if($r['status'] == 'KOSONG') $status_color = 'bg-success';
                                elseif($r['status'] == 'ISI') $status_color = 'bg-danger';
                                elseif($r['status'] == 'DIBERSIHKAN') $status_color = 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($r['kd_kamar']); ?></td>
                                <td><?php echo htmlspecialchars($r['nama_bangsal'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['kelas'] ?? '-'); ?></td>
                                <td>Rp <?php echo number_format($r['trf_kamar'] ?? 0, 0, ',', '.'); ?></td>
                                <td class="pe-4">
                                    <span class="badge <?php echo $status_color; ?> px-3 py-2 rounded-pill"><?php echo $r['status']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fas fa-database fa-2x mb-3 d-block opacity-50"></i>
                                    Belum ada data. Silakan lakukan sinkronisasi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>
