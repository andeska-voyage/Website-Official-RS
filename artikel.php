<?php
 $pageTitle = "Detail Berita";
require_once 'config/database.php';
 $db = new Database();
 
 $pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

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
            <h1 class="display-5 text-white mb-4">Detail Berita</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item"><a href="berita">Berita</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Detail</li>
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
                        <div class="d-flex flex-wrap align-items-center mb-3">
                            <?php if($row['cat_name']): ?>
                            <a href="berita?category=<?php echo $row['cat_slug']; ?>" class="badge bg-primary text-white px-3 py-2 me-3 mb-2">
                                <i class="fas fa-folder me-1"></i> <?php echo $row['cat_name']; ?>
                            </a>
                            <?php endif; ?>
                            <span class="text-muted mb-2">
                                <i class="far fa-calendar-alt text-primary me-1"></i> <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                            </span>
                        </div>

                        <!-- Judul -->
                        <h2 class="mb-2 text-dark" style="font-weight: 700; line-height: 1.3;">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </h2>

                        <!-- === SHARE BUTTONS === -->
                        <div class="share-buttons-top d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small fw-bold me-2">Share:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&text=<?php echo urlencode($row['title']); ?>" target="_blank" class="btn btn-info btn-sm text-white">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($row['title'] . ' ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-success btn-sm">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                             <!-- Tambahan: Copy Link -->
                            <button onclick="copyToClipboard('<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>')" class="btn btn-secondary btn-sm">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                        <!-- End Share Buttons -->

                        <!-- Gambar Utama (PERHATIKAN: mt-3 ditambahkan di sini) -->
                        <div class="mt-3 rounded overflow-hidden">
                            <img src="img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="img-fluid w-100" alt="" style="max-height: 400px; object-fit: cover;">
                        </div>

                        <!-- Isi Konten -->
                        <div class="article-body">
                            <?php echo $row['content']; ?>
                        </div>

                    </div>

                    <!-- Author Box -->
                    <div class="bg-white p-4 rounded shadow-sm mt-4">
                        <div class="d-flex align-items-center">
                            <img src="img/<?php echo htmlspecialchars($siteLogo); ?>" class="rounded-circle border border-primary shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" alt="Logo RSIA">
                            <div class="ms-3">
                                <h5 class="mb-1 text-primary">RSIA Restu Ibu</h5>
                                <p class="mb-0 text-muted small">
                                    Rumah Sakit Ibu dan Anak yang melayani dengan kasih sayang ibu.
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

                <!-- Sidebar -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                     <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Informasi</h5>
                            <p class="text-muted small mb-0">
                                Artikel ini ditulis oleh tim redaksi RSIA Restu Ibu.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script untuk Copy Link -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link berhasil disalin!');
            }, function(err) {
                prompt("Salin link ini: Ctrl+C, Enter", text);
            });
        }
    </script>

<?php require_once 'inc/footer.php'; ?>