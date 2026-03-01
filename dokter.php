<?php
 $pageTitle = "Dokter Praktek"; // Set judul halaman
require_once 'config/database.php';

// Ambil data
 $db = new Database();
 $db->query("SELECT * FROM doctors ORDER BY name ASC");
 $doctors = $db->resultSet();

// Panggil Header
require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Dokter Spesialis</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page">Dokter</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Dokter List Start -->
    <div class="container-fluid team py-5">
        <div class="container py-5">
            <div class="row g-5 justify-content-center">
                
                <?php foreach($doctors as $doc): ?>
                <div class="col-sm-6 col-lg-4 col-xl-3 wow fadeIn" data-wow-delay="0.1s">
                    <div class="team-item border border-primary img-border-radius overflow-hidden">
                        <img src="img/<?php echo htmlspecialchars($doc['image']); ?>" class="img-fluid w-100" alt="<?php echo htmlspecialchars($doc['name']); ?>">
                        <div class="team-icon d-flex align-items-center justify-content-center">
                            <a class="share btn btn-primary btn-md-square text-white rounded-circle me-3" href="#"><i class="fas fa-share-alt"></i></a>
                            <a class="share-link btn btn-primary btn-md-square text-white rounded-circle" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                        <div class="team-content text-center py-3">
                            <h4 class="text-primary"><?php echo htmlspecialchars($doc['name']); ?></h4>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <!-- Dokter List End -->

<?php
// Panggil Footer
require_once 'inc/footer.php';
?>