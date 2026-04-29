<?php
 // Fungsi helper untuk mendapatkan nama file tanpa .php
 $currentUrl = $_SERVER['REQUEST_URI'];
 // Handle root path
 $currentPage = basename(parse_url($currentUrl, PHP_URL_PATH));
 if($currentPage == "") $currentPage = "index"; // root url

 // 1. Ambil data Profil & Logo dari Database
 if (!isset($db)) {
    require_once 'config/database.php';
    $db = new Database();
 }

 $db->query("SELECT * FROM site_profile WHERE id=1");
 $site_profile = $db->single();
 $site_logo = $site_profile['logo'] ?? 'default.jpg';
 $site_motto = $site_profile['motto'] ?? 'Melayani dengan Kasih Ibu';
 
 // === CEK NOTIFIKASI ===
 $db->query("SELECT COUNT(*) as total FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
 $newPostsData = $db->single();
 $newPostsCount = $newPostsData['total'];

 $db->query("SELECT COUNT(*) as total FROM announcements WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
 $newAnnData = $db->single();
 $newAnnCount = $newAnnData['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--<title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>RSIA Restu Ibu</title>-->
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - RSIA Restu Ibu' : 'RSIA Restu Ibu - Rumah Sakit Ibu dan Anak'; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Website resmi RSIA Restu Ibu Padang">
    <meta name="google-site-verification" content="J1BCBnBidG_EHq-OudMtwObeCPM-5svFAHIJkFojEGU" />
    
    <!-- FAVICON -->
    <link rel="icon" type="image/x-icon" href="img/<?php echo htmlspecialchars($site_logo); ?>">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@200;400;600&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <!-- PENTING: Kita load Font Awesome untuk Navbar, dan Bootstrap Icons untuk Konten -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- CSS Animasi Kedip (Ditaruh di sini agar pasti jalan) -->
    <style>
        @keyframes blinker {
            50% { opacity: 0; }
        }
    </style>
    <?php
    require_once 'config/tracker.php';
    ?>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar start -->
    <div class="container-fluid border-bottom bg-light wow fadeIn" data-wow-delay="0.1s">
        <!-- Topbar -->
        <!-- PERUBAHAN: bg-dark (Gelap) lebih nyaman dibanding hijau, text-white -->
        <div class="container topbar bg-primary d-none d-lg-block py-2" style="border-radius: 0 40px">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2 d-flex align-items-center flex-wrap">
                    
                    <!-- ALAMAT: Dihilangkan tag <a>, jadi plain text. Pasti bisa di-copy -->
                    <small class="me-4 text-light">
                        <i class="fas fa-map-marker-alt me-2 text-white"></i> 
                        Jl. Terandam No.5, RW.7, Sawahan, Kec. Padang Tim., Kota Padang, Sumatera Barat 25121
                    </small>
                    
                    <!-- EMAIL: Tetap pakai link mailto (kalau diklik langsung buka email), tapi warna disamakan -->
                    <small class="me-3">
                        <i class="fas fa-envelope me-2 text-white"></i>
                        <a href="mailto:rsiarestuibu.pdg@gmail.com" class="text-light text-decoration-none">rsiarestuibu.pdg@gmail.com</a>
                    </small>
                </div>
                
                <div class="top-link pe-2">
                    <!-- SOSMED: Ikon putih di tombol transparan/abu-abu agar classy -->
                    <a href="https://www.tiktok.com/@rsiarestuibu" target="_blank" class="btn btn-sm-square rounded-circle btn-outline-light border-0 me-1">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://www.instagram.com/rsiarestuibu/" target="_blank" class="btn btn-sm-square rounded-circle btn-outline-light border-0">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navbar Utama -->
        <div class="container px-0">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a href="index" class="navbar-brand d-flex align-items-center">
                    <img src="img/<?php echo htmlspecialchars($site_logo); ?>" alt="Logo" style="height: 45px; width: auto; margin-right: 10px;">
                    <div class="d-flex flex-column">
                        <span class="text-primary fw-bold" style="font-size: 1.3rem; line-height: 1.1;">RSIA Restu Ibu</span>
                        <small class="text-secondary" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($site_motto); ?></small>
                    </div>
                </a>
                
                <button class="navbar-toggler py-2 px-3 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-label="Toggle navigation">
                    <span class="fa fa-bars text-primary"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-lg-auto me-lg-4 mb-2 mb-lg-0">
                        <a href="index" class="nav-item nav-link <?php echo ($currentPage == 'index') ? 'active' : ''; ?>">Beranda</a>
                        <a href="layanan" class="nav-item nav-link <?php echo ($currentPage == 'layanan') ? 'active' : ''; ?>">Layanan</a>
                        <a href="dokter" class="nav-item nav-link <?php echo ($currentPage == 'dokter') ? 'active' : ''; ?>">Dokter</a>
                        <a href="team" class="nav-item nav-link <?php echo ($currentPage == 'team') ? 'active' : ''; ?>">Struktur Organisasi</a>
                        
                        <!-- Menu Berita dengan Notif -->
                        <a href="berita" class="nav-item nav-link position-relative <?php echo ($currentPage == 'berita' || $currentPage == 'artikel') ? 'active' : ''; ?>">
                            Berita
                            <?php if($newPostsCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger ms-n3" style="font-size: 9px; animation: blinker 1.5s linear infinite;">
                                    <?php echo $newPostsCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- Dropdown Berkas -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle <?php echo ($currentPage == 'download' || $currentPage == 'pengumuman') ? 'active' : ''; ?>" data-bs-toggle="dropdown">Berkas</a>
                            <!-- PERBAIKAN: Ganti bg-secondary menjadi bg-white shadow-sm agar teks terlihat -->
                            <div class="dropdown-menu m-0 bg-white shadow-sm border-0 rounded-0">
                                <a href="pengumuman" class="dropdown-item <?php echo ($currentPage == 'pengumuman') ? 'active' : ''; ?>">
                                    Pengumuman
                                    <?php if($newAnnCount > 0): ?>
                                        <span class="badge rounded-pill bg-danger ms-2" style="font-size: 9px; animation: blinker 1.5s linear infinite;">
                                            <?php echo $newAnnCount; ?> Baru
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <a href="download" class="dropdown-item <?php echo ($currentPage == 'download') ? 'active' : ''; ?>">Download</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bagian Telepon -->
                    <div class="d-none d-xl-flex me-4 align-items-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <a href="tel:0751810756" class="position-relative">
                                <i class="fa fa-phone-alt text-primary fa-2x me-3"></i>
                            </a>
                        </div>
                        <div class="d-flex flex-column border-start border-primary ps-3">
                            <span class="text-primary small">Jika Ada Yang ditanyakan ?</span>
                            <span class="text-secondary fw-bold">(0751) 810756</span>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->