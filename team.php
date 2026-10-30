<?php
 $pageTitle = "Tim Kami";
require_once 'config/database.php';
 $db = new Database();
 $year = date("Y");
 
 $pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

// Ambil Bagan Organisasi dari Profil
 $db->query("SELECT org_chart FROM site_profile WHERE id=1");
 $profile = $db->single();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Struktur Organisasi</h1>
            <p class="text-white">Struktur organisasi RSIA Restu Ibu Padang.</p>
        </div>
    </div>

    <!-- === SECTION BAGAN ORGANISASI (FULL WIDTH) === -->
    <div class="container-fluid bg-white py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="row g-0 justify-content-center">
            <div class="col-12 px-3 px-md-5">
                
                <?php if(!empty($profile['org_chart'])): ?>
                    <!-- Jika Ada Gambar Bagan -->
                    <div class="text-center mb-4">
                        <h2 class="text-primary">STRUKTUR ORGANISASI RUMAH SAKIT IBU DAN ANAK RESTU IBU <?php echo $year; ?></h2>
                    </div>
                    
                    <!-- Gambar Full Width -->
                    <div class="org-chart-wrapper rounded overflow-hidden shadow-sm p-2 p-md-4 bg-light">
                        <img src="img/<?php echo htmlspecialchars($profile['org_chart']); ?>" 
                             alt="Bagan Organisasi" 
                             class="img-fluid w-100"
                             style="max-width: 100%; height: auto;">
                    </div>
                <?php else: ?>
                    <!-- Jika Belum Ada Gambar -->
                    <div class="alert alert-warning text-center mx-auto" style="max-width: 600px;">
                        <i class="fas fa-image fa-3x mb-3 d-block"></i>
                        <h5>Bagan Organisasi Belum Tersedia</h5>
                        <p class="mb-0">Silakan upload bagan organisasi</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- === AKHIR BAGAN ORGANISASI === -->

<?php require_once 'inc/footer.php'; ?>