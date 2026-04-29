<?php
 $pageTitle = "Dokter Praktek";
require_once 'config/database.php';
 $db = new Database();

// Query TANPA ALIAS (menggunakan nama tabel penuh)
 $sql = "SELECT dokter.kd_dokter, dokter.nm_dokter, dokter.jk, dokter.foto, dokter.no_telp, spesialis.nm_sps 
        FROM dokter 
        LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps 
        WHERE dokter.status = '1' 
        ORDER BY dokter.nm_dokter ASC";

 $db->query($sql);
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
                    <li class="breadcrumb-item text-white" aria-current="page">Dokter</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Dokter List -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <?php 
                if(!empty($dokters)):
                foreach($dokters as $d): 
                ?>
                <div class="col-lg-3 col-md-6 wow fadeInUp">
                    <div class="card h-100 border-0 shadow-sm text-center p-4 rounded">
                        <img src="img/<?php echo htmlspecialchars($d['foto']); ?>" class="card-img-top rounded-circle mx-auto" style="width:150px; height:150px; object-fit:cover; border: 4px solid #51b749;" alt="<?php echo htmlspecialchars($d['nm_dokter']); ?>">
                        <div class="card-body">
                            <h5 class="text-primary mb-1"><?php echo htmlspecialchars($d['nm_dokter']); ?></h5>
                            <p class="text-muted mb-1"><?php echo htmlspecialchars($d['nm_sps'] ?? 'Dokter Umum'); ?></p>
                            <?php if(!empty($d['no_telp']) && $d['no_telp'] != '0'): ?>
                            <small class="text-dark"><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($d['no_telp']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                endforeach; 
                else:
                ?>
                <div class="col-12 text-center"><p>Data dokter tidak ditemukan.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>