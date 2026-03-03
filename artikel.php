<?php
 $pageTitle = "Detail Berita";
require_once 'config/database.php';
 $db = new Database();

// === 1. Ambil Data Logo untuk Author Box ===
 $db->query("SELECT logo FROM site_profile WHERE id=1");
 $siteData = $db->single();
 $siteLogo = $siteData['logo'] ?? 'default.jpg';

// Ambil ID dari URL
 $encrypted_id = isset($_GET['data']) ? $_GET['data'] : '';
 $id = 0;
if (!empty($encrypted_id)) {
    $decoded = base64_decode($encrypted_id);
    if (is_numeric($decoded)) {
        $id = (int)$decoded;
    }
}

if($id > 0) {
    $db->query("SELECT posts.*, categories.name as cat_name, categories.slug as cat_slug 
                FROM posts 
                LEFT JOIN categories ON posts.category_id = categories.id 
                WHERE posts.id = :id");
    $db->bind(':id', $id);
    $row = $db->single();
} else {
    $row = false;
}

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Detail Berita</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item"><a href="berita">Berita</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Detail Content -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if($row): ?>
                    
                    <!-- Kartu Utama -->
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                        
                        <!-- Meta Info -->
                        <div class="d-flex flex-wrap align-items-center mb-4 border-bottom pb-4">
                            <?php if($row['cat_name']): ?>
                            <a href="berita?category=<?php echo $row['cat_slug']; ?>" class="badge bg-primary text-white px-3 py-2 me-3 mb-2" style="font-size: 0.9rem;">
                                <i class="fas fa-folder me-1"></i> <?php echo $row['cat_name']; ?>
                            </a>
                            <?php endif; ?>
                            <span class="text-muted me-3 mb-2">
                                <i class="far fa-calendar-alt text-primary me-1"></i> <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                            </span>
                        </div>

                        <!-- Judul -->
                        <h2 class="mb-4 text-dark" style="font-weight: 700; line-height: 1.4;">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </h2>

                        <!-- Gambar Utama -->
                        <div class="mb-4 rounded overflow-hidden">
                            <img src="img/<?php echo htmlspecialchars($row['image']); ?>" class="img-fluid w-100" alt="" style="max-height: 400px; object-fit: cover;">
                        </div>

                        <!-- Isi Konten -->
                        <div class="article-body">
                            <?php echo $row['content']; ?>
                        </div>

                        <!-- === BAGIAN SHARE BUTTON (RAPPI & RESPONSIF) === -->
                        <div class="mt-5 pt-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h6 class="mb-0 text-dark fw-bold">Bagikan artikel ini:</h6>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-md-end justify-content-center gap-2">
                                        <!-- Facebook -->
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" target="_blank" class="btn btn-primary px-3 py-2" style="min-width: 100px;">
                                            <i class="fab fa-facebook-f me-1"></i> <span class="d-none d-sm-inline">Facebook</span>
                                        </a>
                                        <!-- Twitter -->
                                        <a href="https://twitter.com/intent/tweet?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&text=<?php echo urlencode($row['title']); ?>" target="_blank" class="btn btn-info text-white px-3 py-2" style="min-width: 100px;">
                                            <i class="fab fa-twitter me-1"></i> <span class="d-none d-sm-inline">Twitter</span>
                                        </a>
                                        <!-- WhatsApp -->
                                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($row['title'] . ' ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-success px-3 py-2" style="min-width: 100px;">
                                            <i class="fab fa-whatsapp me-1"></i> <span class="d-none d-sm-inline">WhatsApp</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Share -->

                    </div>

                    <!-- === AUTHOR BOX (PAKAI LOGO RS) === -->
                    <div class="bg-white p-4 rounded shadow-sm mt-4">
                        <div class="d-flex align-items-center">
                            <!-- Logo RS -->
                            <img src="img/<?php echo htmlspecialchars($siteLogo); ?>" class="rounded-circle border border-primary shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" alt="Logo RSIA">
                            <div class="ms-3">
                                <h5 class="mb-1 text-primary">RSIA Restu Ibu</h5>
                                <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                    Rumah Sakit Ibu dan Anak yang melayani dengan kasih sayang ibu. Memberikan pelayanan kesehatan terbaik untuk ibu dan anak.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="mt-4">
                        <a href="berita" class="btn btn-secondary rounded-pill px-4 py-2"><i class="fas fa-arrow-left me-2"></i>Kembali ke Berita</a>
                    </div>

                    <?php else: ?>
                    <div class="bg-white p-5 rounded shadow-sm text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Pengumuman Tidak Ditemukan</h4>
                        <p class="text-muted">Maaf, konten yang Anda cari tidak tersedia.</p>
                        <a href="berita" class="btn btn-primary mt-3">Lihat Berita Lainnya</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar (Opsional) -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                     <!-- Widget Info -->
                     <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Informasi</h5>
                            <p class="text-muted small mb-0">
                                Artikel ini ditulis oleh tim redaksi RSIA Restu Ibu. Untuk informasi lebih lanjut, silakan hubungi kontak kami.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>