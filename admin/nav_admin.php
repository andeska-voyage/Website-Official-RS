<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin RSIA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4a51f9; /* Modern Blue/Purple */
            --sidebar-bg: #1e1e2d;
            --sidebar-hover: #2a2a3d;
        }

        body {
            background-color: #f5f8fa;
            font-family: 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Style */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: #fff;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background-color: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .menu-label {
            padding: 10px 20px 5px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            font-weight: 700;
        }

        .nav-link {
            color: #a2a3b7;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            margin: 2px 0;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .nav-link.active {
            background-color: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary-color);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            min-height: 100vh;
            background-color: #f5f8fa;
        }

        .topbar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .content-wrapper {
            padding: 30px;
        }

        /* Card Style */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0 fw-bold"><i class="bi bi-hospital me-2"></i>RSIA Admin</h5>
            <small class="text-muted">Management System</small>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="index" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='index')?'active':''; ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <div class="menu-label">Master Data</div>
            <a href="settings" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='settings')?'active':''; ?>">
                <i class="bi bi-gear-fill"></i> Profil RS
            </a>
            <a href="achievements" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='achievements')?'active':''; ?>">
                <i class="bi bi-bar-chart-fill"></i> Pencapaian
            </a>

            <div class="menu-label">SDM (Manusia)</div>
            <a href="doctors" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='doctors')?'active':''; ?>">
                <i class="bi bi-person-badge-fill"></i> Dokter
            </a>
            <a href="staff" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='staff')?'active':''; ?>">
                <i class="bi bi-people-fill"></i> Karyawan
            </a>

            <div class="menu-label">Konten Website</div>
            <a href="categories" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='categories')?'active':''; ?>">
                <i class="bi bi-folder-fill"></i> Kategori
            </a>
            <a href="posts" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='posts')?'active':''; ?>">
                <i class="bi bi-newspaper"></i> Berita
            </a>
            <a href="announcements" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='announcements')?'active':''; ?>">
                <i class="bi bi-megaphone-fill"></i> Pengumuman
            </a>

            <div class="menu-label">Lainnya</div>
            <a href="documents" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='documents')?'active':''; ?>">
                <i class="bi bi-cloud-download-fill"></i> Download
            </a>
            <a href="insurances" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='insurances')?'active':''; ?>">
                <i class="bi bi-shield-check"></i> Asuransi
            </a>
            <a href="accreditations" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='accreditations')?'active':''; ?>">
                <i class="bi bi-award-fill"></i> Akreditasi
            </a>
            
            <div class="mt-5 px-3 d-grid">
                 <a href="../index" class="btn btn-sm btn-outline-light rounded-pill"><i class="bi bi-eye me-2"></i>Lihat Website</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h4 class="mb-0 fw-bold"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h4>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted small">Welcome, <?php echo $_SESSION['admin_user'] ?? 'Admin'; ?></span>
                <a href="logout" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <!-- Page Content Start -->
        <div class="content-wrapper">