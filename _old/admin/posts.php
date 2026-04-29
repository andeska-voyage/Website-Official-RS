<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Berita / Artikel";
require_once '../config/database.php'; 
 $db = new Database();

// ================= PROSES SIMPAN (INSERT/UPDATE) =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title']; 
    $content = $_POST['content']; 
    $category_id = $_POST['category_id']; 
    
    // Ambil nama gambar lama dari input hidden
    $old_image = $_POST['old_image'] ?? 'blog-1.jpg';
    $img_name = $old_image; // Default pakai yang lama

    // Logika Upload Gambar Baru
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . '_' . basename($_FILES['image']['name']);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../img/' . $img_name)) {
            // Jika upload sukses, HAPUS gambar lama (kecuali default)
            if ($old_image != 'blog-1.jpg' && file_exists('../img/' . $old_image)) {
                unlink('../img/' . $old_image);
            }
        }
    }

    // Buat Slug
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');

    if($id){
        // Update
        $db->query("UPDATE posts SET title=:t, slug=:slug, content=:c, category_id=:cat, image=:i WHERE id=:id");
        $db->bind(':id', $id);
    } else {
        // Insert
        $db->query("INSERT INTO posts (title, slug, content, category_id, image) VALUES (:t, :slug, :c, :cat, :i)");
    }
    
    $db->bind(':t', $title);
    $db->bind(':slug', $slug);
    $db->bind(':c', $content);
    $db->bind(':cat', $category_id);
    $db->bind(':i', $img_name);
    
    $db->execute(); 
    header('Location: posts');
}

// ================= PROSES HAPUS =================
if(isset($_GET['delete'])){ 
    // 1. Ambil nama gambar sebelum hapus data
    $db->query("SELECT image FROM posts WHERE id=:id");
    $db->bind(':id', $_GET['delete']);
    $row = $db->single();

    // 2. Hapus file fisik
    if ($row && $row['image'] != 'blog-1.jpg' && file_exists('../img/' . $row['image'])) {
        unlink('../img/' . $row['image']);
    }

    // 3. Hapus data di DB
    $db->query("DELETE FROM posts WHERE id=:id"); 
    $db->bind(':id', $_GET['delete']); 
    $db->execute(); 
    header('Location: posts'); 
}

// Ambil Data
 $db->query("SELECT posts.*, categories.name as cname FROM posts LEFT JOIN categories ON posts.category_id = categories.id ORDER BY posts.created_at DESC"); 
 $rows = $db->resultSet();
 $db->query("SELECT * FROM categories"); 
 $cats = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary"><i class="bi bi-newspaper me-2"></i>Tulis Berita Baru</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" id="formPost">
            <input type="hidden" name="id" id="editId">
            <!-- Input hidden untuk menyimpan nama gambar lama -->
            <input type="hidden" name="old_image" id="editImage">
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Judul Berita</label>
                    <input type="text" name="title" id="editTitle" class="form-control" placeholder="Masukkan judul..." required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="category_id" id="editCat" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach($cats as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Isi Konten</label>
                <textarea name="content" id="editor" class="form-control" rows="10"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gambar Utama</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2"><i class="bi bi-save me-2"></i>Publikasikan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Daftar Berita</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
                    <td><span class="badge bg-secondary"><?php echo $r['cname']; ?></span></td>
                    <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus berita ini? Gambar juga akan terhapus.')"><i class="bi bi-trash"></i></a>
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
        height: 300,
        versionCheck: false,
        toolbar: [
            ['style', ['style']], 
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    function editData(data) {
        $('#editId').val(data.id);
        $('#editTitle').val(data.title);
        $('#editCat').val(data.category_id);
        $('#editImage').val(data.image); // Masukkan nama gambar lama ke hidden input
        
        CKEDITOR.instances.editor.setData(data.content);
        
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
</script>

<?php require_once 'footer_admin.php'; ?>