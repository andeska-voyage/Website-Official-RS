<?php
 $pageTitle = "Dokter Praktek";
require_once 'config/database.php';
 $db = new Database();
 
 $pageTitle = $row['title'];
 $metaDesc = substr(strip_tags($row['content']), 0, 150); // Ambil 150 karakter awal

// Logika Pencarian
 $search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Query Dokter (Tanpa Singkat Alias)
 $sql = "SELECT dokter.kd_dokter, dokter.nm_dokter, dokter.jk, dokter.foto, dokter.no_telp, spesialis.nm_sps 
        FROM dokter 
        LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps 
        WHERE dokter.status = '1'";

if(!empty($search_query)) {
    $sql .= " AND (dokter.nm_dokter LIKE :search OR spesialis.nm_sps LIKE :search)";
}

 $sql .= " ORDER BY dokter.nm_dokter ASC";

 $db->query($sql);
if(!empty($search_query)) {
    $db->bind(':search', "%".$search_query."%");
}
 $dokters = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Tim Dokter Kami</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item text-white">Dokter</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Dokter List -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            
            <!-- Form Pencarian -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="Cari nama dokter atau spesialis..." value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-primary px-4" type="submit"><i class="fas fa-search"></i></button>
                            <?php if($search_query): ?>
                                <a href="dokter" class="btn btn-secondary px-3"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($search_query): ?>
            <div class="text-center mb-4">
                <small class="text-muted">Hasil pencarian untuk: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</small>
            </div>
            <?php endif; ?>

            <div class="row g-4 justify-content-center">
                <?php if(!empty($dokters)): ?>
                    <?php foreach($dokters as $d): ?>
                    <div class="col-lg-3 col-md-6 wow fadeInUp">
                        <!-- Card Dokter dengan Cursor Pointer -->
                        <div class="card h-100 border-0 shadow-sm text-center p-4 rounded card-dokter" 
                             style="cursor: pointer;" 
                             onclick="getJadwal('<?php echo $d['kd_dokter']; ?>', '<?php echo htmlspecialchars($d['nm_dokter']); ?>')"
                             data-bs-toggle="modal" data-bs-target="#jadwalModal">
                            <img src="img/<?php echo htmlspecialchars($d['foto']); ?>" class="card-img-top rounded-circle mx-auto mb-3" style="width:150px; height:150px; object-fit:cover; border: 4px solid #51b749;" alt="">
                            <div class="card-body p-0">
                                <h5 class="text-primary mb-1"><?php echo htmlspecialchars($d['nm_dokter']); ?></h5>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($d['nm_sps'] ?? 'Dokter Umum'); ?></p>
                                <?php if(!empty($d['no_telp']) && $d['no_telp'] != '0'): ?>
                                <small class="text-dark d-block mb-2"><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($d['no_telp']); ?></small>
                                <?php endif; ?>
                                <span class="badge bg-light text-primary mt-2"><i class="far fa-clock me-1"></i> Lihat Jadwal</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center"><p class="text-muted">Dokter tidak ditemukan.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Jadwal -->
    <div class="modal fade" id="jadwalModal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="jadwalModalLabel">Jadwal Dokter</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    <!-- Konten Jadwal akan dimuat di sini -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>

<!-- Script AJAX untuk mengambil jadwal -->
<script>
    function getJadwal(kd_dokter, nama_dokter) {
        // Set Judul Modal
        document.getElementById('jadwalModalLabel').innerText = "Jadwal Praktek: " + nama_dokter;
        
        // Tampilkan Loading
        document.getElementById('modalBodyContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>`;

        // Fetch Data via AJAX
        fetch('get_jadwal?kd=' + kd_dokter)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modalBodyContent').innerHTML = data;
            })
            .catch(error => {
                document.getElementById('modalBodyContent').innerHTML = '<p class="text-danger text-center">Gagal memuat jadwal.</p>';
            });
    }
</script>