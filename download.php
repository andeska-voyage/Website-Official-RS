<?php
 $pageTitle = "Download Dokumen";
require_once 'config/database.php';
 $db = new Database();
 
 $pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

// =========================================================
// 1. LOGIKA DOWNLOAD FILE (Jika Tombol Download Ditekan)
// =========================================================
if(isset($_GET['action']) && $_GET['action'] == 'download' && isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = 'uploads/' . $filename;
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush();
        readfile($filepath);
        exit();
    } else {
        die("<div class='alert alert-danger'>File tidak ditemukan di server.</div>");
    }
}

// =========================================================
// 2. LOGIKA PENCARIAN & PAGINATION
// =========================================================
 $limit = 10; // Jumlah data per halaman
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $search = isset($_GET['search']) ? $_GET['search'] : '';
 $offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Query Total Data (untuk Pagination)
if(!empty($search)) {
    $db->query("SELECT * FROM documents WHERE title LIKE :search OR category LIKE :search");
    $db->bind(':search', "%$search%");
} else {
    $db->query("SELECT * FROM documents");
}
 $totalData = $db->resultSet();
 $totalRows = count($totalData);

// Query Data per Halaman
if(!empty($search)) {
    $db->query("SELECT * FROM documents WHERE title LIKE :search OR category LIKE :search ORDER BY created_at DESC LIMIT :offset, :limit");
    $db->bind(':search', "%$search%");
} else {
    $db->query("SELECT * FROM documents ORDER BY created_at DESC LIMIT :offset, :limit");
}
 $db->bind(':offset', $offset, PDO::PARAM_INT);
 $db->bind(':limit', $limit, PDO::PARAM_INT);

 $docs = $db->resultSet();
 $totalPages = ceil($totalRows / $limit);

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Download Center</h1>
            <p class="text-white">Unduh dokumen rumah sakit seperti Akreditasi, SOP, SK, Brosur, dan Survey.</p>
        </div>
    </div>

    <!-- Download List -->
    <!-- Menggunakan container-fluid agar full width di mobile, px-2 untuk padding kecil di mobile, px-md-5 untuk desktop -->
    <div class="container-fluid py-5 px-2 px-md-5">
        <div class="container">
            
            <!-- Search Form -->
            <div class="row mb-4 justify-content-center">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari dokumen..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            <?php if($search): ?>
                                <a href="download" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <!-- table-responsive agar tabel bisa scroll horizontal di mobile jika lebar -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%" class="ps-3">No</th>
                                    <th>Nama Dokumen</th>
                                    <th width="15%">Kategori</th>
                                    <th width="15%" class="d-none d-md-table-cell">Tanggal</th>
                                    <th width="15%" class="text-center pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = $offset + 1; 
                                if(!empty($docs)) :
                                    foreach($docs as $doc) : 
                                ?>
                                <tr>
                                    <td class="ps-3"><?php echo $no++; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($doc['title']); ?>
                                        <!-- Tampilkan tanggal di mobile di bawah judul -->
                                        <div class="d-block d-md-none small text-muted mt-1">
                                            <?php echo date('d M Y', strtotime($doc['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo htmlspecialchars($doc['category']); ?></span>
                                        <!-- Badge Tipe (Link/File) -->
                                        <?php if(!empty($doc['link_url'])): ?>
                                            <span class="badge bg-success">Link</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?php echo date('d M Y', strtotime($doc['created_at'])); ?></td>
                                    <td class="text-center pe-3">
                                        <?php if(!empty($doc['link_url'])): ?>
                                            <!-- Jika Link -->
                                            <a href="<?php echo htmlspecialchars($doc['link_url']); ?>" target="_blank" class="btn btn-success btn-sm">
                                                <i class="fas fa-external-link-alt"></i> <span class="d-none d-sm-inline">Buka</span>
                                            </a>
                                        <?php else: ?>
                                            <!-- Jika File -->
                                            <a href="download?action=download&file=<?php echo htmlspecialchars($doc['file_path']); ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach; 
                                else : 
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        Dokumen tidak ditemukan.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center flex-wrap">
                            <!-- Prev -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <!-- Numbers -->
                            <?php for($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>