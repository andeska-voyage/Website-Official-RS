<?php
 $pageTitle = "Download Dokumen";
require_once 'config/database.php';
 $db = new Database();

// Konfigurasi Pagination
 $limit = 10; // Jumlah data per halaman
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $search = isset($_GET['search']) ? $_GET['search'] : '';
 $offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Query Pencarian & Total Data
if(!empty($search)) {
    $db->query("SELECT * FROM documents WHERE title LIKE :search OR category LIKE :search");
    $db->bind(':search', "%$search%");
    $totalData = $db->resultSet();
    $totalRows = count($totalData);

    $db->query("SELECT * FROM documents WHERE title LIKE :search OR category LIKE :search LIMIT :offset, :limit");
    $db->bind(':search', "%$search%");
} else {
    $db->query("SELECT * FROM documents");
    $totalData = $db->resultSet();
    $totalRows = count($totalData);

    $db->query("SELECT * FROM documents ORDER BY created_at DESC LIMIT :offset, :limit");
}

// Bind Limit & Offset (Hanya untuk query terakhir)
// Note: Di beberapa versi PDO, bindValue untuk LIMIT harus PARAM_INT
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
            <p class="text-white">Unduh dokumen rumah sakit seperti Akreditasi, SOP, dan SK.</p>
        </div>
    </div>

    <!-- Download List -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-6 mx-auto">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-lg" placeholder="Cari dokumen..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Dokumen</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Upload</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = $offset + 1; 
                                if(!empty($docs)) :
                                    foreach($docs as $doc) : 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($doc['title']); ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo htmlspecialchars($doc['category']); ?></span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($doc['created_at'])); ?></td>
                                    <td>
                                        <a href="uploads/<?php echo htmlspecialchars($doc['file_path']); ?>" class="btn btn-sm btn-success" download>
                                            <i class="fas fa-download me-1"></i> Unduh
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach; 
                                else : 
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">Dokumen tidak ditemukan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12">
                    <?php if($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Tombol Previous -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Nomor Halaman -->
                            <?php for($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Tombol Next -->
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>