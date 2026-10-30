<?php
// File: admin/cron_sync_kamar.php
// Script ini khusus untuk dijalankan oleh Cron Job Server (Tidak butuh login)
// Panggilan HTTP hanya boleh dilakukan oleh admin; cron CLI tetap bisa berjalan tanpa sesi.
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/auth.php';
    if (!$isAdmin) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

// 1. KEAMANAN: Hanya bisa dijalankan via Command Line (Cron Job) atau dengan Secret Key
 $is_cli = php_sapi_name() === 'cli';
 $has_secret_key = isset($_GET['key']) && $_GET['key'] === '32r4t56grtb'; // Ganti kode rahasia ini

if (!$is_cli && !$has_secret_key) {
    die("Akses Ditolak.");
}

// 2. Panggil Database Website
require_once __DIR__ . '/../config/database.php';
 $db = new Database();

// 3. Koneksi ke SIMRS Khanza
 $simrs_host = 'localhost';
 $simrs_db   = 'sik';
 $simrs_user = 'sik';
 $simrs_pass = '';

try {
    $pdo_simrs = new PDO("mysql:host=$simrs_host;dbname=$simrs_db", $simrs_user, $simrs_pass);
    $pdo_simrs->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Ambil data dari SIMRS
    $sql_simrs = "SELECT k.kd_kamar, k.kd_bangsal, b.nm_bangsal, k.kelas, k.status, k.trf_kamar 
                  FROM kamar k 
                  LEFT JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal 
                  WHERE k.statusdata = '1'";
    $stmt = $pdo_simrs->query($sql_simrs);
    $data_kamar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Kosongkan tabel lokal website
    $db->query("TRUNCATE TABLE kamar_sync");
    $db->execute();

    // 6. Masukkan data baru
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

    // Catat log (opsional, untuk memastikan cron jalan)
    echo "Sinkronisasi otomatis berhasil! $insert_count data diperbarui pada " . date('Y-m-d H:i:s');

} catch (PDOException $e) {
    // Catat error ke file log
    error_log("Cron Sync Kamar Gagal: " . $e->getMessage());
    echo "Gagal: " . $e->getMessage();
}
?>
