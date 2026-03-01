<?php
 $pageTitle = "Berita & Artikel";
require_once 'config/database.php';
 $db = new Database();

// Ambil parameter
 $category_filter = isset($_GET['category']) ? $_GET['category'] : null;
 $search_query = isset($_GET['search']) ? $_GET['search'] : null;

// Pagination
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $limit = 5;
 $offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// === LOGIKA QUERY DINAMIS ===
// 1. Query Hitung Total
 $sql_count = "SELECT COUNT(*) as total FROM posts p LEFT JOIN categories c ON p.category_id = c.id";
 $where_clauses = [];
 $params_count = [];

if($category_filter) {
    $where_clauses[] = "c.slug = :slug";
    $params_count[':slug'] = $category_filter;
}
if($search_query) {
    $where_clauses[] = "(p.title LIKE :search OR p.content LIKE :search)";
    $params_count[':search'] = "%".$search_query."%";
}

if(count($where_clauses) > 0) {
    $sql_count .= " WHERE " . implode(" AND ", $where_clauses);
}

 $db->query($sql_count);
foreach($params_count as $key => $val) { $db->bind($key, $val); }
 $count = $db->single();
 $totalPages = ceil($count['total'] / $limit);

// 2. Query Ambil Data
 $sql_data = "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id";
if(count($where_clauses) > 0) {
    $sql_data .= " WHERE " . implode(" AND ", $where_clauses);
}
 $sql_data .= " ORDER BY p.created_at DESC LIMIT :offset, :limit";

 $db->query($sql_data);
foreach($params_count as $key => $val) { $db->bind($key, $val); }
 $db->bind(':offset', $offset, PDO::PARAM_INT);
 $db->bind(':limit', $limit, PDO::PARAM_INT);
 $posts = $db->resultSet();

// Ambil Kategori untuk Sidebar
 $db->query("SELECT c.id, c.name, c.slug, COUNT(p.id) as post_count FROM categories c LEFT JOIN posts p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name ASC");
 $categories = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Berita & Artikel</h1>
            <p class="text-white mb-0">Update informasi kesehatan terkini dari RSIA Restu Ibu</p>
        </div>
    </div>

    <!-- Blog List Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                
                <!-- Kolom Kiri -->
                <div class="col-lg-8">
                    
                    <!-- Form Pencarian -->
                    <div class="card border-0 shadow-sm mb-4 rounded">
                        <div class="card-body p-3">
                            <form method="GET" action="">
                                <div class="input-group">
                                    <?php if($category_filter): ?>
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                                    <?php endif; ?>
                                    <input type="text" name="search" class="form-control form-control-lg border-0" placeholder="Cari judul atau isi berita..." value="<?php echo htmlspecialchars($search_query); ?>">
                                    <button class="btn btn-primary px-4" type="submit"><i class="fas fa-search"></i></button>
                                    <?php if($search_query): ?>
                                        <a href="berita<?php echo $category_filter ? '?category='.$category_filter : ''; ?>" class="btn btn-secondary px-3"><i class="fas fa-times"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Hasil Pencarian Info -->
                    <?php if($search_query): ?>
                        <div class="alert alert-info py-2 mb-4">
                            Menampilkan hasil pencarian untuk: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <?php if(!empty($posts)): ?>
                            <?php foreach($posts as $post): ?>
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="card border-0 shadow-sm card-blog h-100" style="border-radius: 15px;">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <a href="artikel?data=<?php echo base64_encode($post['id']); ?>" class="d-block h-100">
                                                <div class="blog-img-wrapper">
                                                    <img src="img/<?php echo htmlspecialchars($post['image']); ?>" class="w-100" alt="">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="badge bg-light text-primary me-2 px-3 py-2">
                                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                                    </span>
                                                    <?php if($post['category_name']): ?>
                                                    <a href="berita?category=<?php echo $post['category_slug']; ?>" class="badge bg-light text-secondary px-3 py-2">
                                                        <i class="fas fa-folder me-1"></i> <?php echo htmlspecialchars($post['category_name']); ?>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="artikel?data=<?php echo base64_encode($post['id']); ?>" class="text-decoration-none">
                                                    <h4 class="card-title blog-title text-dark mb-3"><?php echo htmlspecialchars($post['title']); ?></h4>
                                                </a>
                                                <p class="card-text blog-desc mb-4"><?php echo strip_tags($post['content']); ?></p>
                                                <a href="artikel?data=<?php echo base64_encode($post['id']); ?>" class="btn btn-primary px-4 py-2 rounded-pill btn-sm">
                                                    Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">Tidak ada berita yang ditemukan.</div>
                            </div>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <?php if($totalPages > 1): ?>
                        <div class="col-12 mt-5">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php if($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link rounded-pill mx-1" href="?page=<?php echo $page-1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>"><i class="fas fa-chevron-left"></i></a>
                                    </li>
                                    <?php endif; ?>

                                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link rounded-circle mx-1" href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>

                                    <?php if($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link rounded-pill mx-1" href="?page=<?php echo $page+1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>"><i class="fas fa-chevron-right"></i></a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sidebar-widget mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Kategori</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="berita" class="text-dark text-decoration-none <?php echo !$category_filter ? 'fw-bold text-primary' : ''; ?>">
                                        <i class="fas fa-angle-right text-primary me-2"></i>Semua Berita
                                    </a>
                                </li>
                                <?php foreach($categories as $cat): ?>
                                <li class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="berita?category=<?php echo $cat['slug']; ?>" class="text-dark text-decoration-none <?php echo ($category_filter == $cat['slug']) ? 'fw-bold text-primary' : ''; ?>">
                                        <i class="fas fa-angle-right text-primary me-2"></i><?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>