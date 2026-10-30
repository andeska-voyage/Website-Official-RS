<?php
 $pageTitle = "RSIA Restu Ibu Official";
require_once 'config/database.php';
require_once 'config/cache.php'; // PANGGIL HELPER CACHE

 $db = new Database();
 
 // === AMBIL PENGUMUMAN DENGAN CACHE (5 menit) ===
 $latest_announcements = get_cache('homepage_announcements', 300);
 if ($latest_announcements === false) {
    $db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
    $latest_announcements = $db->resultSet();
    set_cache('homepage_announcements', $latest_announcements);
 }
 
// === AMBIL BEBERAPA PENGUMUMAN TERBARU UNTUK POPUP ===
 $db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
 $latest_announcements = $db->resultSet();
 
 // ================= LOGIKA JADWAL HARI INI =================
 $day_num = date('N'); 
 $days_map = [1=>'SENIN', 2=>'SELASA', 3=>'RABU', 4=>'KAMIS', 5=>'JUMAT', 6=>'SABTU', 7=>'AKHAD'];
 $hari_ini = $days_map[$day_num];

// === AMBIL JADWAL DENGAN CACHE (1 Jam) ===
// Kita cache 1 jam (3600 detik) karena jadwal dokter tidak berubah tiap menit
 $jadwal_hari_ini = get_cache('jadwal_' . $hari_ini, 3600);
 if ($jadwal_hari_ini === false) {
    $sql_hari_ini = "SELECT 
                        jadwal.jam_mulai, jadwal.jam_selesai, dokter.nm_dokter, dokter.foto, 
                        poliklinik.nm_poli, spesialis.nm_sps 
                    FROM jadwal 
                    JOIN dokter ON jadwal.kd_dokter = dokter.kd_dokter 
                    LEFT JOIN poliklinik ON jadwal.kd_poli = poliklinik.kd_poli
                    LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps
                    WHERE jadwal.hari_kerja = :hari_ini AND dokter.status = '1'
                    ORDER BY jadwal.jam_mulai ASC";
    $db->query($sql_hari_ini);
    $db->bind(':hari_ini', $hari_ini);
    $jadwal_hari_ini = $db->resultSet();
    set_cache('jadwal_' . $hari_ini, $jadwal_hari_ini);
 }

require_once 'inc/header.php';

// ==================== QUERY BERITA DENGAN CACHE (5 menit) ====================
 $latest_news = get_cache('homepage_news', 300);
 if ($latest_news === false) {
    $db->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 4");
    $latest_news = $db->resultSet();
    set_cache('homepage_news', $latest_news);
 }
// Ambil Slides Dengan Cache
 $slides = get_cache('homepage_slides', 300);
if ($slides === false) {
    $db->query("SELECT * FROM slides ORDER BY ordering ASC");
    $slides = $db->resultSet();
    set_cache('homepage_slides', $slides);
}
?>

<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    
    <!-- INDIKATOR (BULATAN) -->
    <div class="carousel-indicators">
        <?php 
        $slide_count = 0;
        if(!empty($slides)) : 
            foreach($slides as $slide) : 
        ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $slide_count; ?>" 
                    class="<?php echo ($slide_count == 0) ? 'active' : ''; ?>" 
                    aria-current="<?php echo ($slide_count == 0) ? 'true' : 'false'; ?>" 
                    aria-label="Slide <?php echo $slide_count + 1; ?>"></button>
        <?php 
                $slide_count++;
            endforeach; 
        endif; 
        ?>
    </div>

    <div class="carousel-inner">
        <?php 
        $active = 'active';
        if(!empty($slides)) :
            foreach($slides as $slide) :
        ?>
        <div class="carousel-item <?php echo $active; ?>">
            <img src="img/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="d-block w-100">
            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                <div class="p-5" style="max-width: 900px;">
                    <?php if($slide['title']): ?>
                        <h1 class="display-2 text-white mb-4 animated fadeInUp"><?php echo htmlspecialchars($slide['title']); ?></h1>
                    <?php endif; ?>
                    <?php if($slide['subtitle']): ?>
                        <p class="fs-5 text-white mb-4 animated fadeInDown"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if($slide['btn_text']): ?>
                        <a href="<?php echo htmlspecialchars($slide['btn_link']); ?>" class="btn btn-primary rounded-pill py-3 px-5 mt-2 animated fadeIn">
                            <?php echo htmlspecialchars($slide['btn_text']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php 
            $active = '';
            endforeach;
        else :
        ?>
            <!-- Fallback -->
            <div class="carousel-item active">
                <img src="img/hero-img.jpg" class="d-block w-100" alt="Hero">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                    <h1 class="display-2 text-white mb-4 animated fadeInUp">Selamat Datang</h1>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- TOMBOL PANAH (PREV/NEXT) -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- Hero Slider End -->


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
                    <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Tentang Kami</h4>
                    <h1 class="text-dark mb-4 display-5">Layanan Unggulan</h1>
                    <p class="text-dark mb-4">Rumah Sakit Ibu dan Anak Restu Ibu mempunyai banyak departemen yang terbagi menjadi berbagai poliklinik. Berikut adalah layanan utama kami.</p>
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Poliklinik Anak</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Poliklinik Kandungan</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-secondary"></i>Fetomaternal</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-info"></i>Poliklinik Penyakit Dalam</h6>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Layanan Imunisasi</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Home Visit & Pijat Laktasi</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-secondary"></i>Jumat Berkah & USG Gratis</h6>
                            <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-info"></i>Dan Lain - lain</h6>
                        </div>
                    </div>
                    <a href="layanan" class="btn btn-primary px-5 py-3 btn-border-radius">Lihat Lebih Banyak</a>
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
            </div>
            <div class="row g-4 justify-content-center">
                <?php 
                $db->query("SELECT * FROM achievements ORDER BY id ASC");
                $achs = $db->resultSet();
                foreach($achs as $a): 
                ?>
                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-primary text-center p-5 rounded shadow position-relative overflow-hidden h-100 d-flex flex-column justify-content-center">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.1); transform: skewY(-6deg);"></div>
                        <i class="<?php echo $a['icon']; ?> fa-4x text-white mb-4 position-relative"></i>
                        <h1 class="display-3 text-white mb-3 position-relative fw-bold">
                            <?php echo $a['value']; ?>
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

    <!-- Modal Video -->
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
            <div class="mobile-scroll-wrapper wow fadeInUp" data-wow-delay="0.1s">
                <?php if(!empty($jadwal_hari_ini)): ?>
                    <?php foreach($jadwal_hari_ini as $j): ?>
                    <div class="mobile-scroll-item">
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
                    <div class="alert alert-warning text-center w-100">Tidak ada jadwal praktek untuk hari ini.</div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="dokter" class="btn btn-primary rounded-pill px-5 py-3">Lihat Semua Dokter Kami</a>
            </div>
        </div>
    </div>
    
    <!-- ================= SECTION BERITA TERBARU ================= -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeIn" style="max-width: 600px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Kabar Terbaru</h4>
                <h1 class="display-6 mb-0">Berita & Artikel</h1>
                <p class="text-muted mt-2">Update informasi kesehatan terkini untuk Anda</p>
            </div>

            <div class="mobile-scroll-wrapper wow fadeInUp" data-wow-delay="0.2s">
                <?php if(!empty($latest_news)): ?>
                    <?php foreach($latest_news as $news): ?>
                    <div class="mobile-scroll-item">
                        <div class="card h-100 border-0 shadow-sm rounded overflow-hidden">
                            <div class="position-relative">
                                <?php 
                                    $news_img_path = file_exists('img/thumb_' . $news['image']) ? 'img/thumb_' . $news['image'] : 'img/' . $news['image'];
                                ?>
                                <img src="<?php echo $news_img_path; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" style="width:100%; height:180px; object-fit:cover;">
                                <div class="position-absolute top-0 start-0 bg-primary text-white px-2 py-1 m-2 rounded" style="font-size:0.8rem;">
                                    <?php echo date('d M Y', strtotime($news['created_at'])); ?>
                                </div>
                                <?php if(strtotime($news['created_at']) > strtotime('-1 month')): ?>
                                    <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-2 rounded-pill" style="font-size:0.7rem; font-weight:bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        NEW
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold text-dark text-truncate"><?php echo htmlspecialchars($news['title']); ?></h6>
                                <p class="card-text small text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo strip_tags($news['content']); ?>
                                </p>
                                <a href="artikel?data=<?php echo base64_encode($news['id']); ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted w-100">Belum ada berita terbaru.</p>
                <?php endif; ?>
            </div>

            <div class="text-center mt-4">
                <a href="berita" class="btn btn-outline-primary rounded-pill px-5 py-3">
                    Lihat Semua Berita <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- ================= MODAL PENGUMUMAN POPUP ================= -->
    <?php if(!empty($latest_announcements)): 
        // Cek apakah total pengumuman lebih dari 4
        $has_more = count($latest_announcements) > 4;
        // Ambil maksimal hanya 4 pengumuman untuk ditampilkan di popup
        $announcements_to_show = array_slice($latest_announcements, 0, 4);
    ?>
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header bg-primary text-white py-3" style="border: 0;">
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-bullhorn me-2" style="color:white;"></i> <a style="color:white;">Pengumuman Terbaru</a>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <!-- Wrapper Scroll Kanan-Kiri (Dengan Ikon Panah Manual) -->
                    <div style="position: relative;">
                        
                        <!-- Baris Scroll Horizontal -->
                        <div style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 15px; scrollbar-width: none;">
                            <style>.modal-scroll-hide::-webkit-scrollbar { display: none; }</style>
                            
                            <?php foreach($announcements_to_show as $ann): 
                                // === LOGIKA AMBIL GAMBAR PERTAMA DARI ISI PENGUMUMAN ===
                                $first_image = '';
                                if (preg_match('/<img[^>]*src="([^"]*)"/i', $ann['content'], $matches)) {
                                    $first_image = str_replace('../', '', $matches[1]);
                                }
                                
                                $is_single = (count($announcements_to_show) == 1);
                                $card_width = $is_single ? '100%' : '300px';
                            ?>
                            <div class="modal-scroll-hide" style="min-width: <?php echo $card_width; ?>; max-width: <?php echo $card_width; ?>; flex-shrink: 0;">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                                    
                                    <?php if(!empty($first_image)): ?>
                                    <div class="rounded-top overflow-hidden" style="height: 180px;">
                                        <img src="<?php echo htmlspecialchars($first_image); ?>" class="w-100 h-100" style="object-fit: cover;" alt="Gambar Pengumuman">
                                    </div>
                                    <?php endif; ?>
                    
                                    <div class="card-body p-4">
                                        <small class="text-muted d-block mb-2">
                                            <i class="far fa-calendar-alt me-1"></i> <?php echo date('d F Y', strtotime($ann['created_at'])); ?>
                                        </small>
                                        <h5 class="text-dark mb-3 fw-bold"><?php echo htmlspecialchars($ann['title']); ?></h5>
                                        
                                        <p class="text-muted small" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo strip_tags($ann['content']); ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 pb-3">
                                        <a href="detail_pengumuman?data=<?php echo base64_encode($ann['id']); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 w-100">
                                            Baca Selengkapnya
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    
                        <!-- IKON PANAH SCROLL (Muncul di Desktop & HP jika kartu > 1) -->
                        <?php if(count($announcements_to_show) > 1): ?>
                        <div style="position: absolute; right: 10px; top: 50%; background: rgba(0, 0, 0, 0.7); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.3); pointer-events: none; z-index: 10; animation: bounce-arrow 2s infinite;">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <?php endif; ?>
                    
                    </div>
    
                    <!-- TOMBOL LIHAT SEMUA (Muncul jika data > 4) -->
                    <?php if($has_more): ?>
                    <div class="text-center mt-3 border-top pt-3">
                        <a href="pengumuman" class="btn btn-primary btn-sm rounded-pill px-4">
                            Lihat Semua Pengumuman <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <?php endif; ?>
    
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('announcementModal'));
            myModal.show();
        });
    </script>
    <?php endif; ?>
    <!-- ================= AKHIR MODAL PENGUMUMAN ================= -->

<?php require_once 'inc/footer.php'; ?>