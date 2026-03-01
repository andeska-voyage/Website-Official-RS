<?php
 $pageTitle = "Tentang & Layanan";
require_once 'config/database.php';
 $db = new Database();

// Ambil Data
 $db->query("SELECT * FROM site_profile WHERE id=1");
 $profile = $db->single();

 $db->query("SELECT * FROM services ORDER BY id ASC");
 $services = $db->resultSet();

 $db->query("SELECT * FROM accreditations ORDER BY created_at DESC LIMIT 3");
 $accreditations = $db->resultSet();

 $db->query("SELECT * FROM insurances ORDER BY name ASC");
 $insurances = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Tentang Kami</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page">Profil & Layanan</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- ==================== SECTION 1: PROFIL (Visi Misi) ==================== -->
    <div class="container-fluid py-5 bg-white">
        <div class="container">
            
            <!-- Header Section -->
            <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s" style="max-width: 600px;">
                <h2 class="text-dark mb-3 display-6" style="font-weight: 300;">Profil <span class="fw-bold">RSIA Restu Ibu</span></h2>
                <div class="bg-primary mx-auto" style="width: 60px; height: 3px;"></div>
            </div>

            <!-- Sejarah -->
            <div class="row g-5 align-items-center mb-5 pb-5 border-bottom">
                <div class="col-lg-5 wow fadeInLeft" data-wow-delay="0.1s">
                    <img src="img/about.jpg" class="img-fluid rounded shadow" alt="Sejarah RS">
                </div>
                <div class="col-lg-7 wow fadeInRight" data-wow-delay="0.3s">
                    <h4 class="text-primary mb-3" style="letter-spacing: 1px;">Sejarah Singkat</h4>
                    <p class="text-muted text-justify mb-0"><?php echo $profile['history']; ?></p>
                </div>
            </div>

            <!-- Tab Navigation (Tombol Pilihan) -->
            <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.5s">
                <div class="col-lg-9">
                    <ul class="nav nav-pills justify-content-center mb-4 bg-light rounded-pill p-2" id="profilTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-2 mx-1" id="visi-tab" data-bs-toggle="pill" data-bs-target="#visi-content" type="button">Visi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2 mx-1" id="misi-tab" data-bs-toggle="pill" data-bs-target="#misi-content" type="button">Misi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2 mx-1" id="motto-tab" data-bs-toggle="pill" data-bs-target="#motto-content" type="button">Motto</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2 mx-1" id="tujuan-tab" data-bs-toggle="pill" data-bs-target="#tujuan-content" type="button">Tujuan</button>
                        </li>
                    </ul>

                    <!-- Tab Content (Konten) -->
                    <div class="tab-content bg-light rounded p-5 text-center" id="profilTabContent">
                        <!-- Visi -->
                        <div class="tab-pane fade show active" id="visi-content" role="tabpanel">
                            <i class="fas fa-eye fa-3x text-primary mb-3 opacity-25"></i>
                            <h3 class="text-primary mb-4">Visi</h3>
                            <p class="lead text-dark mb-0" style="font-size: 1.15rem;"><?php echo $profile['vision']; ?></p>
                        </div>
                        <!-- Misi -->
                        <div class="tab-pane fade" id="misi-content" role="tabpanel">
                            <i class="fas fa-bullseye fa-3x text-secondary mb-3 opacity-25"></i>
                            <h3 class="text-secondary mb-4">Misi</h3>
                            <div class="text-muted text-start mx-auto" style="max-width: 600px;"><?php echo $profile['mission']; ?></div>
                        </div>
                        <!-- Motto -->
                        <div class="tab-pane fade" id="motto-content" role="tabpanel">
                            <i class="fas fa-quote-left fa-3x text-warning mb-3 opacity-25"></i>
                            <h3 class="text-warning mb-4">Motto</h3>
                            <h2 class="text-dark display-6 fw-normal fst-italic mb-0">"<?php echo htmlspecialchars($profile['motto']); ?>"</h2>
                        </div>
                        <!-- Tujuan -->
                        <div class="tab-pane fade" id="tujuan-content" role="tabpanel">
                            <i class="fas fa-flag fa-3x text-info mb-3 opacity-25"></i>
                            <h3 class="text-info mb-4">Tujuan</h3>
                            <div class="text-muted text-start mx-auto" style="max-width: 600px;"><?php echo $profile['goals']; ?></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ==================== SECTION 2: LAYANAN RS ==================== -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeIn" style="max-width: 600px;">
                <h2 class="text-dark mb-3 display-6" style="font-weight: 300;">Layanan <span class="fw-bold">Kami</span></h2>
                <div class="bg-primary mx-auto mb-3" style="width: 60px; height: 3px;"></div>
                <p class="text-muted">Pelayanan kesehatan terpadu untuk Ibu dan Anak.</p>
            </div>
            <div class="row g-4">
                <?php foreach($services as $s): ?>
                <div class="col-md-6 col-lg-4 wow fadeInUp">
                    <div class="card h-100 border-0 bg-white shadow-sm rounded text-center p-4">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <i class="<?php echo $s['icon']; ?> fa-2x text-primary"></i>
                            </div>
                            <h5 class="mb-3 text-dark"><?php echo htmlspecialchars($s['title']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($s['description']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ==================== SECTION 3: AKREDITASI ==================== -->
    <div class="container-fluid py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5 wow fadeIn">
                <h2 class="text-dark mb-3 display-6" style="font-weight: 300;">Akreditasi <span class="fw-bold">Rumah Sakit</span></h2>
                <div class="bg-primary mx-auto mb-3" style="width: 60px; height: 3px;"></div>
                <p class="text-muted">Standar mutu pelayanan yang kami raih.</p>
            </div>
            <div class="row justify-content-center g-4">
                <?php foreach($accreditations as $a): ?>
                <div class="col-md-6 col-lg-3 wow fadeInUp">
                    <div class="card h-100 border-0 shadow-sm rounded overflow-hidden">
                        <div class="bg-light text-center py-4">
                            <img src="img/<?php echo $a['image']; ?>" class="img-fluid" style="max-height: 150px; object-fit: contain;" alt="">
                        </div>
                        <div class="card-body text-center p-3">
                            <h6 class="text-dark mb-0"><?php echo htmlspecialchars($a['title']); ?></h6>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ==================== SECTION 4: ASURANSI PARTNER ==================== -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 wow fadeIn">
                <h2 class="text-dark mb-3 display-6" style="font-weight: 300;">Asuransi <span class="fw-bold">Partner</span></h2>
                <div class="bg-primary mx-auto mb-3" style="width: 60px; height: 3px;"></div>
                <p class="text-muted">Bekerja sama dengan berbagai pihak Asuransi.</p>
            </div>
            <div class="row justify-content-center g-4 align-items-center">
                <?php foreach($insurances as $i): ?>
                <div class="col-6 col-md-3 col-lg-2 wow zoomIn">
                    <div class="bg-white p-3 rounded shadow-sm text-center h-100 d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <img src="img/<?php echo $i['logo']; ?>" alt="<?php echo $i['name']; ?>" class="img-fluid" style="max-height: 50px; filter: grayscale(100%); transition: 0.3s;" onmouseover="this.style.filter='grayscale(0%)'" onmouseout="this.style.filter='grayscale(100%)'">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>