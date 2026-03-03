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
 
 // === TAMBAHAN: CEK BERITA TERBARU (1 BULAN TERAKHIR) ===
 $db->query("SELECT COUNT(*) as total FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
 $newPostsData = $db->single();
 $newPostsCount = $newPostsData['total'];
 // === TAMBAHAN: CEK PENGUMUMAN BARU (1 BULAN TERAKHIR) ===
 $db->query("SELECT COUNT(*) as total FROM announcements WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
 $newAnnData = $db->single();
 $newAnnCount = $newAnnData['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>RSIA Restu Ibu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <!-- FAVICON (Logo di Tab Browser) -->
    <link rel="icon" type="image/x-icon" href="img/<?php echo htmlspecialchars($site_logo); ?>">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@200;400;600&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar start -->
    <div class="container-fluid border-bottom bg-light wow fadeIn" data-wow-delay="0.1s">
        <!-- Topbar (Sembunyi di mobile) -->
        <div class="container topbar bg-primary d-none d-lg-block py-2" style="border-radius: 0 40px">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">Jl. Terandam No.5, Padang</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">rsiarestuibu.pdg@gmail.com</a></small>
                </div>
                <div class="top-link pe-2">
                    <a href="" class="btn btn-light btn-sm-square rounded-circle"><i class="fab fa-facebook-f text-secondary"></i></a>
                    <a href="" class="btn btn-light btn-sm-square rounded-circle"><i class="fab fa-instagram text-secondary"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Navbar Utama -->
        <div class="container px-0">
            <!-- Ubah expand-lg agar menu lebih cepat collapse di tablet -->
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a href="index" class="navbar-brand d-flex align-items-center">
                    <!-- Logo Gambar -->
                    <img src="img/<?php echo htmlspecialchars($site_logo); ?>" alt="Logo" style="height: 45px; width: auto; margin-right: 10px;">
                    <!-- Teks Brand -->
                    <div class="d-flex flex-column">
                        <span class="text-primary fw-bold" style="font-size: 1.3rem; line-height: 1.1;">RSIA Restu Ibu</span>
                        <small class="text-secondary" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($site_motto); ?></small>
                    </div>
                </a>
                
                <!-- Tombol Hamburger Menu -->
                <button class="navbar-toggler py-2 px-3 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-label="Toggle navigation">
                    <span class="fa fa-bars text-primary"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <!-- mx-auto akan membuat menu di tengah, tapi kita geser sedikit agar tidak menabrak logo di laptop kecil -->
                    <div class="navbar-nav ms-lg-auto me-lg-4 mb-2 mb-lg-0">
                        <a href="index" class="nav-item nav-link <?php echo ($currentPage == 'index') ? 'active' : ''; ?>">Beranda</a>
                        <a href="layanan" class="nav-item nav-link <?php echo ($currentPage == 'layanan') ? 'active' : ''; ?>">Layanan</a>
                        <a href="dokter" class="nav-item nav-link <?php echo ($currentPage == 'dokter') ? 'active' : ''; ?>">Dokter</a>
                        <a href="jadwal" class="nav-item nav-link <?php echo ($currentPage == 'jadwal') ? 'active' : ''; ?>">Jadwal</a>
                        <a href="team" class="nav-item nav-link <?php echo ($currentPage == 'team') ? 'active' : ''; ?>">Tim Kami</a>
                        <!-- Menu Berita dengan Notif -->
                        <a href="berita" class="nav-item nav-link position-relative <?php echo ($currentPage == 'berita' || $currentPage == 'artikel') ? 'active' : ''; ?>">
                            Berita
                            <!-- Notifikasi jika ada berita baru -->
                            <?php if($newPostsCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger ms-n3" style="font-size: 9px; animation: blinker 1.5s linear infinite;">
                                    <?php echo $newPostsCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- Dropdown Berkas -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle <?php echo ($currentPage == 'download' || $currentPage == 'pengumuman') ? 'active' : ''; ?>" data-bs-toggle="dropdown">Berkas</a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <!-- Menu Pengumuman dengan Notif Inline -->
                                <a href="pengumuman" class="dropdown-item <?php echo ($currentPage == 'pengumuman') ? 'active' : ''; ?>">
                                    Pengumuman
                                    <?php if($newAnnCount > 0): ?>
                                        <!-- Notif Sejajar dengan teks -->
                                        <span class="badge rounded-pill bg-danger ms-2" style="font-size: 9px; animation: blinker 1.5s linear infinite;">
                                            <?php echo $newAnnCount; ?> Baru
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <a href="download" class="dropdown-item <?php echo ($currentPage == 'download') ? 'active' : ''; ?>">Download</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bagian Telepon (Hanya muncul di Layar Besar) -->
                    <div class="d-none d-xl-flex me-4 align-items-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <a href="tel:+6282391856461" class="position-relative">
                                <i class="fa fa-phone-alt text-primary fa-2x me-3"></i>
                            </a>
                        </div>
                        <div class="d-flex flex-column border-start border-primary ps-3">
                            <span class="text-primary small">Have any questions?</span>
                            <span class="text-secondary fw-bold">+62 823 9185 6461</span>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->