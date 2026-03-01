<?php
 // Fungsi helper untuk mendapatkan nama file tanpa .php
 $currentUrl = $_SERVER['REQUEST_URI'];
// Handle root path
 $currentPage = basename(parse_url($currentUrl, PHP_URL_PATH));
if($currentPage == "") $currentPage = "index"; // root url
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>RSIA Restu Ibu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
                <a href="index.php" class="navbar-brand">
                    <h1 class="text-primary display-6 m-0" style="font-size: 1.4em;">RSIA <span class="text-secondary">Restu Ibu</span></h1>
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
                        <a href="berita" class="nav-item nav-link <?php echo ($currentPage == 'berita' || $currentPage == 'artikel') ? 'active' : ''; ?>">Berita</a>
                        
                        <!-- Dropdown Berkas -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle <?php echo ($currentPage == 'download' || $currentPage == 'pengumuman') ? 'active' : ''; ?>" data-bs-toggle="dropdown">Berkas</a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <a href="pengumuman" class="dropdown-item <?php echo ($currentPage == 'pengumuman') ? 'active' : ''; ?>">Pengumuman</a>
                                <a href="download" class="dropdown-item <?php echo ($currentPage == 'download') ? 'active' : ''; ?>">Download</a>
                            </div>
                        </div>
                        <!-- End Dropdown -->
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