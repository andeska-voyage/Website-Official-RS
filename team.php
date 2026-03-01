<?php
 $pageTitle = "Tim Kami";
require_once 'config/database.php';

 $db = new Database();
// Query join untuk ambil nama dokter juga jika perlu, atau ambil dari tabel staff
// Di sini saya contohkan ambil dari tabel 'staff' yang sudah dibuat sebelumnya
 $db->query("SELECT * FROM staff ORDER BY category, name ASC");
 $staffs = $db->resultSet();

// Kelompokkan berdasarkan kategori
 $groupedStaff = [];
foreach($staffs as $s) {
    $groupedStaff[$s['category']][] = $s;
}

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Tim Kami</h1>
            <p class="text-white">Struktur Organisasi & Karyawan</p>
        </div>
    </div>

    <!-- Team Hierarchy Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container py-5">
            
            <?php foreach($groupedStaff as $category => $members): ?>
            
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius mb-4">
                            <?php echo htmlspecialchars($category); ?>
                        </h2>
                    </div>
                </div>

                <div class="row g-4 justify-content-center mb-5">
                    <?php foreach($members as $member): ?>
                    <div class="col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="0.1s">
                        <div class="team-item bg-white rounded shadow-sm border border-primary overflow-hidden">
                            <img src="img/<?php echo htmlspecialchars($member['image']); ?>" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="">
                            <div class="team-content text-center py-3">
                                <h5 class="text-primary mb-1"><?php echo htmlspecialchars($member['name']); ?></h5>
                                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($member['position']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
            <?php endforeach; ?>

        </div>
    </div>

<?php
require_once 'inc/footer.php';
?>