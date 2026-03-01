<?php
 $pageTitle = "Detail Berita";
require_once 'config/database.php';
 $db = new Database();

// Ambil parameter 'data'
 $encrypted_id = isset($_GET['data']) ? $_GET['data'] : '';

// Decode dari Base64
 $id = 0; // Default value
if (!empty($encrypted_id)) {
    $decoded = base64_decode($encrypted_id);
    // Validasi: Pastikan hasil decode adalah angka (Keamanan tambahan)
    if (is_numeric($decoded)) {
        $id = (int)$decoded;
    }
}

if($id > 0) {
    // Query ambil berita + nama kategori
    $db->query("SELECT p.*, c.name as cat_name, c.slug as cat_slug 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id");
    $db->bind(':id', $id);
    $post = $db->single();

    // Update View Count (Opsional, jika ada kolom 'views' di DB)
    // $db->query("UPDATE posts SET views = views + 1 WHERE id = :id");
    // $db->bind(':id', $id);
    // $db->execute();
} else {
    $post = false;
}

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4"><?php echo $post ? htmlspecialchars($post['title']) : 'Berita Tidak Ditemukan'; ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="berita.php">Berita</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Detail Content Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <!-- Kolom Konten Utama -->
                <div class="col-lg-8">
                    <?php if($post): ?>
                    
                    <!-- Card Artikel -->
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                        
                        <!-- Meta Info -->
                        <div class="d-flex flex-wrap align-items-center mb-4 border-bottom pb-4">
                            <!-- Kategori -->
                            <?php if($post['cat_name']): ?>
                            <a href="berita.php?category=<?php echo $post['cat_slug']; ?>" class="badge bg-primary text-white px-3 py-2 me-3 mb-2" style="font-size: 0.9rem;">
                                <i class="fas fa-folder me-1"></i> <?php echo htmlspecialchars($post['cat_name']); ?>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Tanggal -->
                            <span class="text-muted me-3 mb-2">
                                <i class="far fa-calendar-alt text-primary me-1"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?>
                            </span>
                            
                            <!-- Penulis -->
                            <span class="text-muted mb-2">
                                <i class="far fa-user text-primary me-1"></i> Admin
                            </span>
                        </div>

                        <!-- Gambar Utama -->
                        <div class="mb-4 rounded overflow-hidden">
                            <img src="img/<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid w-100" alt="<?php echo htmlspecialchars($post['title']); ?>" style="min-height: 300px; object-fit: cover;">
                        </div>

                        <!-- Isi Konten -->
                        <div class="article-body">
                            <div class="article-body">
                                <?php echo $post['content']; ?>
                            </div>
                            
                            <!-- Contoh Paragraf Tambahan jika konten pendek -->
                            <?php if(strlen($post['content']) < 200): ?>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Share Buttons -->
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="mb-3 text-dark">Bagikan :</h6>
                            <div class="d-flex">
                                <a href="#" class="btn btn-outline-primary btn-sm me-2 px-3 rounded-pill"><i class="fab fa-facebook-f me-1"></i> Facebook</a>
                                <a href="#" class="btn btn-outline-info btn-sm me-2 px-3 rounded-pill"><i class="fab fa-twitter me-1"></i> Twitter</a>
                                <a href="#" class="btn btn-outline-success btn-sm me-2 px-3 rounded-pill"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
                            </div>
                        </div>

                    </div>

                    <!-- Author Box -->
                    <div class="bg-white p-4 rounded shadow-sm mt-4">
                        <div class="d-flex align-items-center">
                            <img src="img/team-1.jpg" class="rounded-circle border border-primary" style="width: 80px; height: 80px; object-fit: cover;" alt="Author">
                            <div class="ms-3">
                                <h5 class="mb-1 text-primary">Admin RSIA</h5>
                                <p class="mb-0 text-muted small">Tim Redaksi RSIA Restu Ibu. Menyajikan informasi kesehatan terpercaya.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="mt-4">
                        <a href="berita" class="btn btn-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Berita</a>
                    </div>

                    <?php else: ?>
                    <div class="alert alert-danger text-center">Berita yang Anda cari tidak ditemukan.</div>
                    <div class="text-center">
                        <a href="berita.php" class="btn btn-primary">Lihat Semua Berita</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Kanan -->
                <div class="col-lg-4">
                    <!-- Widget Search -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Pencarian</h5>
                            <form action="berita.php" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari berita...">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Widget Kategori -->
                    <?php 
                    // Ambil Kategori
                    $db->query("SELECT * FROM categories LIMIT 5");
                    $categories = $db->resultSet();
                    ?>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Kategori</h5>
                            <ul class="list-unstyled mb-0">
                                <?php foreach($categories as $cat): ?>
                                <li class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="berita.php?category=<?php echo $cat['slug']; ?>" class="text-dark text-decoration-none">
                                        <i class="fas fa-angle-right text-primary me-2"></i><?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Widget Info RS -->
                    <div class="card border-0 bg-primary text-white" style="border-radius: 15px;">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-phone-alt fa-2x mb-3"></i>
                            <h5 class="text-white mb-3">Hubungi Kami</h5>
                            <p class="mb-0 small">Jika Anda memiliki pertanyaan seputar kesehatan, silakan hubungi kami.</p>
                            <a href="tel:+6282391856461" class="btn btn-light text-primary mt-3 rounded-pill px-4 fw-bold">
                                +62 823 9185 6461
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>