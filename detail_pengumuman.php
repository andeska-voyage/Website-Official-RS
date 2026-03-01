<?php
 $pageTitle = "Detail Pengumuman";
require_once 'config/database.php';
 $db = new Database();

// Decode ID dari Base64
 $encrypted_id = isset($_GET['data']) ? $_GET['data'] : '';
 $id = 0;
if (!empty($encrypted_id)) {
    $decoded = base64_decode($encrypted_id);
    if (is_numeric($decoded)) {
        $id = (int)$decoded;
    }
}

if($id > 0) {
    $db->query("SELECT * FROM announcements WHERE id = :id");
    $db->bind(':id', $id);
    $row = $db->single();
} else {
    $row = false;
}

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Detail Pengumuman</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item"><a href="pengumuman">Pengumuman</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Detail Content Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <!-- Kolom Kiri: Konten Utama -->
                <div class="col-lg-8">
                    <?php if($row): ?>
                    
                    <!-- Card Utama -->
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                        
                        <!-- Meta Info (Perbaikan Warna) -->
                        <div class="d-flex flex-wrap align-items-center mb-4 border-bottom pb-4">
                            <!-- Tanggal: Menggunakan bg-light agar terlihat jelas -->
                            <span class="badge bg-light text-primary px-3 py-2 me-2 mb-2" style="font-size: 0.9rem;">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                            </span>
                            
                            <!-- Label Kategori (Opsional) -->
                            <span class="badge bg-light text-secondary px-3 py-2 mb-2">
                                <i class="fas fa-bullhorn me-1"></i> Pengumuman Resmi
                            </span>
                        </div>

                        <!-- Judul -->
                        <h2 class="mb-4 text-dark" style="font-weight: 700; line-height: 1.4;">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </h2>

                        <!-- Garis Pembatas -->
                        <hr class="my-4">

                        <!-- Isi Konten -->
                        <div class="article-body">
                            <?php echo $row['content']; ?>
                        </div>

                        <!-- Lampiran File -->
                        <?php if(!empty($row['file_path'])): ?>
                        <div class="mt-5 p-4 bg-light rounded border border-primary border-start border-5">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-dark">File Lampiran Tersedia</h6>
                                    <small class="text-muted d-block mb-2">Klik tombol di bawah untuk mengunduh file.</small>
                                    <a href="uploads/<?php echo $row['file_path']; ?>" class="btn btn-primary btn-sm px-4 rounded-pill" download>
                                        <i class="fas fa-download me-2"></i>Download File
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Tombol Kembali -->
                        <div class="mt-5 pt-4 border-top">
                            <a href="pengumuman" class="btn btn-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>

                    <?php else: ?>
                    <div class="bg-white p-5 rounded shadow-sm text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Pengumuman Tidak Ditemukan</h4>
                        <p class="text-muted">Maaf, pengumuman yang Anda cari tidak tersedia.</p>
                        <a href="pengumuman" class="btn btn-primary mt-3">Lihat Pengumuman Lainnya</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Kanan -->
                <div class="col-lg-4">
                    <!-- Widget Info -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3 text-primary border-bottom border-primary border-2 d-inline-block pb-2">Informasi</h5>
                            <p class="text-muted small mb-0">
                                Pengumuman ini diterbitkan oleh pihak administrasi RSIA Restu Ibu. Untuk informasi lebih lanjut, silakan hubungi kontak kami.
                            </p>
                        </div>
                    </div>

                    <!-- Widget Kontak -->
                    <!-- <div class="card border-0 bg-primary text-white" style="border-radius: 15px;">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-headset fa-2x mb-3"></i>
                            <h5 class="text-white mb-3">Butuh Bantuan?</h5>
                            <p class="small mb-3">Jika ada pertanyaan seputar pengumuman ini, hubungi kami.</p>
                            <a href="tel:+6282391856461" class="btn btn-light text-primary rounded-pill px-4 fw-bold">
                                <i class="fas fa-phone-alt me-2"></i>Hubungi
                            </a>
                        </div>
                    </div> -->
                </div>

            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>