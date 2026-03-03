<?php
 $pageTitle = "RSIA Restu Ibu Official";
require_once 'config/database.php';

 $db = new Database();

// === PERBAIKAN 1: Query data dokter harus ada di sini ===
//  $db->query("SELECT * FROM doctors LIMIT 4");
//  $doctors = $db->resultSet();
 
 // ================= LOGIKA JADWAL HARI INI =================
 $day_num = date('N'); 
 $days_map = [1=>'SENIN', 2=>'SELASA', 3=>'RABU', 4=>'KAMIS', 5=>'JUMAT', 6=>'SABTU', 7=>'AKHAD'];
 $hari_ini = $days_map[$day_num];

// Query TANPA ALIAS
 $sql_hari_ini = "SELECT 
                    jadwal.jam_mulai, 
                    jadwal.jam_selesai, 
                    dokter.nm_dokter, 
                    dokter.foto, 
                    poliklinik.nm_poli, 
                    spesialis.nm_sps 
                FROM jadwal 
                JOIN dokter ON jadwal.kd_dokter = dokter.kd_dokter 
                LEFT JOIN poliklinik ON jadwal.kd_poli = poliklinik.kd_poli
                LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps
                WHERE jadwal.hari_kerja = :hari_ini AND dokter.status = '1'
                ORDER BY jadwal.jam_mulai ASC";

 $db->query($sql_hari_ini);
 $db->bind(':hari_ini', $hari_ini);
 $jadwal_hari_ini = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Hero Start -->
    <div class="container-fluid py-5 hero-header wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7 col-md-12 text-center text-lg-start">
                    <h1 class="mb-3 text-primary">We Care Your Baby</h1>
                    <h1 class="mb-5 display-1 text-white">RSIA Restu Ibu Melayani Dengan Kasih Ibu</h1>
                    <a href="layanan" class="btn btn-primary px-4 py-3 px-md-5 btn-border-radius">Learn More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->


    <!-- About Start -->
    <div class="container-fluid py-5 about bg-light" id="about">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
                    <div class="video border">
                        <button type="button" class="btn btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                            <span></span>
                        </button>
                    </div>
                </div>
                <div class="col-lg-7 wow fadeIn" data-wow-delay="0.3s">
                    <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">About Us</h4>
                    <h1 class="text-dark mb-4 display-5">Layanan Unggulan</h1>
                    <p class="text-dark mb-4">Rumah Sakit Ibu dan Anak Restu Ibu mempunyai banyak departemen yang terbagi menjadi berbagai poliklinik. Berikut adalah layanan utama kami.</p>
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Poliklinik Anak</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Poliklinik Kandungan</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-secondary"></i>Poliklinik Penyakit Dalam</h6>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Home Visit & Pijat Laktasi</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Jumat Berkah & USG Gratis</h6>
                            <h6><i class="fas fa-check-circle me-2 text-secondary"></i>...</h6>
                        </div>
                    </div>
                    <a href="layanan" class="btn btn-primary px-5 py-3 btn-border-radius">More Details</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


            <!-- Achievement Start -->
    <div class="container-fluid py-5 bg-light" id="achievement">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 600px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Pencapaian Kami</h4>
                <!-- <h1 class="display-6 mb-4">Angka Berbicara</h1> -->
            </div>
            <div class="row g-4 justify-content-center">
                <?php 
                $db->query("SELECT * FROM achievements ORDER BY id ASC");
                $achs = $db->resultSet();
                foreach($achs as $a): 
                ?>
                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                    <!-- Tambahan class h-100, d-flex, flex-column, justify-content-center -->
                    <div class="bg-primary text-center p-5 rounded shadow position-relative overflow-hidden h-100 d-flex flex-column justify-content-center">
                        <!-- Background Shape -->
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.1); transform: skewY(-6deg);"></div>
                        
                        <i class="<?php echo $a['icon']; ?> fa-4x text-white mb-4 position-relative"></i>
                        <h1 class="display-3 text-white mb-3 position-relative fw-bold">
                            <?php echo number_format($a['count'], 0, ',', '.'); ?>
                        </h1>
                        <h5 class="text-white position-relative mb-0" style="line-height: 1.4;">
                            <?php echo htmlspecialchars($a['title']); ?>
                        </h5>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Achievement End -->


    <!-- === PERBAIKAN 2: Modal Video harus ada agar tombol play berfungsi === -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Video Profil RSIA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always" allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Video End -->

    <!-- ================= SECTION HTML JADWAL HARI INI ================= -->
    <div class="container-fluid py-5 bg-white">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeIn" style="max-width: 600px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Jadwal Hari Ini</h4>
                <h1 class="display-6 mb-0">Dokter Praktek (<?php echo $hari_ini; ?>)</h1>
                <p class="text-muted mt-2">Berikut adalah jadwal dokter yang bertugas hari ini</p>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if(!empty($jadwal_hari_ini)): ?>
                    <?php foreach($jadwal_hari_ini as $j): ?>
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card h-100 border-0 shadow-sm text-center p-4 rounded">
                            <img src="img/<?php echo htmlspecialchars($j['foto']); ?>" class="rounded-circle mx-auto mb-3 shadow" style="width: 100px; height: 100px; object-fit: cover;" alt="">
                            <h5 class="text-primary mb-1"><?php echo htmlspecialchars($j['nm_dokter']); ?></h5>
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($j['nm_sps']); ?></p>
                            <p class="text-primary small fw-bold mb-1"><?php echo htmlspecialchars($j['nm_poli'] ?? 'Poliklinik Umum'); ?></p>
                            <div class="bg-light rounded p-2 mt-2">
                                <h6 class="text-dark mb-0">
                                    <i class="far fa-clock text-primary me-2"></i>
                                    <?php echo date('H:i', strtotime($j['jam_mulai'])); ?> - 
                                    <?php echo date('H:i', strtotime($j['jam_selesai'])); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">
                        <p>Tidak ada jadwal praktek untuk hari ini.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="jadwal" class="btn btn-primary rounded-pill px-5 py-3">Lihat Semua Jadwal</a>
            </div>
        </div>
    </div>



<?php
require_once 'inc/footer.php';
?>