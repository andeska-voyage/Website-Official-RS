<?php
 $pageTitle = "Jadwal Dokter";
require_once 'config/database.php';
 $db = new Database();
 
 $pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

// Query TANPA ALIAS (menggunakan nama tabel penuh)
 $sql = "SELECT 
            jadwal.hari_kerja, 
            jadwal.jam_mulai, 
            jadwal.jam_selesai, 
            dokter.nm_dokter, 
            dokter.foto, 
            spesialis.nm_sps, 
            poliklinik.nm_poli 
        FROM jadwal 
        JOIN dokter ON jadwal.kd_dokter = dokter.kd_dokter 
        LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps
        LEFT JOIN poliklinik ON jadwal.kd_poli = poliklinik.kd_poli
        WHERE dokter.status = '1'
        ORDER BY 
            poliklinik.nm_poli ASC, 
            FIELD(jadwal.hari_kerja, 'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AKHAD'), 
            jadwal.jam_mulai ASC";

 $db->query($sql);
 $schedules = $db->resultSet();

// Kelompokkan data berdasarkan Nama Poliklinik
 $grouped = [];
foreach($schedules as $row) {
    $poli_name = $row['nm_poli'] ? $row['nm_poli'] : 'Lainnya';
    $grouped[$poli_name][] = $row;
}

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Jadwal Praktek</h1>
            <p class="text-white">Jadwal praktek dokter RSIA Restu Ibu</p>
        </div>
    </div>

    <!-- Jadwal List -->
    <div class="container-fluid py-5">
        <div class="container">
            <?php 
            if(!empty($grouped)):
            foreach($grouped as $poli_name => $rows): 
            ?>
            <div class="mb-5">
                <h3 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block pb-2">
                    <i class="fas fa-hospital-alt me-2"></i><?php echo $poli_name; ?>
                </h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover shadow-sm bg-white">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="120">Hari</th>
                                <th>Nama Dokter</th>
                                <th>Spesialis</th>
                                <th>Jam Praktek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rows as $r): ?>
                            <tr>
                                <td class="fw-bold align-middle"><?php echo $r['hari_kerja']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="img/<?php echo htmlspecialchars($r['foto']); ?>" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;" alt="">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($r['nm_dokter']); ?></span>
                                    </div>
                                </td>
                                <td class="align-middle"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($r['nm_sps']); ?></span></td>
                                <td class="align-middle">
                                    <?php echo date('H:i', strtotime($r['jam_mulai'])); ?> - 
                                    <?php echo date('H:i', strtotime($r['jam_selesai'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php 
            endforeach; 
            else:
            ?>
            <div class="alert alert-warning text-center">Tidak ada jadwal praktek yang tersedia.</div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>