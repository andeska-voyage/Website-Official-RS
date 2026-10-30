<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Berita / Artikel";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; 
 $db = new Database();

// ======================================================
// FUNGSI KOMPRESI GAMBAR
// ======================================================
function compressImage($source, $destination, $quality) {
    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);
    else 
        return false; // Tidak support format lain

    // Simpan dengan kualitas tertentu
    // Untuk PNG, quality adalah level compression (0-9)
    if ($info['mime'] == 'image/png') {
        imagepng($image, $destination, 8); // Compression level 8 (0-9)
    } else {
        imagejpeg($image, $destination, $quality); // Quality 75
    }
    
    imagedestroy($image);
    return true;
}

// Proses Simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title']; 
    $content = $_POST['content']; 
    $category_id = $_POST['category_id']; 
    $img = $_FILES['image']['name'] ?? '';

    // Slug Generator
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');

    if($id){
        $q = "UPDATE posts SET title=:t, slug=:slug, content=:c, category_id=:cat"; 
        $p = [':t'=>$title, ':slug'=>$slug, ':c'=>$content, ':cat'=>$category_id, ':id'=>$id];
        if($img){ 
            // Hapus gambar lama jika bukan default
            $db->query("SELECT image FROM posts WHERE id=:id"); $db->bind(':id',$id); $old = $db->single();
            if ($old && $old['image'] != 'blog-1.jpg' && file_exists('../img/' . $old['image'])) unlink('../img/' . $old['image']);

            // Upload & Kompres Baru
            $img = time() . '_' . basename($_FILES['image']['name']);
            $target = '../img/' . $img;
            $tmp = $_FILES['image']['tmp_name'];
            
            // Jalankan Kompresi (Quality 75)
            compressImage($tmp, $target, 75);
            
            $q.=", image=:i"; $p[':i']=$img; 
        }
        $q.=" WHERE id=:id"; 
        $db->query($q); 
        foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='blog-1.jpg'; 
        else {
            $img = time() . '_' . basename($_FILES['image']['name']);
            $target = '../img/' . $img;
            $tmp = $_FILES['image']['tmp_name'];
            compressImage($tmp, $target, 75);
        }
        
        $db->query("INSERT INTO posts (title, slug, content, category_id, image) VALUES (:t, :slug, :c, :cat, :i)");
        $db->bind(':t',$title); $db->bind(':slug',$slug); $db->bind(':c',$content); $db->bind(':cat',$category_id); $db->bind(':i',$img);
    } 
    $db->execute(); 
    header('Location: posts');
}

// Hapus
if(isset($_GET['delete'])){ 
    $db->query("SELECT image FROM posts WHERE id=:id"); $db->bind(':id',$_GET['delete']); $row = $db->single();
    if ($row && $row['image'] != 'blog-1.jpg' && file_exists('../img/' . $row['image'])) unlink('../img/' . $row['image']);
    $db->query("DELETE FROM posts WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: posts'); 
}

// Ambil Data
 $db->query("SELECT posts.*, categories.name as cname FROM posts LEFT JOIN categories ON posts.category_id = categories.id ORDER BY posts.created_at DESC"); 
 $rows = $db->resultSet();
 $db->query("SELECT * FROM categories"); $cats = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-newspaper me-2"></i>Tulis Berita Baru</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" id="formPost">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Judul Berita</label>
                    <input type="text" name="title" id="editTitle" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="category_id" id="editCat" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach($cats as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Isi Konten</label>
                <textarea name="content" id="editor" class="form-control" rows="10"></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Gambar Utama</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Akan otomatis dikompres (Max lebar 1200px, Kualitas 75%).</small>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2"><i class="bi bi-save me-2"></i>Publikasikan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Daftar Berita</h5></div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="bg-light"><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th width="150">Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
                    <td><span class="badge bg-secondary"><?php echo $r['cname']; ?></span></td>
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

<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', { height: 300, versionCheck: false });
    function editData(data) {
        $('#editId').val(data.id); $('#editTitle').val(data.title); $('#editCat').val(data.category_id);
        CKEDITOR.instances.editor.setData(data.content);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
</script>
<?php require_once 'footer_admin.php'; ?>