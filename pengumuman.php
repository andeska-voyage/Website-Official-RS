<?php
 $pageTitle = "Pengumuman";
require_once 'config/database.php';
 $db = new Database();

// Logika Pencarian
 $search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $limit = 6;
 $offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Query Total dengan Pencarian
 $sql_count = "SELECT * FROM announcements";
if($search_query) {
    $sql_count .= " WHERE title LIKE :search OR content LIKE :search";
}
 $db->query($sql_count);
if($search_query) { $db->bind(':search', "%".$search_query."%"); }
 $totalRows = count($db->resultSet());
 $totalPages = ceil($totalRows / $limit);

// Query Data dengan Pencarian
 $sql_data = "SELECT * FROM announcements";
if($search_query) {
    $sql_data .= " WHERE title LIKE :search OR content LIKE :search";
}
 $sql_data .= " ORDER BY created_at DESC LIMIT :offset, :limit";

 $db->query($sql_data);
if($search_query) { $db->bind(':search', "%".$search_query."%"); }
 $db->bind(':offset', $offset, PDO::PARAM_INT);
 $db->bind(':limit', $limit, PDO::PARAM_INT);
 $announcements = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Pengumuman</h1>
            <p class="text-white">Informasi penting dan resmi dari RSIA Restu Ibu.</p>
        </div>
    </div>

    <!-- List Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <!-- Form Pencarian -->
                    <div class="card border-0 shadow-sm mb-4 rounded">
                        <div class="card-body p-3">
                            <form method="GET" action="">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control form-control-lg border-0" placeholder="Cari pengumuman..." value="<?php echo htmlspecialchars($search_query); ?>">
                                    <button class="btn btn-primary px-4" type="submit"><i class="fas fa-search"></i></button>
                                    <?php if($search_query): ?>
                                        <a href="pengumuman" class="btn btn-secondary px-3"><i class="fas fa-times"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if($search_query): ?>
                        <div class="alert alert-info py-2 mb-4">
                            Hasil pencarian untuk: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <?php if(!empty($announcements)): ?>
                            <?php foreach($announcements as $a): ?>
                            <div class="col-md-6 col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="card h-100 border-0 shadow-sm rounded overflow-hidden">
                                    <div class="card-body p-4">
                                        <div class="d-flex mb-3">
                                            <small class="text-muted"><i class="far fa-calendar-alt text-primary me-2"></i><?php echo date('d M Y', strtotime($a['created_at'])); ?></small>
                                        </div>
                                        <h5 class="card-title text-dark mb-3"><?php echo htmlspecialchars($a['title']); ?></h5>
                                        <p class="card-text text-muted">
                                            <?php echo substr(strip_tags($a['content']), 0, 100) . '...'; ?>
                                        </p>
                                        <a href="detail_pengumuman?data=<?php echo base64_encode($a['id']); ?>" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                            Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">Pengumuman tidak ditemukan.</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if($totalPages > 1): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link rounded-pill mx-1" href="?page=<?php echo $page-1; ?>&search=<?php echo $search_query; ?>"><i class="fas fa-chevron-left"></i></a>
                                    </li>
                                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link rounded-circle mx-1" href="?page=<?php echo $i; ?>&search=<?php echo $search_query; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link rounded-pill mx-1" href="?page=<?php echo $page+1; ?>&search=<?php echo $search_query; ?>"><i class="fas fa-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>