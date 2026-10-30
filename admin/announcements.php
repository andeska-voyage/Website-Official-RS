<?php
require_once __DIR__ . '/auth.php';
 $pageTitle = "Pengumuman";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

// ==========================================
// PROSES SIMPAN / UPDATE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title']; 
    $content = $_POST['content']; 
    $img = $_FILES['file']['name'] ?? '';

    if($id){
        // ==========================================
        // PROSES EDIT (UPDATE)
        // ==========================================
        $db->query("SELECT file_path, content FROM announcements WHERE id=:id");
        $db->bind(':id', $id);
        $old_data = $db->single();

        // 1. Proses File Lampiran Utama (PDF/Doc)
        $file_to_save = $old_data['file_path'] ?? '';
        if($img){ 
            // Jika ada file baru diupload, hapus file lama
            if (!empty($old_data['file_path']) && file_exists('../uploads/' . $old_data['file_path'])) {
                unlink('../uploads/' . $old_data['file_path']);
            }
            move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img);
            $file_to_save = $img;
        }

        // 2. Proses Gambar di dalam CKEditor (Hapus gambar yang dikurangi/dihapus dari konten)
        $old_content = $old_data['content'] ?? '';
        if (!empty($old_content)) {
            // Cari semua gambar di konten LAMA
            preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $old_content, $old_matches);
            $old_images = $old_matches[1] ?? [];
            
            // Cari semua gambar di konten BARU
            preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $content, $new_matches);
            $new_images = $new_matches[1] ?? [];

            // Bandingkan, cari gambar yang ada di LAMA tapi tidak ada di BARU
            $images_to_delete = array_diff($old_images, $new_images);
            foreach ($images_to_delete as $filename) {
                $path = '../uploads/' . $filename;
                if (file_exists($path)) {
                    unlink($path); // Hapus file fisiknya
                }
            }
        }

        // 3. Update Database
        $db->query("UPDATE announcements SET title=:t, content=:c, file_path=:fp WHERE id=:id");
        $db->bind(':t', $title);
        $db->bind(':c', $content);
        $db->bind(':fp', $file_to_save);
        $db->bind(':id', $id);
        
    } else {
        // ==========================================
        // PROSES TAMBAH BARU (INSERT)
        // ==========================================
        $file_to_save = '';
        if($img){
            move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img);
            $file_to_save = $img;
        }
        $db->query("INSERT INTO announcements (title, content, file_path) VALUES (:t, :c, :i)");
        $db->bind(':t', $title);
        $db->bind(':c', $content);
        $db->bind(':i', $file_to_save);
    } 
    
    $db->execute(); 
    // HAPUS CACHE PENGUMUMAN HOMEPAGE
    @unlink(__DIR__ . '/../cache/' . md5('homepage_announcements') . '.cache');
    header('Location: announcements');
}

// ==========================================
// PROSES HAPUS
// ==========================================
if(isset($_GET['delete'])){ 
    // 1. Ambil data lampiran dan isi konten dari database
    $db->query("SELECT file_path, content FROM announcements WHERE id=:id"); 
    $db->bind(':id',$_GET['delete']); 
    $row = $db->single();
    
    if ($row) {
        // A. Hapus File Lampiran Utama (jika ada)
        if (!empty($row['file_path']) && file_exists('../uploads/' . $row['file_path'])) {
            unlink('../uploads/' . $row['file_path']);
        }
        
        // B. Hapus Gambar di dalam Konten CKEditor (Folder uploads)
        if (!empty($row['content'])) {
            preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $row['content'], $matches);
            if (isset($matches[1]) && !empty($matches[1])) {
                foreach ($matches[1] as $filename) {
                    $file_path = '../uploads/' . $filename;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }
    }
    
    // 2. Hapus data dari database
    $db->query("DELETE FROM announcements WHERE id=:id"); 
    $db->bind(':id',$_GET['delete']); 
    $db->execute(); 
    // HAPUS CACHE PENGUMUMAN HOMEPAGE
    @unlink(__DIR__ . '/../cache/' . md5('homepage_announcements') . '.cache');
    header('Location: announcements'); 
}

 $db->query("SELECT * FROM announcements ORDER BY created_at DESC"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-megaphone-fill me-2"></i>Buat Pengumuman</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            <div class="mb-3"><label>Judul</label><input type="text" name="title" id="editTitle" class="form-control" required></div>
            <div class="mb-3"><label>Isi</label><textarea name="content" id="editor" class="form-control"></textarea></div>
            <div class="row mb-3">
                <div class="col-md-6"><label>File (Opsional)</label><input type="file" name="file" class="form-control"></div>
                <div class="col-md-6 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Daftar Pengumuman</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.20.0/full-all/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', { 
        height: 200, 
        versionCheck: false,
        
        // Aktifkan plugin dan arahkan upload ke file upload.php
        extraPlugins: 'uploadimage',
        imageUploadUrl: 'upload.php',
        filebrowserImageUploadUrl: 'upload.php',
        
        // Cegah CKEditor menyimpan width/height/inline style yang bikin gambar ketarik
        disallowedContent: 'img[width,height,style]',
        disableObjectResizing: true
    }); 
    
    function editData(d){
        $('#editId').val(d.id);
        $('#editTitle').val(d.title);
        CKEDITOR.instances.editor.setData(d.content);
        window.scrollTo(0,0);
    }
</script>
<?php require_once 'footer_admin.php'; ?>