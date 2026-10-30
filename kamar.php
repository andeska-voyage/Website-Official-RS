<?php
 $pageTitle = "Ketersediaan Tempat Tidur";
require_once 'config/database.php';
 $db = new Database();

 $db->query("SELECT * FROM kamar_sync ORDER BY 
            FIELD(kelas, 'Kelas VVIP', 'Kelas VIP', 'Kelas Utama', 'Kelas 1', 'Kelas 2', 'Kelas 3'), 
            nama_bangsal, kd_kamar");
 $rows = $db->resultSet();

// Kelompokkan data
 $grouped_data = [];
 $total_all = 0; $total_kosong = 0; $total_isi = 0;

foreach($rows as $r) {
    $kelas = $r['kelas'] ?? 'Lainnya';
    $bangsal = $r['nama_bangsal'] ?? 'Lainnya';
    $grouped_data[$kelas][$bangsal][] = $r;
    $total_all++;
    if($r['status'] == 'KOSONG') $total_kosong++;
    elseif($r['status'] == 'ISI') $total_isi++;
}

 $kelas_config = [
    'Kelas VVIP' => ['facilities' => ['1 Bed Pasien', 'Sofa Tamu', 'Kamar Mandi Dalam', 'TV', 'AC', 'WiFi', 'Kulkas']],
    'Kelas VIP' => ['facilities' => ['1 Bed Pasien', 'Sofa Tamu', 'Kamar Mandi Dalam', 'TV', 'AC', 'WiFi']],
    'Kelas Utama' => ['facilities' => ['2 Bed Pasien', 'Kamar Mandi Dalam', 'TV', 'AC', 'WiFi']],
    'Kelas 1' => ['facilities' => ['2 Bed Pasien', 'Kamar Mandi', 'AC', 'WiFi']],
    'Kelas 2' => ['facilities' => ['4 Bed Pasien', 'Kamar Mandi', 'AC']],
    'Kelas 3' => ['facilities' => ['4 Bed Pasien', 'Kamar Mandi']]
];

require_once 'inc/header.php';
?>

<style>
    /* CSS Khusus Desain Modern Kamar */
    /* Kotak latar belakang disamakan menjadi warna lembut */
    .kamar-card { border-radius: 20px; border: 1px solid #e2e8f0; background: #f8faff; }
    
    /* Desain Kotak Bed (Warna Lembut) */
    .tt-badge {
        min-width: 40px; height: 36px; padding: 0 10px;
        display: flex; align-items: center; justify-content: center; 
        border-radius: 10px; font-weight: 700; font-size: 0.8rem; 
        border: 1px solid transparent;
    }
    .tt-kosong { background: #ffffff; color: #64748b; border-color: #e2e8f0; } /* Abu-abu lembut */
    .tt-isi { background: #c2f2dc; color: #198754; border-color: #198754; } /* Hijau lembut */
    .tt-bersih { background: #fffbeb; color: #d97706; border-color: #fef3c7; } /* Kuning lembut */
    .tt-lain { background: #ffffff; color: #64748b; border-color: #e2e8f0; }
    
    /* Label Kelas disamakan menjadi Biru */
    .kelas-pill { border-radius: 16px; padding: 8px 16px; font-weight: 700; font-size: 0.85rem; background-color: #3156F5; color: #ffffff; }
    .facility-chip { 
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; 
        padding: 6px 12px; font-size: 0.75rem; font-weight: 600; color: #475569; 
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 5px;
    }
    .facility-chip i { color: #22c55e; font-size: 10px; }
    
    /* CSS untuk Tombol Filter */
    .filter-group { background: #f1f5f9; border-radius: 16px; padding: 6px; display: inline-flex; gap: 4px; }
    .filter-btn { border: none; background: transparent; color: #64748b; font-weight: 700; font-size: 0.85rem; padding: 8px 20px; border-radius: 12px; transition: all 0.3s; }
    .filter-btn.active { background: #3156F5; color: #fff; box-shadow: 0 4px 6px rgba(49, 86, 245, 0.2); }
    
    /* Responsivitas Mobile */
    @media (max-width: 767.98px) {
        .room-status-row { flex-direction: column; align-items: flex-start !important; gap: 10px; }
        .tariff-card-body { padding: 15px !important; }
        .tariff-card-body .text-end { text-align: left !important; }
    }
</style>

<!-- Header Section -->
<div class="container-fluid py-5" style="background: #f8faff;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fw-bold mb-3">
                <i class="fas fa-bed me-1"></i> Informasi Rawat Inap
            </span>
            <h1 class="display-5 fw-bold text-dark mb-3">Ketersediaan <span class="text-primary">Kamar</span></h1>
            <p class="text-muted mx-auto" style="max-width: 600px;">Pantau ketersediaan tempat tidur secara real-time dan pilih kamar yang sesuai dengan kebutuhan Anda.</p>
        </div>

        <!-- Statistik Kartu -->
        <div class="row g-3 justify-content-center mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-3 p-3"><i class="fas fa-bed fa-lg"></i></div>
                        <div>
                            <h3 class="fw-bold text-primary mb-0"><?php echo $total_all; ?></h3>
                            <small class="text-muted fw-semibold text-uppercase">Total Tempat Tidur</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-3 p-3"><i class="fas fa-check-circle fa-lg"></i></div>
                        <div>
                            <h3 class="fw-bold text-success mb-0"><?php echo $total_kosong; ?></h3>
                            <small class="text-muted fw-semibold text-uppercase">Tempat Tidur Kosong</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-3 p-3"><i class="fas fa-times-circle fa-lg"></i></div>
                        <div>
                            <h3 class="fw-bold text-danger mb-0"><?php echo $total_isi; ?></h3>
                            <small class="text-muted fw-semibold text-uppercase">Tempat Tidur Terisi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Status Real-Time -->
        <div class="mb-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <h4 class="fw-bold text-dark mb-0">Status Ketersediaan Tempat Tidur</h4>
                
                <!-- Tombol Filter -->
                <div class="filter-group">
                    <button class="filter-btn active" onclick="filterBeds('all', this)">Semua</button>
                    <button class="filter-btn" onclick="filterBeds('kosong', this)">Kosong</button>
                    <button class="filter-btn" onclick="filterBeds('isi', this)">Terisi</button>
                </div>
            </div>
            
            <?php foreach($grouped_data as $kelas_name => $bangsals): 
                $config = $kelas_config[$kelas_name] ?? ['facilities' => []];
            ?>
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="kelas-pill">
                        <?php echo htmlspecialchars($kelas_name); ?>
                    </span>
                    <div class="flex-grow-1 border-top"></div>
                </div>

                <div class="kamar-card p-3 p-md-4">
                    <?php foreach($bangsals as $bangsal_name => $kamars): 
                        $kosong = 0; $isi = 0;
                        foreach($kamars as $k) {
                            if($k['status'] == 'KOSONG') $kosong++;
                            elseif($k['status'] == 'ISI') $isi++;
                        }
                    ?>
                    <!-- Baris Status Ruangan -->
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 py-3 border-bottom room-status-row">
                        <div style="min-width: 200px;">
                            <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($bangsal_name); ?></h6>
                            <small class="text-muted fst-italic">(<?php echo htmlspecialchars($kelas_name); ?>)</small>
                        </div>
                        
                        <!-- Kotak Kamar (Badges) -->
                        <div class="d-flex flex-wrap gap-2 flex-grow-1">
                            <?php foreach($kamars as $k): 
                                $tt_class = 'tt-lain';
                                if($k['status'] == 'KOSONG') $tt_class = 'tt-kosong';
                                elseif($k['status'] == 'ISI') $tt_class = 'tt-isi';
                                elseif($k['status'] == 'DIBERSIHKAN') $tt_class = 'tt-bersih';
                            ?>
                            <div class="tt-badge <?php echo $tt_class; ?>" title="Kamar <?php echo htmlspecialchars($k['kd_kamar']); ?> - <?php echo $k['status']; ?>">
                                <?php echo htmlspecialchars($k['kd_kamar']); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Status Kosong/Terisi -->
                        <div class="text-end w-100" style="min-width: 120px;">
                            <span class="badge bg-success text-white px-3 py-2 mb-1"><?php echo $isi; ?> Terisi</span>
                            <span class="badge px-3 py-2" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;"><?php echo $kosong; ?> Kosong</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Bagian Tarif & Galeri -->
        <div>
            <div class="text-center mb-5">
                <span class="text-warning fw-bold text-uppercase" style="letter-spacing: 2px; font-size: 0.85rem;">Informasi Biaya & Fasilitas</span>
                <h4 class="fw-bold text-dark mt-2">Tarif & Foto Kamar</h4>
                <p class="text-muted mx-auto" style="max-width: 600px;">Tarif rawat inap per malam beserta fasilitas setiap kelas kamar. Belum termasuk biaya tindakan medis dan obat-obatan.</p>
            </div>

            <div class="row g-4">
                <?php foreach($grouped_data as $kelas_name => $bangsals): 
                    $config = $kelas_config[$kelas_name] ?? ['facilities' => []];
                    
                    $first_kamar = reset($bangsals);
                    $first_bangsal = reset($first_kamar);
                    $tarif = $first_bangsal['trf_kamar'] ?? 0;
                    
                    $folder_path = 'img/kamar/' . strtolower(str_replace(' ', '_', $kelas_name)) . '/';
                    $photos = [];
                    if (is_dir($folder_path)) {
                        $photos = glob($folder_path . '*.{jpg,jpeg,png}', GLOB_BRACE);
                    }
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px;">
                        <!-- Garis Warna Atas Disamakan Biru -->
                        <div style="height: 6px; background: #3156F5;"></div>
                        
                        <div class="row g-0 h-100">
                            <!-- Galeri Foto -->
                            <div class="col-md-5">
                                <?php if(!empty($photos)): ?>
                                <div id="gallery-<?php echo strtolower(str_replace(' ', '_', $kelas_name)); ?>" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach($photos as $idx => $photo): ?>
                                        <div class="carousel-item <?php echo $idx == 0 ? 'active' : ''; ?>">
                                            <img src="<?php echo $photo; ?>" class="d-block w-100" style="height: 250px; object-fit: cover;" alt="Foto <?php echo $kelas_name; ?>">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if(count($photos) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#gallery-<?php echo strtolower(str_replace(' ', '_', $kelas_name)); ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#gallery-<?php echo strtolower(str_replace(' ', '_', $kelas_name)); ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center h-100" style="min-height: 250px;">
                                    <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Detail Tarif & Fasilitas -->
                            <div class="col-md-7">
                                <div class="p-4 d-flex flex-column h-100 tariff-card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="kelas-pill">
                                            <?php echo htmlspecialchars($kelas_name); ?>
                                        </span>
                                        <div class="text-end">
                                            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">mulai dari</small>
                                            <h5 class="fw-bold mb-0" style="color: #3156F5;">
                                                <?php echo $tarif > 0 ? 'Rp ' . number_format($tarif, 0, ',', '.') : 'Hubungi Kami'; ?>
                                            </h5>
                                            <small class="text-muted">per malam</small>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        <?php foreach($config['facilities'] as $f): ?>
                                        <span class="facility-chip">
                                            <i class="fas fa-check-circle"></i> <?php echo $f; ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mt-auto border-top pt-3">
                                        <?php foreach($bangsals as $bangsal_name => $kamars): 
                                            $b_kosong = 0; $b_total = count($kamars);
                                            foreach($kamars as $k) if($k['status'] == 'KOSONG') $b_kosong++;
                                        ?>
                                        <div class="d-flex justify-content-between align-items-center py-2 <?php echo $bangsal_name != array_key_first($bangsals) ? 'border-top' : ''; ?>">
                                            <div>
                                                <h6 class="mb-0 text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($bangsal_name); ?></h6>
                                                <small class="text-muted"><i class="fas fa-bed me-1"></i><?php echo $b_total; ?> Tempat Tidur</small>
                                            </div>
                                            <span class="badge px-3 py-2" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                                            <?php echo $b_kosong; ?> Kosong
                                        </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- Script untuk Fungsi Filter -->
<script>
    function filterBeds(status, btn) {
        // Update tombol aktif
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Tampilkan/sembunyikan kotak kamar
        document.querySelectorAll('.tt-badge').forEach(badge => {
            if (status === 'all') {
                badge.style.display = 'flex'; // Tampilkan semua
            } else if (status === 'kosong') {
                // Hanya tampilkan yang kosong
                badge.style.display = badge.classList.contains('tt-kosong') ? 'flex' : 'none';
            } else if (status === 'isi') {
                // Hanya tampilkan yang terisi
                badge.style.display = badge.classList.contains('tt-isi') ? 'flex' : 'none';
            }
        });
    }
</script>

<?php require_once 'inc/footer.php'; ?>