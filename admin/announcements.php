<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Pengumuman";
require_once '../config/database.php'; 
 $db = new Database();

// ================= PROSES SIMPAN =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title'] ?? ''; 
    $content = $_POST['content'] ?? ''; 
    
    // Ambil nama file lama
    $old_file = $_POST['old_file'] ?? '';
    $file_name = $old_file; // Default pakai lama

    if (!empty($_FILES['file']['name'])) {
        $file_name = time() . '_' . basename($_FILES['file']['name']);
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/' . $file_name)) {
            // Jika sukses, HAPUS file lama
            if (!empty($old_file) && file_exists('../uploads/' . $old_file)) {
                unlink('../uploads/' . $old_file);
            }
        }
    }

    if($id){
        $db->query("UPDATE announcements SET title=:t, content=:c, file_path=:f WHERE id=:id");
        $db->bind(':id', $id);
    } else {
        $db->query("INSERT INTO announcements (title, content, file_path) VALUES (:t, :c, :f)");
    }
    
    $db->bind(':t', $title);
    $db->bind(':c', $content);
    $db->bind(':f', $file_name);
    
    $db->execute(); 
    header('Location: announcements');
}

// ================= PROSES HAPUS =================
if(isset($_GET['delete'])){ 
    // 1. Ambil nama file
    $db->query("SELECT file_path FROM announcements WHERE id=:id");
    $db->bind(':id', $_GET['delete']);
    $row = $db->single();

    // 2. Hapus file fisik
    if ($row && !empty($row['file_path']) && file_exists('../uploads/' . $row['file_path'])) {
        unlink('../uploads/' . $row['file_path']);
    }

    // 3. Hapus Data
    $db->query("DELETE FROM announcements WHERE id=:id"); 
    $db->bind(':id', $_GET['delete']); 
    $db->execute(); 
    header('Location: announcements'); 
}

// Ambil Data
 $db->query("SELECT * FROM announcements ORDER BY created_at DESC"); 
 $rows = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary"><i class="bi bi-megaphone-fill me-2"></i>Buat Pengumuman</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" id="formPengumuman">
            <input type="hidden" name="id" id="editId">
            <!-- Hidden input untuk file lama -->
            <input type="hidden" name="old_file" id="editFile">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Pengumuman</label>
                <input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul Pengumuman" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Isi Pengumuman</label>
                <textarea name="content" id="editor" class="form-control" rows="8"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Lampiran File (Opsional)</label>
                    <input type="file" name="file" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin ganti file.</small>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2"><i class="bi bi-save me-2"></i>Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Daftar Pengumuman</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengumuman ini? File juga akan terhapus.')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SCRIPT CKEDITOR 4 -->
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>

<script>
    CKEDITOR.replace('editor', {
        height: 200,
        versionCheck: false,
        toolbar: [
            ['style', ['style']], 
            ['font', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview']]
        ]
    });

    function editData(data) {
        $('#editId').val(data.id);
        $('#editTitle').val(data.title);
        $('#editFile').val(data.file_path); // Masukkan nama file lama
        
        CKEDITOR.instances.editor.setData(data.content);
        
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
</script>

<?php require_once 'footer_admin.php'; ?>