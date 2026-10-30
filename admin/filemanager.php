<?php
require_once __DIR__ . '/auth.php';

 $pageTitle = "Manajer Berkas (Uploads)";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; 
 $db = new Database();

// ==========================================
// 1. LOGIKA BULK DELETE (Hapus Massal)
// ==========================================
if(isset($_POST['bulk_delete'])){
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Sesi tidak valid!'); history.back();</script>");
    }

    if(!empty($_POST['selected_files'])){
        $deleted_count = 0;
        foreach($_POST['selected_files'] as $filename){
            $filepath = '../uploads/' . basename($filename);
            
            // Hapus file fisik
            if(file_exists($filepath)){
                if(unlink($filepath)){
                    $deleted_count++;
                }
            }
        }
        $msg = "Berhasil menghapus <strong>$deleted_count</strong> file terpilih.";
    } else {
        $err = "Tidak ada file yang dipilih.";
    }
}

// ==========================================
// 2. LOGIKA HAPUS SINGLE (Satu File)
// ==========================================
if(isset($_GET['delete'])){
    // Validasi CSRF
    if (!isset($_GET['csrf_token']) || !validateCSRFToken($_GET['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $filename = basename($_GET['delete']);
    $filepath = '../uploads/' . $filename;

    if(file_exists($filepath)){
        if(unlink($filepath)){
            $msg = "File <strong>$filename</strong> berhasil dihapus.";
        } else {
            $err = "Gagal menghapus file. Cek permission folder.";
        }
    } else {
        $err = "File tidak ditemukan.";
    }
}

// ==========================================
// 3. LOGIKA DETEKSI FILE TERPAKAI
// ==========================================

 $files_in_db = [];

// A. Cek tabel POSTS
 $db->query("SELECT content FROM posts");
 $posts = $db->resultSet();
foreach($posts as $p) {
    if(preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $p['content'], $matches)) {
        if(isset($matches[1])) {
            foreach($matches[1] as $m) { $files_in_db[] = $m; }
        }
    }
}

// B. Cek tabel ANNOUNCEMENTS
 $db->query("SELECT file_path FROM announcements WHERE file_path IS NOT NULL AND file_path != ''");
 $anns = $db->resultSet();
foreach($anns as $a) { $files_in_db[] = basename($a['file_path']); }
// B. Cek tabel ANNOUNCEMENTS (File Lampiran + Konten CKEditor)
 $db->query("SELECT file_path, content FROM announcements");
 $anns = $db->resultSet();
foreach($anns as $a) {
    // 1. Cek File Lampiran biasa
    if (!empty($a['file_path'])) {
        $files_in_db[] = basename($a['file_path']);
    }
    // 2. Cek Gambar di dalam CKEditor
    if (!empty($a['content'])) {
        if(preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $a['content'], $matches)) {
            if(isset($matches[1])) {
                foreach($matches[1] as $m) { $files_in_db[] = $m; }
            }
        }
    }
}

// C. Cek tabel DOCUMENTS
 $db->query("SELECT file_path FROM documents WHERE file_path IS NOT NULL AND file_path != ''");
 $docs = $db->resultSet();
foreach($docs as $d) { $files_in_db[] = basename($d['file_path']); }


// ==========================================
// 4. LOGIKA PAGINATION
// ==========================================
 $uploadDir = '../uploads/';
 $all_files = [];

// Ambil semua file fisik
if (is_dir($uploadDir)) {
    foreach (glob($uploadDir . "*") as $file) {
        if (basename($file) !== '.' && basename($file) !== '..') {
            $all_files[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'time' => filemtime($file),
                'type' => pathinfo($file, PATHINFO_EXTENSION)
            ];
        }
    }
    // Urutkan dari yang terbaru
    usort($all_files, function($a, $b) {
        return $b['time'] - $a['time'];
    });
}

// Konfigurasi Pagination
 $limit = 10; // Jumlah file per halaman
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $offset = ($page > 1) ? ($page * $limit) - $limit : 0;
 $totalFiles = count($all_files);
 $totalPages = ceil($totalFiles / $limit);

// Slice array sesuai halaman
 $files = array_slice($all_files, $offset, $limit);

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 text-primary">
                <i class="bi bi-folder-fill me-2"></i>Manajer Berkas Uploads
            </h5>
            <small class="text-muted">
                Total File: <strong><?php echo $totalFiles; ?></strong> | 
                Sistem mendeteksi file yang tidak terpakai (Orphan Files).
            </small>
        </div>
        <div>
            <span class="badge bg-success">Terpakai</span>
            <span class="badge bg-danger">Tidak Terpakai</span>
        </div>
    </div>
    <div class="card-body p-4">
        
        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if(isset($err)): ?>
            <div class="alert alert-danger"><?php echo $err; ?></div>
        <?php endif; ?>

        <!-- FORM BULK DELETE -->
        <form method="POST" action="" id="fileManagerForm">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <!-- Tombol Aksi Massal -->
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="checkAll" onchange="toggleAllCheckboxes(this)">
                    <label class="form-check-label fw-bold" for="checkAll">Pilih Semua di Halaman Ini</label>
                </div>
                <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus file yang dipilih? Beberapa mungkin sedang digunakan di artikel.')">
                    <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="40">#</th>
                            <th width="50"><input type="checkbox" disabled class="form-check-input"></th>
                            <th>Pratinjau / Tipe</th>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($files)): ?>
                            <?php 
                            $no = $offset + 1;
                            foreach($files as $f): 
                            
                            // CEK STATUS APAKAH FILE INI ADA DI DATABASE
                            $is_used = in_array($f['name'], $files_in_db);
                            ?>
                            <tr class="<?php echo (!$is_used) ? 'table-danger bg-opacity-10' : ''; ?>">
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <!-- CHECKBOX PER BARIS -->
                                    <input class="form-check-input file-checkbox" type="checkbox" name="selected_files[]" value="<?php echo $f['name']; ?>">
                                </td>
                                <td width="80">
                                    <?php if(in_array(strtolower($f['type']), ['jpg','jpeg','png','gif'])): ?>
                                        <a href="../uploads/<?php echo $f['name']; ?>" target="_blank">
                                            <img src="../uploads/<?php echo $f['name']; ?>" class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">
                                        </a>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <i class="bi bi-file-earmark-text fa-2x text-secondary"></i>
                                            <div class="small text-muted" style="font-size:0.7rem; font-weight:bold;">
                                                <?php echo strtoupper($f['type']); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?php echo $f['name']; ?></code>
                                </td>
                                <td><?php echo round($f['size'] / 1024, 2); ?> KB</td>
                                
                                <!-- TAMPILKAN STATUS BADGE -->
                                <td>
                                    <?php if($is_used): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Terpakai
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Tidak Terpakai
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- KOLOM AKSI (LOGIKA PERBAIKAN) -->
                                <td>
                                    <a href="../uploads/<?php echo $f['name']; ?>" target="_blank" class="btn btn-sm btn-info" title="Lihat/Buka">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if($is_used): ?>
                                        <!-- JIKA TERPAKAI: TAMPILKAN TOMBOL TERKUNCI (GEMBOK) ABU-ABU -->
                                        <button class="btn btn-sm btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed;" title="File sedang digunakan, tombol dikunci.">
                                            <i class="bi bi-lock-fill"></i>
                                        </button>
                                    <?php else: ?>
                                        <!-- JIKA TIDAK TERPAKAI: TAMPILKAN TOMBOL HAPUS MERAH -->
                                        <a href="?delete=<?php echo $f['name']; ?>&csrf_token=<?php echo generateCSRFToken(); ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Hapus file ini?');"
                                           title="Hapus Permanen">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada file di halaman ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
        
        <!-- PAGINATION -->
        <?php if($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-sm">
                    <!-- PREV -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>">&laquo;</a>
                    </li>
                    
                    <!-- NUMBERS -->
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- NEXT -->
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="posts" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Berita
            </a>
        </div>
    </div>
</div>

<script>
// SCRIPT UNTUK CHECKBOX "PILIH SEMUA"
function toggleAllChecks(source) {
    var checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });
}

// Script untuk meluruskan "Check All" jika user uncheck salah satu manual
document.querySelectorAll('.file-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        var allChecked = true;
        document.querySelectorAll('.file-checkbox').forEach(function(c) {
            if(!c.checked) allChecked = false;
        });
        document.getElementById('checkAll').checked = allChecked;
    });
});
</script>

<?php require_once 'footer_admin.php'; ?>