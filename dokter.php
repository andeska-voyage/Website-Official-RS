<?php
 $pageTitle = "Dokter Praktek";
require_once 'config/database.php';
 $db = new Database();

// Logika Pencarian
 $search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Query Dokter
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

<style>
    /* CSS Khusus Halaman Dokter (Modern Card) */
    .doctor-card {
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    .doctor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .doctor-img-wrapper {
        height: 320px;
        overflow: hidden;
        background-color: #f8faff;
    }
    .doctor-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center; /* Agar wajah tidak terpotong */
        transition: transform 0.5s ease;
    }
    .doctor-card:hover .doctor-img-wrapper img {
        transform: scale(1.05);
    }
    .doctor-spec {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #3156F5;
        margin-bottom: 0.5rem;
    }
    .doctor-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    .btn-doctor {
        background-color: #3156F5;
        color: #fff;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 0;
        transition: 0.3s;
        border: none;
    }
    .btn-doctor:hover {
        background-color: #2648dd;
        color: #fff;
    }
</style>

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
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="search" class="form-control border-0" placeholder="Cari nama dokter atau spesialis..." value="<?php echo htmlspecialchars($search_query); ?>">
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
            <small class="text-muted">Menampilkan hasil pencarian untuk: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</small>
        </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <?php if(!empty($dokters)): ?>
                <?php foreach($dokters as $d): ?>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <!-- Kartu Dokter -->
                    <div class="card doctor-card shadow-sm h-100 text-center">
                        <!-- Foto Dokter -->
                        <div class="doctor-img-wrapper">
                            <img src="img/<?php echo htmlspecialchars($d['foto']); ?>" alt="<?php echo htmlspecialchars($d['nm_dokter']); ?>">
                        </div>
                        <!-- Detail Dokter -->
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="doctor-spec"><?php echo htmlspecialchars($d['nm_sps'] ?? 'Dokter Umum'); ?></div>
                            <div class="doctor-name"><?php echo htmlspecialchars($d['nm_dokter']); ?></div>
                            
                            <!-- Tombol Lihat Jadwal -->
                            <button onclick="getJadwal('<?php echo $d['kd_dokter']; ?>', '<?php echo htmlspecialchars($d['nm_dokter']); ?>')" 
                                    class="btn btn-doctor mt-auto w-100" 
                                    data-bs-toggle="modal" data-bs-target="#jadwalModal">
                                Jadwal Praktek
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <h5>Dokter tidak ditemukan</h5>
                    <p class="text-muted">Coba kata kunci lain.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Jadwal (Tetap dipertahankan) -->
<div class="modal fade" id="jadwalModal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="jadwalModalLabel">Jadwal Dokter</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
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
        document.getElementById('jadwalModalLabel').innerText = "Jadwal Praktek: " + nama_dokter;
        
        document.getElementById('modalBodyContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>`;

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