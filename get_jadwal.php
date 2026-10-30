<?php
// File: get_jadwal.php
// Pastikan path config benar. Karena file ini dipanggil via AJAX dari root, pakai path langsung.
require_once 'config/database.php'; 

// Matikan error reporting agar tidak mengganggu output JSON/HTML
error_reporting(0);

$pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

 $db = new Database();

 $kd_dokter = $_GET['kd'] ?? '';

if(!empty($kd_dokter)) {
    // Query Jadwal + Poliklinik
    $db->query("SELECT jadwal.hari_kerja, jadwal.jam_mulai, jadwal.jam_selesai, poliklinik.nm_poli 
                FROM jadwal 
                LEFT JOIN poliklinik ON jadwal.kd_poli = poliklinik.kd_poli 
                WHERE jadwal.kd_dokter = :kd 
                ORDER BY FIELD(jadwal.hari_kerja, 'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AKHAD'), jadwal.jam_mulai ASC");
    $db->bind(':kd', $kd_dokter);
    $rows = $db->resultSet();

    if(!empty($rows)) {
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="bg-light">
                <tr>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Poliklinik</th>
                </tr>
              </thead><tbody>';
        
        foreach($rows as $r) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($r['hari_kerja']) . '</strong></td>';
            echo '<td>' . date('H:i', strtotime($r['jam_mulai'])) . ' - ' . date('H:i', strtotime($r['jam_selesai'])) . '</td>';
            echo '<td>' . htmlspecialchars($r['nm_poli'] ?? 'Umum') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-warning text-center mb-0">Jadwal praktek belum tersedia.</div>';
    }
} else {
    echo '<div class="alert alert-danger text-center mb-0">ID Dokter tidak valid.</div>';
}
?>